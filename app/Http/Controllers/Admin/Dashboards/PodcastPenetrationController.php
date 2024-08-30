<?php

namespace App\Http\Controllers\Admin\Dashboards;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PodcastPenetrationController extends Controller
{
    public function topPodcasts(PodcastCategory $category): JsonResponse
    {
        $classIds = $category->podcasts->pluck('id')->toArray();

        $classes = collect();

        foreach ($classIds as $id) {
            $class = Podcast::find($id);
            $views = mt_rand(25, 150);

            $classes->push([
                'id' => $class->id,
                'name' => $class->name,
                'views' => $views,
            ]);
        }

        return response()->json($classes);
    }

    public function previousThirtyDays(PodcastCategory $category): JsonResponse
    {
        $classIds = $category->podcasts->pluck('id')->toArray();

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

            $revenue = mt_rand(50, 100);

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

    public function categoryMemberList(PodcastCategory $category, Request $request): JsonResponse
    {
        $ages = $this->getAgeRangeArray($request->ages);

        /**
         * Fetch all class ids within this category.
         */
        $classIds = $category->podcasts->pluck('id')->toArray();

        /**
         * Fetch how many members have viewed these classes.
         */
        $members = mt_rand(0, 25);

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

    public function episodeMemberList(Podcast $episode, Request $request): JsonResponse
    {
        $ages = $this->getAgeRangeArray($request->ages);

        /**
         * Fetch how many members have viewed this podcast..
         */
        $members = mt_rand(0, 25);

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
}
