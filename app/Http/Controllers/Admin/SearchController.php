<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class SearchController extends Controller
{
    /**
     * Global search results.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $keyword = request('q');

        $users = User::where(function ($query) use ($keyword) {
            $query->where('users.id', $keyword)
                ->orWhere('users.first_name', 'like', '%' . $keyword . '%')
                ->orWhere('users.last_name', 'like', '%' . $keyword . '%')
                ->orWhere('users.email', 'like', '%' . $keyword . '%')
                ->orWhereRaw("concat(first_name, ' ', last_name) like '%" . $keyword . "%' ");
        })->paginate(50);

        return response()->json($users);
    }
}
