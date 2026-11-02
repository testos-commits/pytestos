<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ExpoNotificationService
{

    // public static function send($data)
    // {
    //     $jsonData = json_encode($data);
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, "https://exp.host/--/api/v2/push/send");
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         "host: exp.host",
    //         "accept: application/json",
    //         "accept-encoding: gzip, deflate",
    //         "content-type: application/json"
    //     ]);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     $response = curl_exec($ch);

    //     if (curl_errno($ch)) {
    //         Log::error("cURL error: " . curl_error($ch));
    //     } else {
    //         Log::info("Notifications sent. Response: " . $response);
    //     }

    //     curl_close($ch);
    // }

    public static function send($data)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'accept-encoding' => 'gzip, deflate',
                'content-type' => 'application/json',
            ])
                ->post('https://exp.host/--/api/v2/push/send', $data);

            if ($response->successful()) {
                Log::info('Notifications sent. Response: ' . $response->body());
            } else {
                Log::error('Expo push failed. Status: ' . $response->status() . ', Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('HTTP error while sending Expo push: ' . $e->getMessage());
        }
    }
}
