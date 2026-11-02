<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PresignedUrlController extends Controller
{

    public function generatePresignedUrl(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
            "directory" => 'required|string',
            'bucket' => 'required|string',
            'region' => 'required|string'
        ]);

        $bucket = Storage::createS3Driver([
            'driver' => 's3',
            'key'    =>  config('filesystems.disks.s3_admin.key'),
            'secret' => config('filesystems.disks.s3_admin.secret'),
            'region' => $request->region,
            'bucket' => $request->bucket,
        ]);

        $key = $request->directory . '/' . now()->timestamp . '_' . Str::random(10) . '_' . $request->file_name;
        $expiry = now()->addMinutes(5);

        $url = $bucket->temporaryUploadUrl(
            $key,
            $expiry,
            [
                'Content-Type' => $request->file_type,
            ]
        );

        return response()->json([
            'url' => $url['url'],
            'headers' => $url['headers'],
            'expiry' => $expiry,
            'key' => $key,
        ]);
    }

    public function generatePresignedUrls(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.file_name' => 'required|string',
            'items.*.file_type' => 'required|string',
            'bucket' => 'required|string',
            'region' => 'required|string'
        ]);


        $bucket = Storage::createS3Driver([
            'driver' => 's3',
            'key'    =>  env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => $request->region,
            'bucket' => $request->bucket,
        ]);


        $urls = [];

        foreach ($request->items as $file) {

            $key = now()->timestamp . '_' . Str::random(10) . '_' . $file['file_name'];

            $url = $bucket->temporaryUploadUrl(
                $key,
                now()->addMinutes(5),
            );
            $urls[] = $url;
        }

        return response()->json(['files' => $urls]);
    }
}
