<?php
namespace App\Services\Notifications;

use App\Services\Notifications\AllProfilesNotification;
use App\Services\Notifications\AuditionCountryNotification;
use App\Services\Notifications\CountryNotification;
use App\Services\Notifications\ProfileNotification;
use Illuminate\Support\Facades\Log;


class NotificationsFactory
{
    public static function getFactory($mode)
    {
        Log::channel('notifications')
            ->info('NotificationsFactory::getFactory', [
                'mode' => $mode
            ]);

        switch ($mode) {
            case 'all':
                return AllProfilesNotification::class;
            case 'audition_country':
                return AuditionCountryNotification::class;
            case 'countries':
                return CountryNotification::class;
            case 'some':
                return ProfileNotification::class;
        }
    }
}
