<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Index;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Sorters\UserSorter;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    public function __invoke(Index $request, UserFilter $filters, UserSorter $sorter)
    {
        $users = User::query()
            ->withoutCurrentUser()
            ->withRelationships()
            ->withAdditionalFields()
            ->filter($filters)
            ->sort($sorter);

        return $request->input('download')
            ? Excel::download((new UsersExport($users->get())), 'Guest Export.csv')
            : UserResource::collection($users->paginate($request->get('perPage', 20)));
    }
}
