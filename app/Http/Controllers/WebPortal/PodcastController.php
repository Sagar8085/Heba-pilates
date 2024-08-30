<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\UserPodcastData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PodcastController extends Controller
{

    public function purchase(Podcast $podcast): JsonResponse
    {
        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => 399,
            'method' => 'stripe',
            'orderable_id' => $podcast->id,
            'orderable_type' => 'App\Models\Podcast',
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function episode(PodcastCategory $category, $episode): JsonResponse
    {
        $category = $category->load('podcasts');

        return response()->json($category->podcasts[$episode - 1]);
    }

    public function updateProgress(Podcast $podcast, Request $request): JsonResponse
    {
        $podcastData = UserPodcastData::where('user_id', auth()->user()->id)->where('podcast_id',
            $podcast->id)->first();

        $completed = $podcast->duration * 0.95 < $request->current_time ? 1 : 0;

        if (!$podcastData) {
            $podcastData = UserPodcastData::create([
                "user_id" => auth()->user()->id,
                "podcast_id" => $podcast->id,
                "current_time" => $request->current_time,
                "completed" => $completed,
            ]);
        } else {
            $podcastData->current_time = $request->current_time;
            $podcastData->completed = $completed;
            $podcastData->save();
        }

        return response()->json($podcastData);
    }

    public function getUserProgress(Podcast $podcast): JsonResponse
    {
        $podcastData = UserPodcastData::where('user_id', auth()->user()->id)->where('podcast_id',
            $podcast->id)->first();

        return response()->json($podcastData);
    }
}
