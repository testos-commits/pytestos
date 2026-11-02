<?php
namespace App\Traits;
use App\Models\Audition;
use App\Services\AuditionService;
use Illuminate\Support\Facades\Log;
trait FiltersProfilesByAuditionCriteria
{

    public function filterProfilesByAuditionCriteria(Audition $audition): static
    {
        $as = new AuditionService();
     
        $this->subscribedProfiles = $this->subscribedProfiles
            ->filter(function ($profile) use ($audition, $as) {
                return $as->profileElligibleForAudition($profile, $audition);
            });

        return $this;
    }
}
