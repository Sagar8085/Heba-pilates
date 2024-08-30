<?php

namespace App\Http\Controllers\WebPortal;

use App\Http\Controllers\Controller;
use App\Models\OnDemand;

class SearchController extends Controller
{
    public $keyword;

    public function index()
    {
        $this->keyword = request('q');

        return response()->json([
            'ondemand' => $this->ondemandSearchResults(),
        ]);
    }

    private function ondemandSearchResults()
    {
        return OnDemand::select('on_demand_videos.*', 'users.first_name', 'users.last_name')
            ->join('users', 'users.id', 'on_demand_videos.instructor_id')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->keyword . '%')
                    ->orWhereRaw("concat(users.first_name, ' ', users.last_name) like '%" . $this->keyword . "%' ");
            })->with('categories', 'equipment', 'instructor')->get();
    }
}
