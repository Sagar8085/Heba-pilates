<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\OnDemand;
use App\Models\OnDemandCategory;
use App\Models\OnDemandFavourite;
use App\Models\OnDemandTag;
use App\Models\OnDemandWatchProgress;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnDemandController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = OnDemandCategory::orderBy('name', 'ASC')->with('videos')->get();

        return response()->json($categories);
    }

    public function singleCategory(OnDemandCategory $category): JsonResponse
    {
        $category->videos = $category->videos;
        return response()->json($category);
    }

    public function purchase(Request $request): JsonResponse
    {
        $this->validate($request, [
            'class_id' => 'required',
            'payment_method' => 'required',
            'price' => 'required',
        ]);

        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => $request->price,
            'method' => $request->payment_method,
            'orderable_id' => $request->class_id,
            'orderable_type' => 'App\Models\OnDemand',
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function continueWatching()
    {
        $continueWatching = OnDemand::select('on_demand_videos.*')
            ->join('on_demand_watch_progress', 'on_demand_watch_progress.ondemand_id', 'on_demand_videos.id')
            ->where('on_demand_watch_progress.completed', 0)
            ->where('on_demand_watch_progress.user_id', auth()->user()->id)
            ->with('instructor', 'equipment')
            ->get();

        $watchAgain = OnDemand::select('on_demand_videos.*')
            ->join('on_demand_watch_progress', 'on_demand_watch_progress.ondemand_id', 'on_demand_videos.id')
            ->where('on_demand_watch_progress.completed', 1)
            ->where('on_demand_watch_progress.user_id', auth()->user()->id)
            ->with('instructor', 'equipment')
            ->get();

        return response()->json([
            'continue_watching' => $continueWatching,
            'watch_again' => $watchAgain,
        ]);
    }

    public function recommended()
    {
        $topTags = OnDemandTag::select('_pivot_on_demand_tags.*',
            \DB::raw('COUNT(_pivot_on_demand_tags.tag_id) as totalTags'))
            ->join('on_demand_watch_progress', 'on_demand_watch_progress.ondemand_id',
                '_pivot_on_demand_tags.ondemand_id')
            ->where('on_demand_watch_progress.user_id', auth()->user()->id)
            ->groupBy('tag_id')
            ->orderBy('totalTags', 'DESC')
            ->take(1)
            ->get()->pluck('tag_id')->toArray();

        $classes = OnDemand::select('on_demand_videos.*')
            ->join('_pivot_on_demand_tags', '_pivot_on_demand_tags.ondemand_id', 'on_demand_videos.id')
            ->whereIn('_pivot_on_demand_tags.tag_id', $topTags)
            ->with('instructor', 'equipment')
            ->get();

        return response()->json($classes);
    }

    public function suggested()
    {
        $mostRecentWatch = OnDemandWatchProgress::where('user_id', auth()->user()->id)->where('completed',
            1)->latest()->first();

        if ($mostRecentWatch !== null) {
            $latest = OnDemand::find($mostRecentWatch->ondemand_id);
            $class = OnDemand::where('category_id', $latest->category_id)->where('order', '>',
                $latest->order)->orderBy('order', 'ASC')->with('equipment', 'instructor')->first();
        } else {
            $class = OnDemand::where('category_id', 1)->orderBy('order', 'ASC')->with('equipment',
                'instructor')->first();
        }


        return response()->json($class);
    }

    public function favourites()
    {
        $ids = OnDemandFavourite::where('user_id', auth()->user()->id)->get()->pluck('ondemand_id')->toArray();
        $favourites = OnDemand::whereIn('id', $ids)->with('instructor', 'equipment')->get();

        return response()->json($favourites);
    }

    public function single(OnDemand $ondemand): JsonResponse
    {
        $ondemand->storeView();
        $ondemand->playbackHistory = $ondemand->playbackHistory;
        return response()->json($ondemand);
    }

    public function toggleFavourite(OnDemand $ondemand): JsonResponse
    {
        $favourite = OnDemandFavourite::where('user_id', auth()->user()->id)
            ->where('ondemand_id', $ondemand->id)
            ->first();

        if ($favourite === null) {
            OnDemandFavourite::create([
                'user_id' => auth()->user()->id,
                'ondemand_id' => $ondemand->id,
            ]);
        } else {
            $favourite->delete();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function favouriteIds()
    {
        $favourites = OnDemandFavourite::where('user_id', auth()->user()->id)->get()->pluck('ondemand_id')->toArray();

        return response()->json($favourites);
    }
}
