<?php

return [
    'eu' => [ //TODO FIX DUPLICATE ISSUE BECAUSE OF s3_ being prepended
        'bucket' => env('AWS_EU_BUCKET'),
        'region' => env('AWS_EU_BUCKET_REGION'),
    ],
    's3_eu' => [
        'bucket' => env('AWS_EU_BUCKET'),
        'region' => env('AWS_EU_BUCKET_REGION'),
    ],
    'default' => [
        'bucket' => env('AWS_USER_BUCKET'),
        'region' => env('AWS_USER_BUCKET_REGION'),
    ]
];
