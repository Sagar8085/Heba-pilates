<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Load current auth admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = User::find(auth()->user()->id);
        $user->privileges = $user->privileges;
        return response()->json($user);
    }

    /**
     * Fetch paginated list of all admins.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all()
    {
        $admins = User::onlyAdmins()->latest()->paginate(50);

        return response()->json($admins);
    }

    /**
     * Load a single admin profile.
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(User $user)
    {
        $user->privileges = $user->privileges;
        $user->gyms = $user->gyms;
        return response()->json($user);
    }

    /**
     * Validate and store a new admin account.
     *
     * @param \App\Http\Requests\AdminRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AdminRequest $request)
    {
        $user = User::create([
            'role_id' => 2,
            // This role ID is for normal administrators only
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'is_sales_agent' => $request->is_sales_agent,
            'password' => Hash::make(\Str::random(100))
            // A random string is assigned to this password as the recipient will recieve an email to confirm account and set a password
        ]);

        $user->sendInvitationEmail();

        return response()->json($user);
    }

    /**
     * Validate and update an admin.
     * @param \App\Models\User $user
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->email . ',email',
            'phone_number' => 'required',
            'is_sales_agent' => 'required',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'is_sales_agent' => $request->is_sales_agent,
        ]);

        $user->gyms()->sync(array_column($request->gyms, 'id'));

        return response()->json($user);
    }

    /**
     * Process profile picture to store in S3.
     *
     * @param \App\Models\User $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function uploadImage(User $user, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('admins', $file, 'public');

                /**
                 * Save the root image path against the user.
                 */
                $user->update([
                    'avatar_path' => $file_path,
                ]);
            }
        }

        return response()->json($user);
    }

    public function updatePrivilege(User $user, Request $request): JsonResponse
    {
        $privilege = Privilege::where('user_id', $user->id)->where('privilege', $request->privilege)->first();

        $split = explode('-', $request->privilege);

        if ($privilege === null) {
            // If this new privilege is create, edit or delete, we need to make sure they have the 'read' option too.

            if ($split[1] === 'create' || $split[1] === 'update' || $split[1] === 'delete') {
                $read = Privilege::where('user_id', $user->id)->where('privilege', $split[0] . '-read')->first();

                if ($read === null) {
                    Privilege::create([
                        'user_id' => $user->id,
                        'privilege' => $split[0] . '-read',
                    ]);
                }
            }

            Privilege::create([
                'user_id' => $user->id,
                'privilege' => $request->privilege,
            ]);
        } else {
            // If we are removing the 'read' option, we also need to remove the 'create', 'edit' and 'delete' options too.
            if ($split[1] === 'read') {
                $read = Privilege::where('user_id', $user->id)->whereIn('privilege', [
                    $split[0] . '-create',
                    $split[0] . '-update',
                    $split[0] . '-delete',
                ])->delete();
            }

            $privilege->delete();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }
}
