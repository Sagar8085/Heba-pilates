<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\OnDemand;
use App\Models\OnDemandFavourite;
use App\Models\OnDemandTag;
use App\Models\OnDemandWatchProgress;
use App\Models\Order;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnDemandController extends Controller
{
    /**
     * Fetch library of all videos.
     *
     * @param None
     *
     * @return Json
     */
    public function library()
    {
        $library = OnDemand::latest()->where('processed', 1)->paginate(25);

        return response()->json($library);
    }

    public function single(OnDemand $ondemand): JsonResponse
    {
        $ondemand->storeView();
        $ondemand->playbackHistory = $ondemand->playbackHistory;
        $ondemand->load(['category']);
        return response()->json($ondemand);
    }

    public function purchase(OnDemand $ondemand, Request $request)
    {
        $order = Order::create([
            'member_id' => auth()->user()->id,
            'value' => 399,
            'method' => 'stripe',
            'orderable_id' => $ondemand->id,
            'orderable_type' => 'App\Models\OnDemand',
            'stripe_order_id' => $request->stripe_order_id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function toggleFavourite(OnDemand $ondemand): JsonResponse
    {
        $favourite = OnDemandFavourite::where('user_id', auth()->id())
            ->where('ondemand_id', $ondemand->id)
            ->first();

        if ($favourite === null) {
            $favourite = OnDemandFavourite::create([
                'user_id' => auth()->user()->id,
                'ondemand_id' => $ondemand->id,
            ]);
        } else {
            $favourite->delete();
        }

        return response()->json([
            'favourited' => $favourite->exists,
        ]);
    }

    public function favourites()
    {
        $ids = OnDemandFavourite::where('user_id', auth()->user()->id)->get()->pluck('ondemand_id')->toArray();
        $favourites = OnDemand::whereIn('on_demand_videos.id', $ids)->with('instructor', 'equipment')
            ->filterDurationRange(request('duration'))
            ->filterInstructor(request('instructor'))
            ->filterTag(request('tag'))->get();

        return response()->json($favourites);
    }

    public function favouritesByID()
    {
        $favourites = OnDemandFavourite::where('user_id', auth()->user()->id)->get()->pluck('ondemand_id')->toArray();

        return response()->json($favourites);
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


        if (request('tag')) {
            $classes = OnDemand::select('on_demand_videos.*')
                ->with('instructor', 'equipment')
                ->where('processed', 1)
                ->filterDurationRange(request('duration'))
                ->filterInstructor(request('instructor'))
                ->filterTag(request('tag'))
                ->whereIn('_pivot_on_demand_tags.tag_id', $topTags)
                ->get();
        } else {
            $classes = OnDemand::select('on_demand_videos.*')
                ->join('_pivot_on_demand_tags', '_pivot_on_demand_tags.ondemand_id', 'on_demand_videos.id')
                ->whereIn('_pivot_on_demand_tags.tag_id', $topTags)
                ->with('instructor', 'equipment')
                ->where('processed', 1)
                ->filterDurationRange(request('duration'))
                ->filterInstructor(request('instructor'))
                ->filterTag(request('tag'))
                ->get();
        }

        return response()->json($classes);
    }

    public function continueWatching()
    {
        $continueWatching = OnDemand::select('on_demand_videos.*')
            ->join('on_demand_watch_progress', 'on_demand_watch_progress.ondemand_id', 'on_demand_videos.id')
            ->where('on_demand_watch_progress.completed', 0)
            ->where('on_demand_watch_progress.user_id', auth()->user()->id)
            ->where('processed', 1)
            ->with('instructor', 'equipment')
            ->filterDurationRange(request('duration'))
            ->filterInstructor(request('instructor'))
            ->filterTag(request('tag'))
            ->get();

        $watchAgain = OnDemand::select('on_demand_videos.*')
            ->join('on_demand_watch_progress', 'on_demand_watch_progress.ondemand_id', 'on_demand_videos.id')
            ->where('on_demand_watch_progress.completed', 1)
            ->where('on_demand_watch_progress.user_id', auth()->user()->id)
            ->where('processed', 1)
            ->with('instructor', 'equipment')
            ->filterDurationRange(request('duration'))
            ->filterInstructor(request('instructor'))
            ->filterTag(request('tag'))
            ->get();

        return response()->json([
            'continue_watching' => $continueWatching,
            'watch_again' => $watchAgain,
        ]);
    }

    public function watchProgress(OnDemand $ondemand, Request $request)
    {
        $progress = OnDemandWatchProgress::where('ondemand_id', $ondemand->id)->where('user_id',
            auth()->user()->id)->first();

        if ($progress === null) {
            $progress = OnDemandWatchProgress::create([
                'ondemand_id' => $ondemand->id,
                'user_id' => auth()->user()->id,
                'time' => ($request->progress * 100),
                'completed' => 0,
            ]);
        } else {
            $completed = 0;

            // If we are 30 seconds from finishing the video, let's mark as completed.
            if ($request->video_length - $request->progress < 30) {
                $completed = 1;
            }

            $progress->update([
                'time' => ($request->progress * 100),
                'completed' => $completed,
            ]);
        }

        return response()->json($request->video_length - $request->progress);
    }

    public function suggested()
    {
        $mostRecentWatch = OnDemandWatchProgress::where('user_id', auth()->user()->id)->where('completed',
            1)->latest()->first();

        if ($mostRecentWatch !== null) {
            $latest = OnDemand::find($mostRecentWatch->ondemand_id);
            if ($latest === null) {
                $class = OnDemand::where('category_id', 1)->where('processed', 1)->orderBy('order',
                    'ASC')->with('equipment', 'instructor')->first();
            } else {
                $class = OnDemand::where('category_id', $latest->category_id)->where('processed', 1)->where('order',
                    '>', $latest->order)->orderBy('order', 'ASC')->with('equipment', 'instructor')->first();
            }
        } else {
            $class = OnDemand::where('category_id', 1)->where('processed', 1)->orderBy('order',
                'ASC')->with('equipment', 'instructor')->first();
        }


        return response()->json($class);
    }

    public function instructors()
    {
        $instructors = User::onlyTrainers()->get();
        return response()->json($instructors);
    }

    public function tags()
    {
        $tags = Tag::orderBy('name', 'ASC')->get();
        return response()->json($tags);
    }
}
