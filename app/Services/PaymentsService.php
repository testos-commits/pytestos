<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Profile;

class PaymentsService
{
    public static function getByProject($projectId)
    {
        return Payment::with('profile.user')->projectAccounting($projectId)->get();
    }

    public static function getByAudition($auditionId)
    {
        return Payment::with('profile.user')->auditionAccounting($auditionId)->get();
    }

    public static function getById($id)
    {
        return Payment::with('profile.user.paymentDetails', 'audition', 'project')->find($id);
    }

    public static function create($projectId, $auditionId, array | int $users)
    {
        if (is_array($users)) {
            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    'user_profile_id' => $user,
                    'project_id' => $projectId,
                    'audition_id' => $auditionId
                ];
            }
            if (count($data) > 0) {
                Payment::insert($data);
            }
        } else {
            Payment::create([
                'user_profile_id' => $users,
                'project_id' => $projectId,
                'audition_id' => $auditionId
            ]);
        }

        self::timestampWallet($users);
    }

    public static function delete($projectId, $auditionId, array | int $users)
    {
        $query = Payment::where([
            'project_id' => $projectId,
            'audition_id' => $auditionId
        ]);

        if (is_array($users)) {
            $query->whereIn('user_profile_id', $users);
        } else {
            $query->where('user_profile_id', $users);
        }

        $query->delete();
    }

    public static function getUserWallet($profile)
    {
        $_payments = Payment::where('user_profile_id', $profile->id)
            ->with([
                'project',
                'audition'
            ])
            ->whereHas('audition', function ($query) {
                $query->whereNotNull('filming_budget');
            })
            ->get();

        $total = 0;
        $outstanding = 0;
        $currency = '';

        $payments = $_payments->map(function ($payment) use (&$total, &$outstanding, &$currency) {
            if (!is_null($payment->audition) && !is_null($payment->audition->filming_budget)) {
                $currency = $payment->custom_currency ? $payment->custom_currency : $payment->audition->currency;
                $audition_amount = 0;

                if ($payment->custom_amount > 0 && $payment->custom_amount !== null) {
                    $audition_amount = $payment->custom_amount;
                } else {
                    $audition_amount += $payment->audition->total_budget;
                }

                $total += $audition_amount;

                if ($payment->status === 'pending' || $payment->status === 'ready_for_collection') {
                    $outstanding += $audition_amount;
                }

                return [
                    'id' => $payment->id,
                    'audition' => $payment->project ? $payment->project->title : '#' . (string)$payment->project_id, 
                    'amount' => (string)$audition_amount . ' ' . $currency,
                    'status' => $payment->status,
                ];
            }
        });

        return [
            'total' => (string)$total . ' ' . $currency,
            'outstanding' => (string)$outstanding . ' ' . $currency,
            'payments' => $payments
        ];
    }

    public static function timestampWallet(int | array $users)
    {
        if (is_array($users)) {
            Profile::whereIn('id', $users)->update(['wallet_updated_on' => now()]);
        } else {
            Profile::where('id', $users)->update(['wallet_updated_on' => now()]);
        }
    }
}
