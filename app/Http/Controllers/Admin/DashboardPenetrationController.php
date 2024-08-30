<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\LiveClassBooking;
use App\Models\LiveClassCategory;
use App\Models\OnDemand;
use App\Models\OnDemandCategory;
use App\Models\OnDemandView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPenetrationController extends Controller
{
    /**
     * Fetch penetration rates per class.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function classes()
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d') . ' 23:59:59';

        $classes = collect();

        $list = OnDemandCategory::select('on_demand_categories.*')->filterAdminAccess()->get();

        foreach ($list as $item) {

            /**
             * Fetch all class ids within this category.
             */
            $classIds = $item->videos->pluck('id')->toArray();

            /**
             * Fetch how many members have viewed these classes.
             */
            $members = OnDemandView::select('user_id')
                ->whereIn('on_demand_id', $classIds)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('user_id')->get()->count();

            $totalMembers = User::onlyMembers()->count();

            $classes->push([
                'id' => $item->id,
                'name' => $item->name,
                'penetration_rate' => round($members / ($totalMembers / 100), 2),
            ]);
        }

        return response()->json([
            'classes' => $classes,
            'total_members' => $totalMembers,
        ]);
    }

    /**
     * Fetch penetration rates per live class.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function live()
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d') . ' 23:59:59';

        $classes = collect();

        $list = LiveClassCategory::latest()->get();

        foreach ($list as $item) {

            /**
             * Fetch all the scheduled live classes within this category this month.
             */
            $classIds = LiveClass::where('datetime', '>=', $startDate)
                ->where('datetime', '<=', $endDate)
                ->where('category_id', $item->id)
                ->get()->pluck('id')->toArray();

            /**
             * Fetch how many members have viewed these classes.
             */
            $members = LiveClassBooking::select('member_id')
                ->whereIn('liveclass_id', $classIds)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('member_id')->get()->count();

            $totalMembers = User::onlyMembers()->count();

            $classes->push([
                'id' => $item->id,
                'name' => $item->name,
                'penetration_rate' => round($members / ($totalMembers / 100), 2),
            ]);
        }

        return response()->json([
            'classes' => $classes,
            'total_members' => $totalMembers,
        ]);
    }

    /**
     * Load age demographics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAgeDemographics()
    {
        $type = request('type');
        $id = request('id');

        $userIds = $this->loadDemographicUsers($type, $id);

        $legend = collect();
        $data = collect();

        /**
         * Loop through each of the age ranges and get the number of users using this item for each age range.
         */
        $ranges = [[16, 25], [26, 40], [41, 55], ['56+']];

        foreach ($ranges as $range) {

            if ($range[0] == '56+') {
                $ageStart = 56;
                $ageEnd = 120;
                $name = $range[0];
            } else {
                $ageStart = $range[0];
                $ageEnd = $range[1];
                $name = $ageStart . ' - ' . $ageEnd;
            }

            $value = User::whereIn('id', $userIds)->where('age', '>=', $ageStart)->where('age', '<=', $ageEnd)->count();

            $legend->push($name);
            $data->push($value);
        }

        return response()->json([
            'legend' => $legend,
            'data' => $data,
        ]);
    }

    /**
     * Load gender demographics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadGenderDemographics()
    {
        $type = request('type');
        $id = request('id');

        $userIds = $this->loadDemographicUsers($type, $id);

        $legend = collect();
        $data = collect();

        /**
         * Loop through each of the age ranges and get the number of users using this item for each age range.
         */
        $genders = ['male', 'female', 'other'];

        foreach ($genders as $gender) {
            $value = User::whereIn('id', $userIds)->where('gender', $gender)->count();

            $legend->push($gender);
            $data->push($value);
        }

        return response()->json([
            'legend' => $legend,
            'data' => $data,
        ]);
    }

    /**
     * Fetch user ids for use in a demographic.
     *
     * @param String $type
     * @param Integer $id
     *
     * @return Array $userIds
     */
    private function loadDemographicUsers($type, $id)
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d') . ' 23:59:59';

        if ($type === 'class-category') {
            $category = OnDemandCategory::find($id);
            $classIds = $category->videos->pluck('id')->toArray();

            $userIds = OnDemandView::select('user_id')
                ->whereIn('on_demand_id', $classIds)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('user_id')
                ->get()->pluck('user_id')->toArray();
        } else {
            if ($type === 'class') {
                $class = OnDemand::find($id);

                $userIds = OnDemandView::select('user_id')
                    ->where('on_demand_id', $class->id)
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->groupBy('user_id')
                    ->get()->pluck('user_id')->toArray();
            } else {
                if ($type === 'live-category') {
                    $classIds = LiveClass::where('datetime', '>=', $startDate)
                        ->where('datetime', '<=', $endDate)
                        ->where('category_id', $id)
                        ->get()->pluck('id')->toArray();

                    $userIds = LiveClassBooking::select('member_id')
                        ->whereIn('liveclass_id', $classIds)
                        ->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate)
                        ->groupBy('member_id')
                        ->get()->pluck('member_id')->toArray();
                }
            }
        }

        return $userIds;
    }

    /**
     * Load popularity graph data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadPopularityGraph()
    {
        $type = request('type');
        $id = request('id');

        $data = collect();
        $legend = collect();

        /**
         * Loop through the previous 30 days and calculate the number of views for this category.
         */
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateName = Carbon::now()->subDays($i)->format('jS');
            $legend->push($dateName);

            $startDate = Carbon::parse($date)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->format('Y-m-d') . ' 23:59:59';

            $views = $this->loadPopularityViews($type, $id, $startDate, $endDate);
            $data->push($views);
        }

        /**
         * Calculate the total sum of paid revenue for today.
         * Reverse the order of the data so that the most recent day is on the right.
         */
        $sum = number_format($data->sum(), 2);
        $data = $data->reverse()->values()->all();
        $legend = $legend->reverse()->values()->all();

        return response()->json([
            'sum' => $sum,
            'data' => $data,
            'legend' => $legend,
        ]);
    }

    /**
     * Fetch popularity views for graph data.
     *
     * @param String $type
     * @param Integer $id
     *
     * @return Array $views
     */
    private function loadPopularityViews($type, $id, $startDate, $endDate)
    {
        if ($type === 'class-category') {
            $category = OnDemandCategory::find($id);
            $classIds = $category->videos->pluck('id')->toArray();

            $views = OnDemandView::select('user_id')
                ->whereIn('on_demand_id', $classIds)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('user_id')->get()->count();
        } else {
            if ($type === 'class') {
                $views = OnDemandView::select('user_id')
                    ->where('on_demand_id', $id)
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->groupBy('user_id')->get()->count();
            } else {
                if ($type === 'workout-category') {
                    $category = WorkoutCategory::find($id);
                    $workoutIds = $category->workouts->pluck('id')->toArray();

                    $views = UserWorkoutData::select('user_id')
                        ->whereIn('workout_id', $workoutIds)
                        ->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate)
                        ->groupBy('user_id')->get()->count();
                } else {
                    if ($type === 'workout') {
                        $views = UserWorkoutData::select('user_id')
                            ->where('workout_id', $id)
                            ->where('created_at', '>=', $startDate)
                            ->where('created_at', '<=', $endDate)
                            ->groupBy('user_id')->get()->count();
                    }
                }
            }
        }

        return $views;
    }

    /**
     * Load a list of members who have penetrated a particular item.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \League\Csv\CannotInsertRecord
     */
    public function loadMembers(Request $request)
    {
        $ages = $this->getAgeRangeArray($request->ages);

        $userIds = $this->loadDemographicUsers($request->type, $request->id);

        $members = User::whereIn('gender', $request->genders)
            ->whereIn('age', $ages)
            ->whereIn('id', $userIds);

        if ($request->download === 'true') {
            $members = $members->get();

            // Create CSV in Memory
            $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());

            // Add Headers
            $csv->insertOne([
                'Name',
                'Email',
                'Phone Number',
                'Date of Birth',
                'Age',
            ]);

            // Add Results
            foreach ($members as $member) {
                $csv->insertOne([
                    $member->name,
                    $member->email,
                    $member->phone_number,
                    $member->date_of_birth,
                    $member->age,
                ]);
            }

            $csv->output('export.csv');
            die;
        } else {
            $members = $members->paginate(25);
        }

        return response()->json($members);
    }


    private function getAgeRangeArray($ranges)
    {
        $ages = collect();

        /**
         * Add age ranges to an array.
         */
        foreach ($ranges as $ageRange) {

            if ($ageRange == '56+') {
                for ($i = 56; $i <= 120; $i++) {
                    $ages->push($i);
                }

                continue;
            }

            $range = explode('-', $ageRange);
            $start = (int)$range[0];
            $end = (int)$range[1];
            while ($start <= $end) {
                $ages->push($start);
                $start++;
            }
        }

        return $ages;
    }
}
