<?php

namespace App\Traits;

use App\Services\ApplicationService;
use Filament\Notifications\Notification as FilamentNotification;
use App\Services\NotificationsService;
use App\Services\PaymentsService;
use App\Jobs\NotifyAppUsers;
use Filament\Notifications\Notification;

trait CanManageApplications
{
    public $selectedApplicants = [];
    public $activeTab = 'applicants';
    public $actionsOpen = false;

    public $tabLabels = [
        'applicants' => 'Applicants',
        'shortlisted' => 'Shortlisted',
        'accepted' => 'Accepted',
        'selection-list' => 'Selection List'
    ];


    public $tabProperty = [
        'applicants' => 'applicants',
        'shortlisted' => 'shortlisted',
        'accepted' => 'accepted',
        'selection-list' => 'selected'
    ];

    public function __construct()
    {
        if (auth()->user()->hasRole('director')) {
            $this->tabLabels = [
                'shortlisted' => 'Shortlisted',
                'selection-list' => 'Selection List'
            ];

            $this->tabProperty = [
                'shortlisted' => 'shortlisted',
                'selection-list' => 'selected'
            ];

            $this->activeTab = 'shortlisted';
        }
    }


    public function selectApplicant($id)
    {
        $this->selectedApplicants[] = $id;
    }

    public function unselectApplicant($id)
    {
        $this->selectedApplicants = array_diff($this->selectedApplicants, [$id]);
    }


    public function isApplicantSelected($id)
    {
        return in_array($id, $this->selectedApplicants);
    }

    public function setActiveTab($activeTab)
    {
        $this->activeTab = $activeTab;
    }

    // public function toggleActions()
    // {
    //     $this->dispatch('manage-audition-actions-popup-toggle');
    // }

    public function shouldSendNotification($status)
    {
        if ($this->activeTab == 'accepted') {
            return false;
        }

        if ($this->activeTab === 'shortlisted' && $status === 'unaccept') {
            return false;
        }

        if ($status === 'unaccept' || $status === 'unshortlist') {
            return false;
        }

        if ($status === 'select' || $status === 'unselect') {
            return false;
        }

        return true;
    }

    public function canModifyApplicationStatus()
    {   
        if (!$this->audition->applicationStatusesUpdatable()) {
            Notification::make()
                ->title('Project is not active')
                ->body('You cannot modify the status of this application. Either the project was deleted or the audition was terminated.')
                ->send();
            return false;
        }

        return true;
    }

    public function changeSelectedApplicationStatus($status)
    {
        if (!$this->canModifyApplicationStatus()) {
            return;
        }

        ApplicationService::changeApplicationStatus($this->audition, $this->selectedApplicants, $status);

        $type = 'APPLICATION_STATUS_CHANGED';

        if ($status === 'accept') {
            $type = 'STATUS_ACCEPTED';
        } else if ($status === 'shortlist') {
            $type = 'STATUS_SHORTLISTED';
        }


        if ($this->shouldSendNotification($status)) {
            NotifyAppUsers::dispatch(
                $type,
                $this->audition,
                $this->selectedApplicants,
                'some'
            )->onQueue('notification-chunks');
        }

        $this->selectedApplicants = [];
        unset($this->audition);
        $this->notify();
    }


    public function changeApplicationStatus($status, $profileId)
    {
        if (!$this->canModifyApplicationStatus()) {
            return;
        }

        if ($status === 'upgrade') {
            if ($this->activeTab === 'applicants') {
                $status = 'shortlist';
            } else if ($this->activeTab === 'shortlisted') {
                $status = 'accept';
            }
        }

        if ($status === 'downgrade') {
            if ($this->activeTab === 'shortlisted') {
                $status = 'unaccept';
            } else if ($this->activeTab === 'accepted') {
                $status = 'shortlist';
            }
        }

        ApplicationService::changeApplicationStatus($this->audition, $profileId, $status);

        $type = 'APPLICATION_STATUS_CHANGED';

        if ($status === 'accept') {
            $type = 'STATUS_ACCEPTED';
        } else if ($status === 'shortlist') {
            $type = 'STATUS_SHORTLISTED';
        }

        if ($this->shouldSendNotification($status)) {
            NotifyAppUsers::dispatch(
                $type,
                $this->audition,
                $profileId,
                'some'
            )->onQueue('notification-chunks');
        }

        unset($this->audition);
        $this->notify();
    }

    public function getActiveTabLabel()
    {
        return $this->tabLabels[$this->activeTab];
    }

    public function selectApplicantsInTab()
    {
        $property = $this->tabProperty[$this->activeTab];
        $this->selectedApplicants = $this->audition->{$property}->pluck('id')->toArray();

        if ($this->selectedApplicants == null) {
            $this->selectedApplicants = [];
            return;
        }
    }

    public function clearSelection()
    {
        $this->selectedApplicants = [];
    }


    public function notify($message = 'Application Status Changed!', $color = 'success')
    {
        FilamentNotification::make()
            ->title($message)
            ->color($color)
            ->send();
    }
}
