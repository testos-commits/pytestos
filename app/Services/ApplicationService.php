<?php

namespace App\Services;

use App\Models\AuditionApplication;
use App\Services\MessageService;
use App\Services\PaymentsService;
use App\Services\ReferralsService;
use App\Models\Audition;


class ApplicationService
{

    public static $statusConfiguration = [
        'unaccept' => [
            'is_accepted' => false,
            'is_shortlisted' => false,
        ],
        'accept' => [
            'is_accepted' => true,
            'is_shortlisted' => false,
        ],
        'shortlist' => [
            'is_accepted' => false,
            'is_shortlisted' => true,
        ],
        'unshortlist' => [
            'is_accepted' => false,
            'is_shortlisted' => false,
        ],
        'select' => [
            'in_selection' => true,
        ],
        'unselect' => [
            'in_selection' => false,
        ]
    ];

    public static $messageStatusConditions = [
        'accept' => ['for_accepted' => true],
        'shortlist' => ['for_shortlisted' => true],
        'unshortlist' => ['for_applicants' => true],
    ];

    /**
     * Change the status of an application
     * @param int $auditionId
     * @param mixed $target array | int
     * @param string $action accept | shortlist | select | unselect
     */
    public static function changeApplicationStatus(Audition $audition, mixed $target, string $action)
    {
        self::validateAction($action);
        
        $referralService = new ReferralsService( $target, $audition->id);
        $query = AuditionApplication::where('audition_id', $audition->id);

        if (is_array($target)) {
            $query->whereIn('user_profile_id', $target);
        } else {
            $query->where('user_profile_id', $target);
        }

        $query->update(self::$statusConfiguration[$action]);

        // handle adding the payment to the profile
        if ($action === 'accept') {
            PaymentsService::create($audition->project_id, $audition->id, $target);
            $referralService->rewardReferrals();
        }

        // handle adding the removing to the profile
        if ($action == 'unaccept' || $action == 'unshortlist' || $action == 'shortlist') {
            PaymentsService::delete($audition->project_id, $audition->id, $target);
            $referralService->removeRewards();
        }

        // if (isset(self::$messageStatusConditions[$action])) {
        //     if (is_array($target)) {
        //         foreach ($target as $t) {
        //             MessageService::syncUserMessages($t, $audition->id, self::$messageStatusConditions[$action]);
        //         }
        //     } else {
        //         MessageService::syncUserMessages($target, $audition->id, self::$messageStatusConditions[$action]);
        //     }
        // }
    }

    /**
     * Validate the action
     * @param string $action
     */

    private static function validateAction(string $action)
    {
        if (!array_key_exists($action, self::$statusConfiguration)) {
            throw new \Exception('Invalid action');
        }
    }

    public static function getProfileApplication(int $applicationId)
    {
        return AuditionApplication::where('id', $applicationId)
            ->with('profile.castingImages', 'profile.profileImages', 'audition', 'profile.nationalities', 'profile.passports', 'profile.country')
            ->first();
    }
}
