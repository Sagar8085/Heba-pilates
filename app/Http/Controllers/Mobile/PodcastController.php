<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\UserPodcastData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = PodcastCategory::latest()->with('podcasts')->get();

        return response()->json($categories);
    }

    public function purchase(Request $request): JsonResponse
    {
        $this->validate($request, [
            'podcast_id' => 'required',
            'payment_method' => 'required',
            'price' => 'required',
        ]);

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => $request->price,
            'method' => $request->payment_method,
            'orderable_id' => $request->podcast_id,
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
