<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsLike;
use App\Http\Resources\NewsResource;
use App\Models\Profile;

class NewsService
{
    public int $limit = 10;

    public static function create($data)
    {

        $news = News::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $data['image'],
            'country_id' => $data['countries'][0]
        ]);

        $news->countries()->attach($data['countries']);

        return $news;
    }

    public static function update($data)
    {
        $news = News::find($data['id']);

        $news->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'image' => $data['image'],
            'country_id' => $data['countries'][0]
        ]);

        $news->countries()->sync($data['countries']);

        return $news;
    }

    public static function getCountryNews($page = 1)
    {
        $limit = 10;
        $countryId = auth()->user()->profile->country_id;

        $highlightNewsTabIcon = false;

        $news = News::whereHas('countries', function ($query) use ($countryId) {
            $query->where('country_id', $countryId);
        })->likesInfo()
            ->orderBy('id', 'desc')
            ->paginate($limit);


        if ($news->total() > 0) {
            $profile = Profile::find(auth()->user()->profile->id);

            if ($profile->news_last_seen == null) {
                $highlightNewsTabIcon = true;
            } else if ($profile->news_last_seen < News::whereHas('countries', function ($query) use ($countryId) {
                $query->where('country_id', $countryId);
            })->orderBy('id', 'desc')->first()->created_at) {
                $highlightNewsTabIcon = true;
            }

            $profile->news_last_seen = now();
            $profile->save();
        }

        $nextPage = $page * $limit < $news->total() ? $page + 1 : null;

        return [
            'has_unread_news' => $highlightNewsTabIcon,
            'news' => NewsResource::collection($news),
            'nextPage' => $nextPage
        ];
    }

    public static function getNewsArtcile($newsId)
    {
        $news = News::likesInfo()->findOrFail($newsId);

        return new NewsResource($news);
    }

    public static function likeNews($newsId)
    {
        $like = NewsLike::where([
            'news_id' => $newsId,
            'user_profile_id' => auth()->user()->profile->id
        ])->first();

        $user = auth()->user()->profile;

        if ($like) {
            $like->delete();
        } else {
            NewsLike::create([
                'news_id' => $newsId,
                'user_profile_id' => $user->id
            ]);
        }
    }
}
