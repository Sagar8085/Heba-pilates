<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainerRequest;
use App\Models\Availability;
use App\Models\MemberPackage;
use App\Models\Order;
use App\Models\Session;
use App\Models\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    /**
     * Fetch paginated list of all trainers.
     *
     * @return Json
     */
    public function all()
    {
        $trainers = User::onlyTrainers()->latest()->with('trainer', 'trainerSpecialisations')->paginate(50);

        return response()->json($trainers);
    }

    /**
     * Load a single trainer user.
     *
     * @param \App\Models\User $user
     *
     * @return Json
     */
    public function single(User $user)
    {
        $user->trainer = $user->trainer;

        $user = User::where('id', $user->id)->with('trainer', 'packages', 'trainerSpecialisations')->first();

        return response()->json($user);
    }

    /**
     * Update a trainer profile.
     *
     * @param \App\Models\User $user
     * @param Request $request
     *
     * @return Json
     */
    public function update(User $user, Request $request)
    {
        // return response()->json([
        //     'hi'
        // ]);
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->email . ',email',
            'phone_number' => 'required',
            'biography' => 'required|min:100',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        $user->trainerSpecialisations()->sync(array_column($request->specialisations, 'id'));

        $user->trainer()->update([
            'biography' => $request->biography,
            'qualifications' => $request->qualifications,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Validate and store a new trainer user and also create a trainer profile.
     *
     * @param \App\Http\Requests\TrainerRequest $request
     *
     * @return Json
     */
    public function store(TrainerRequest $request)
    {
        $user = User::create([
            'role_id' => 3,
            // This role ID is for trainer accounts only
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make(\Str::random(100))
            // A random string is assigned to this password as the recipient will recieve an email to confirm account and set a password
        ]);

        $user->trainerSpecialisations()->sync(array_column($request->specialisations, 'id'));

        Trainer::create([
            'user_id' => $user->id,
            'biography' => $request->biography,
        ]);

        return response()->json($user);
    }

    /**
     * Process profile picture to store in S3.
     *
     * @param \App\Models\User $user
     * @param Request $request
     *
     * @return Json
     */
    public function uploadImage(User $user, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('trainers', $file, 'public');

                /**
                 * Save the root picture path against the trainer profile.
                 */
                $user->trainer->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($user);
    }

    /**
     * Load a list of upcoming sessions.
     *
     * @param \App\Models\User $user
     *
     * @return Json
     */
    public function upcomingSessions(User $user)
    {
        $sessions = Session::where('trainer_id', $user->id)
            ->where(function ($q) {
                $q->where('datetime', '>=', date('Y-m-d H:i:s'))
                    ->orWhere('status', '=', 'active');
            })
            ->with('member', 'trainer', 'package', 'link')
            ->get();

        return response()->json($sessions);
    }

    /**
     * Load trainer performance statistics.
     *
     * @param \App\Models\User $user
     *
     * @return Json
     */
    public function stats(User $user)
    {
        $stats = collect();

        $totalSpend = Order::sum('value');
        $totalMembers = User::onlyMembers()->count();

        $totalBookings = Session::where('trainer_id', $user->id)->where('status', '!=', 'noshow')->count();
        $noShows = Session::where('trainer_id', $user->id)->where('status', 'noshow')->count();

        $avgRating = Session::where('trainer_id', $user->id)->avg('rating');

        $stats->push(
            [
                'title' => 'Revenue',
                'value' => '£' . number_format(MemberPackage::where('trainer_id', $user->id)->join('packages',
                        'packages.id', 'members_packages.package_id')->sum('packages.price')),
            ],
            [
                'title' => 'Active Clients',
                'value' => MemberPackage::where('trainer_id', $user->id)->where('remaining', '>', 0)->count(),
            ],
            [
                'title' => 'Completed Sessions',
                'value' => Session::where('trainer_id', $user->id)->where('status', 'completed')->count(),
            ],
            [
                'title' => 'Cancellations',
                'value' => Session::where('trainer_id', $user->id)->where('status', 'cancelled')->count(),
            ],
            [
                'title' => 'Attendance Rate',
                'value' => $totalBookings > 0 ? 100 - round($noShows / ($totalBookings / 100)) . '%' : 'N/A',
            ],
            [
                'title' => 'Avg. Rating',
                'value' => $avgRating > 0 ? $avgRating : 0 . ' / 5',
            ]
        );

        return response()->json($stats);
    }

    /**
     * Load all session history for this specific trainer.
     *
     * @param \App\Models\User $user
     *
     * @return Json
     */
    public function sessionHistory(User $user)
    {
        $sessions = Session::where('trainer_id', $user->id)->orderBy('datetime', 'DESC')->with('member',
            'package.package', 'link')->paginate(25);

        return response()->json($sessions);
    }

    public function capacity(User $user): JsonResponse
    {
        $data = collect();
        $legend = collect();

        /**
         * Loop through the previous 6 months and calculate the availability capacity for this trainer.
         */
        for ($i = 0; $i <= 6; $i++) {
            $date = Carbon::now()->subMonths($i)->format('Y-m-d');
            $dateName = Carbon::now()->subMonths($i)->format('jS');
            $legend->push($dateName);

            $startDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d') . ' 23:59:59';

            $capacity = Availability::where('user_id', $user->id)
                ->where('start', '>=', $startDate)
                ->where('end', '<=', $endDate)
                ->sum('hours');

            $data->push($capacity);
        }

        /**
         * Calculate the total sum of availability capacity.
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
