<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileDownloadService;

class DownloadRequestController extends Controller
{
    public function download($hash)
    {
       return FileDownloadService::downloadZip($hash);
    }
}
