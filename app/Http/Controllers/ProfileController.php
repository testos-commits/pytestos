<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\MediaAssetRequest;
use App\Http\Requests\PaymentDetailRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Models\Profile;
use App\Traits\RequiresSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
    use RequiresSubscription;
    private $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }
    public function index()
    {
        Log::channel('debug')->info('Fetching user info');
        $user = $this->profileService->getUser();
        return response()->json($user);
    }

    public function create(ProfileRequest $request)
    {

        $user = User::with('profiles')
            ->find(auth()->user()->id);

        if (!$user->canCreateProfile()) {
            return response()->json(['message' => 'Profile limit per account reached'], 422);
        }


        $valid = $request->validated();
        $valid['country_id'] = $user->profiles->first()->country_id;
        $user = $this->profileService->create($valid);
        return response()->json($user);
    }

    public function update(ProfileRequest $request)
    {
        $user = $this->profileService->updateInfo($request->validated());
        return response()->json($user);
    }

    public function saveMediaAssetURL(MediaAssetRequest $request)
    {
        if (
            $request->validated()['type'] == 'profile_image'
            && !User::with("profile.profileImages")
                ->find(auth()->user()->id)
                ->profile
                ->canUploadPhotos()
        ) {
            return response()->json([
                'message' => 'You have reached the maximum number of photos allowed per profile',
            ]);
        }

        $user = $this->profileService->saveMediaAssetURL($request->validated());

        return response()->json($user);
    }

    public function deleteProfileImage($id)
    {

        $user = $this->profileService->deleteProfileImage($id);

        if ($user === false) {
            return response()->json(['message' => 'Error deleting profile image'], 500);
        }
        return response()->json($user);
    }

    public function switchLanguage($language)
    {
        $success = $this->profileService->switchLanguage($language);

        if (!$success) {
            return response()->json(['message' => 'Language not supported'], 422);
        }

        return response()->json([
            'message' => 'Language switched successfully',
            'language' => $language,
        ]);
    }

    public function switchProfile(Profile $profile)
    {
        $user = User::with(['profiles' => function ($query) {
            $query->orderBy('created_at', 'asc')->limit(1);
        }])->find(auth()->user()->id);

        if (!$user->canSwitchProfiles()) {
            return response()->json(
                $this->profileService
                    ->switchProfile(
                        $user->profiles->first()
                    )
            );
        }

        return response()->json(
            $this->profileService->switchProfile($profile)
        );
    }

    public function addPreviousCommercial(Request $request)
    {
        $request->validate([
            'commercial' => 'required',
        ]);

        $user = $this->profileService->addPreviousCommercials($request->get('commercial'));
        return response()->json($user);
    }

    public function createPaymentDetails(PaymentDetailRequest $request)
    {
        ProfileService::createPaymentDetails($request->validated());
        return response()->json(['message' => 'Payment details created successfully']);
    }

    public function deleteProfile()
    {
        return response()->json($this->profileService->delete());
    }

    public function deleteAccount()
    {
        return response()->json($this->profileService->deleteAccount());
    }
}
