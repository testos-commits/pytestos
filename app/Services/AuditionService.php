<?php

namespace App\Services;

use App\Classes\MessageHandler;
use App\Models\Audition;
use App\Services\FiltersService;
use App\Http\Resources\AuditionResource;
use App\Models\Profile;
use Carbon\Carbon;
use App\Traits\Filterable;

class AuditionService extends MessageHandler
{
    use Filterable;
    public int $limit = 10;
    public $filters;
    private $message;

    public function __construct()
    {
        parent::__construct();
    }

    public function getMessage()
    {
        return $this->message;
    }
    public function create($data): void
    {
        Audition::create($data);
    }

    public function getUserCountryAuditions($page = 1)
    {
        $userProfile = auth()->user()->profile;

        $auditions = Audition::filterCountry($userProfile->country_id)
            // ->projectActive()
            ->withUserApplications($userProfile->id)
            ->with(['countries', 'project'])
            ->orderByDesc('id')
            ->paginate($this->limit);

        $nextPage = $page * $this->limit < $auditions->total() ? $page + 1 : null;

        return [
            'offers' => AuditionResource::collection($auditions),
            'nextPage' => $nextPage
        ];
    }

    public function getForUser($page = 1)
    {
        $userProfile = auth()->user()->profile;

        $allAuditions = Audition::filterCountry($userProfile->country_id)
            ->projectActive()
            ->select('auditions.min_age', 'auditions.max_age', 'auditions.gender', 'auditions.meta', 'auditions.id')
            ->get();

        $forYou = array_unique($this->filterForYou($allAuditions, $userProfile)->toArray());

        $total = count($forYou);

        $nextPage = $page * $this->limit < $total ? $page + 1 : null;

        $forYouAuditions = Audition::whereIn('id', $forYou)
            ->withUserApplications($userProfile->id)
            ->with(['countries', 'project'])
            ->orderByDesc('id')
            ->paginate($this->limit);

        return [
            'offers' => AuditionResource::collection($forYouAuditions),
            'nextPage' => $nextPage
        ];
    }

    public function filterForYou($auditions, $userProfile)
    {
        $profileMeta = $userProfile->meta ? $userProfile->meta : [];

        $age = $userProfile->date_of_birth ? Carbon::parse($userProfile->date_of_birth)->age : null;
        $gender = $userProfile->gender ? $userProfile->gender : null;

        return $auditions->filter(function ($audition) use ($profileMeta, $age, $gender) {

            // Filter age
            if (!is_null($age)) {
                if ($audition->min_age && $age < $audition->min_age) {
                    return false;
                }

                if ($audition->max_age && $age > $audition->max_age) {
                    return false;
                }
            }

            // Filter gender
            if (!is_null($gender) && !is_null($audition->gender)) {
                if ($audition->gender !== $gender) {
                    return false;
                }
            }

            // Filter meta if they're not set, no further filtering is needed
            if (is_null($audition->meta) || count($audition->meta) === 0) {
                return true;
            }

            //if meta is set, but user has no meta, return false
            if (!isset($profileMeta) || count($profileMeta) === 0) {
                return false;
            }

            $returnVal = true;

            foreach ($audition->meta as $key => $values) {
                if (!isset($profileMeta[$key]) || count($profileMeta[$key]) === 0) {
                    $returnVal = false;
                    continue;
                }

                foreach ($values as $value) {
                    if (!in_array($value, $profileMeta[$key])) {
                        $returnVal = false;
                        continue;
                    }
                }
            }

            return $returnVal;
        })
            ->pluck('id');
    }

    public function getById($id)
    {
        $userProfile = auth()->user()->profile;
        $audition = Audition::withUserApplications($userProfile->id)->find($id);

        if (!$audition) {
            return false;
        }
        return new AuditionResource($audition);
    }

    public static function getForPriviledgedUser($id, $searchQuery = '', $filters = [])
    {
        return Audition::with(['project' => function ($query) {
            return $query->withTrashed();
        }])->withAllApplications($searchQuery, $filters)->find($id);
    }

    public static function getForPriviledgedUserByAnswerId($auditionId, $answerId, $searchQuery = '', $filters = [])
    {
        return  Audition::withAllApplicationsExtended(function ($query) use ($answerId, $searchQuery, $filters) {
            $query->whereHas('profileMessages', function ($query) use ($answerId) {
                $query->where('audition_messages_option_id', $answerId);
            });

            if (!empty($searchQuery)) {
                $query->where('first_name', 'LIKE', "%$searchQuery%");
            }

            foreach ($filters as $key => $value) {
                if (is_array($value) && count($value) > 0) {
                    if (strpos($key, 'meta_') !== false) {
                        $key = str_replace('meta_', '', $key);
                        $query->whereJsonContains("meta->{$key}", $value);
                    }
                } else {
                    if ($key == 'gender' && $value !== null && $value !== '') {
                        $query->where('gender', $value);
                    }

                    if ($key === 'min_age' && $value !== null && $value !== '') {
                        $query->where('date_of_birth', '<=', now()->subYears($value));
                    }

                    if ($key === 'max_age' && $value !== null && $value !== '') {
                        $query->where('date_of_birth', '>=', now()->subYears($value));
                    }
                }
            }
        })->find($auditionId);
    }

    public function apply($auditionId)
    {
        $audition = Audition::findOrFail($auditionId);
        $canApply = auth()->user()->profile->canApply();

        if ($canApply['status'] == false) {
            $this->setMessages($canApply['message']);
            return false;
        }

        // Keep this before the isExpired check so the user gets the correct message, because on termination we also get isExpired true
        if ($audition->isTerminated()) {
            $this->setMessages('AUDITION_NOT_ACTIVE');
            return false;
        }

        if ($audition->isExpired()) {
            $this->setMessages('AUDITION_EXPIRED');
            return false;
        }

        if (auth()->user()->profile->auditionApplications()->where('audition_id', $auditionId)->exists()) {
            $this->setMessages('ALREADY_APPLIED_TO_AUDITION');
            return false;
        }


        $audition->applications()
            ->create([
                'user_profile_id' => auth()->user()->profile->id
            ]);

        $this->setMessages('APPLIED_SUCCESSFULLY');
        return true;
    }

    public function getProfileApplications($page = 1)
    {
        $userProfile = auth()->user()->profile;

        $applications = $userProfile->applied()
            ->withUserApplications($userProfile->id)
            ->orderByDesc('id')
            ->paginate($this->limit);

        $nextPage = $page * $this->limit < $applications->total() ? $page + 1 : null;

        return [
            'offers' => AuditionResource::collection($applications),
            'nextPage' => $nextPage
        ];
    }

    public function getProfileShortlists($page = 0)
    {
        $userProfile = auth()->user()->profile;

        $shortlisted = $userProfile->shortlisted()
            ->withUserApplications($userProfile->id)
            ->orderByDesc('id')
            ->limit($this->limit)
            ->paginate($this->limit);

        $nextPage = $page * $this->limit < $shortlisted->total() ? $page + 1 : null;

        return [
            'offers' => AuditionResource::collection($shortlisted),
            'nextPage' => $nextPage
        ];
    }

    public function getProfileAccepted($page = 0)
    {
        $userProfile = auth()->user()->profile;

        $accepted = $userProfile->accepted()
            ->withUserApplications($userProfile->id)
            ->orderByDesc('id')
            ->limit($this->limit)
            ->paginate($this->limit);

        $nextPage = $page * $this->limit < $accepted->total() ? $page + 1 : null;

        return [
            'offers' => AuditionResource::collection($accepted),
            'nextPage' => $nextPage
        ];
    }



    public function delete($id)
    {
        $audition = Audition::find($id);
        $audition->delete();
    }

    public static function getProjectAuditions($projectId, $searchQuery = '')
    {
        $query = Audition::where('project_id', $projectId)->orderBy('created_at', 'desc');

        if ($searchQuery) {
            $query->where('title', 'like', '%' . $searchQuery . '%');
        }

        return $query->get();
    }

    public  static function getAuditionWithMessages($id, $searchQuery = '')
    {
        return Audition::with([
            'messages' => function ($subQuery) use ($searchQuery) {
                if (!empty($searchQuery)) {
                    return $subQuery->where('message', 'like', '%' . $searchQuery . '%');
                }
            },
            'messages.options'
        ])->find($id);
    }

    public function profileElligibleForAudition(Profile $profile, Audition $audition): bool
    {
        return $this->profilePassesAuditionAgeFilter($profile, $audition)
            && $this->profilePassesAuditionGenderFilter($profile, $audition)
            && $this->profilePassesAuditionMetaFilter($profile, $audition);
    }
    public function profilePassesAuditionAgeFilter(Profile $profile, Audition $audition): bool
    {
        $profileAge = $profile->date_of_birth ?
            Carbon::parse($profile->date_of_birth)->age
            : null;

        $auditionHasAgeFilters = $audition->min_age || $audition->max_age;

        if ($auditionHasAgeFilters) {
            if (is_null($profileAge)) {
                return false;
            }

            if ($audition->min_age && $profileAge < $audition->min_age) {
                return false;
            }

            if ($audition->max_age && $profileAge > $audition->max_age) {
                return false;
            }
        }

        return true;
    }

    public function profilePassesAuditionGenderFilter(Profile $profile, Audition $audition): bool
    {
        $profileGender = $profile->gender ? $profile->gender : null;
        $auditionGender = $audition->gender ? $audition->gender : null;

        if (is_null($auditionGender)) {
            return true;
        }

        if (is_null($profileGender)) {
            return false;
        }

        if ($profileGender !== $auditionGender) {
            return false;
        }

        return true;
    }

    public function profilePassesAuditionMetaFilter(Profile $profile, Audition $audition): bool
    {
        if (is_null($audition->meta) || count($audition->meta) === 0) {
            return true;
        }

        if (is_null($profile->meta) || count($profile->meta) === 0) {
            return false;
        }

        foreach ($audition->meta as $key => $values) {
            if (!isset($profileMeta[$key]) || count($profile->meta[$key]) === 0) {
                $returnVal = false;
                continue;
            }

            foreach ($values as $value) {
                if (!in_array($value, $profile->meta[$key])) {
                    $returnVal = false;
                    continue;
                }
            }
        }

        return true;
    }
}
