<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnDemandCategory;
use App\Models\OnDemandView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassPenetrationController extends Controller
{
    /**
     * Fetch the top performing classes within a category.
     * @param OnDemandCategory $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topClasses(OnDemandCategory $category)
    {
        $classIds = $category->videos->pluck('id')->toArray();
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
        $endDate = Carbon::now()->format('Y-m-d H:i:s');

        $classes = OnDemandView::select('on_demand_id', \DB::raw('count(*) as total'))
            ->whereIn('on_demand_id', $classIds)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('on_demand_id')
            ->with('class')
            ->orderByDesc('total')
            ->get();

        return response()->json($classes);
    }

    /**
     * Fetch list of members who penetrated this category.
     * @param OnDemandCategory $category
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \League\Csv\CannotInsertRecord
     *
     */
    public function categoryMemberList(OnDemandCategory $category, Request $request)
    {
        $ages = $this->getAgeRangeArray($request->ages);

        /**
         * Fetch all class ids within this category.
         */
        $classIds = $category->videos->pluck('id')->toArray();

        /**
         * Fetch how many members have viewed these classes.
         */
        $members = OnDemandView::select('user_id')->whereIn('on_demand_id', $classIds)->groupBy('user_id')->get();

        $members = User::onlyMembers()
            ->whereIn('gender', $request->genders)
            ->whereIn('age', $ages);
        // ->whereIn('id', $members);

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

    /**
     * Load the view stats for the previous 30 days.
     *
     * @param OnDemandCategory $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function previousThirtyDays(OnDemandCategory $category)
    {
        $classIds = $category->videos->pluck('id')->toArray();

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

            $revenue = OnDemandView::select('user_id')
                ->whereIn('on_demand_id', $classIds)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->groupBy('user_id')->get()->count();

            $data->push($revenue);
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
}
