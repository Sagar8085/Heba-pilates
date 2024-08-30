<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\OnDemandView;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserWorkoutData;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class UserDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalUsers = User::whereIn('gender', ['male', 'female'])->count();
        $maleUsers = User::where('gender', 'male')->count();
        $maleRatio = round($maleUsers / ($totalUsers / 100));
        $femaleRatio = (100 - $maleRatio);

        $stats = new \stdClass();
        $stats->newSignUps = User::where('created_at', '>=', Carbon::now()->startOfMonth())->where('created_at', '<=',
            Carbon::now())->count();
        $stats->inactive = 0;
        $stats->averageAge = round(User::avg('age'));
        $stats->genderRatio = $maleRatio . '%' . ' / ' . $femaleRatio . '%';
        $stats->premiumSubscribers = Subscription::where('tier', 'premium')->where('expires', '>',
            Carbon::now())->count();
        $stats->podcastSubscribers = Subscription::where('tier', 'podcast')->where('expires', '>',
            Carbon::now())->count();
        $stats->classesWatched = OnDemandView::where('created_at', '>=',
            Carbon::now()->startOfMonth())->where('created_at', '<=', Carbon::now())->count();
        $stats->workoutsCompleted = UserWorkoutData::where('created_at', '>=',
            Carbon::now()->startOfMonth())->where('created_at', '<=', Carbon::now())->count();

        return response()->json($stats);
    }
}
