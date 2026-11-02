<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewsService;

class NewsController extends Controller
{
    public function index(Request $request) {
        $request->validate(['page' => 'integer']);

        return response()->json(NewsService::getCountryNews($request->get('page', 1)));
    }

    public function show($newsId) {
        return response()->json(NewsService::getNewsArtcile($newsId));
    }

    public function changeLikeStatus($newsId) {
       
        NewsService::likeNews($newsId);
        return response()->json(['message' => 'success']);
    }
}
