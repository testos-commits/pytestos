<?php

namespace App\Services;

use App\Models\User;
use App\Models\RewardReferral;

/// add referral_reward_count in user table, defaults to 0
class ReferralsService
{
    public $eligible = null;
    public $profileId = null;
    public $auditionId = null;

    public function __construct($profileId, $auditionId)
    {
        $this->profileId = $profileId;
        $this->auditionId = $auditionId;
    }

    public function rewardReferrals()
    {
        $this->setEligibleRewards();
        $this->insertRefferalRewards();
    }

    public function removeRewards()
    {
        $query = new RewardReferral();

        if (is_array($this->profileId)) {
            $query->whereIn('referred_id', $this->profileId);
        } else {
            $query->where('referred_id', $this->profileId);
        }

        $query->where('audition_id', $this->auditionId)
            ->delete();
    }

    private function setEligibleRewards()
    {
        $query = User::whereNotNull('referred_by')
            ->with('referredBy')
            ->has('referralRewards', '<', config('referral-rewards.reward_count'));

        if (is_array($this->profileId)) {
            $query->whereHas('profile', function ($q) {
                $q->whereIn('id', $this->profileId);
            });
        } else {
            $query->whereHas('profile', function ($q) {
                $q->where('id', $this->profileId);
            });
        }

        $this->eligible = $query
            ->get();
    }

    private function insertRefferalRewards()
    {
        if (!$this->eligible) {
            return;
        }

        $data = [];
        foreach ($this->eligible as $user) {
            $data[] = [
                'referrer_id' => $user->referredBy->id,
                'referred_id' => $user->id,
                'audition_id' => $this->auditionId,
                'reward' => config('referral-rewards.reward_amount'), // add the reward amount in config
            ];
        }

        RewardReferral::insert($data);
    }
}
