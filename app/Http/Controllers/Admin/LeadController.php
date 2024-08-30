<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Focus;
use App\Models\Goal;
use App\Models\Lead;
use App\Models\LeadAchievement;
use App\Models\LeadActivityLog;
use App\Models\LeadActivityType;
use App\Models\LeadAppointment;
use App\Models\LeadCall;
use App\Models\LeadCallSchedule;
use App\Models\LeadNote;
use App\Models\LeadSignup;
use App\Models\Member;
use App\Models\User;
use App\Models\UserFocus;
use App\Rules\DateOfBirth;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Storage;

/**
 * LeadController - Lead Management API
 */
class LeadController extends Controller
{

    /**
     * Return the data needed for the initial Lead Management Dashboard
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        // Get the date filters for start and end of day
        $startOfDay = Carbon::now()->startOfMonth();
        $endOfDay = Carbon::now()->endOfMonth();

        // get the leads assigned to this Sales Agent
        $leads = Lead::where('assigned_to', Auth::user()->id)
            ->where('interested', 1)
            ->limit(10);

        if ($request->sort) {
            switch ($request->sort['field']) {
                case 'calls_made':
                    if ($request->sort['order'] === 'DESC') {
                        $leads = $leads->get()->sortByDesc('calls_made')->values()->all();
                    } else {
                        $leads = $leads->get()->sortBy('calls_made')->values()->all();
                    }

                    break;
                default:
                    $leads = $leads->orderBy($request->sort['field'], $request->sort['order'])->get();
                    break;
            }
        } else {
            $leads = $leads->orderBy('created_at', 'DESC')->get();
        }

        // Get the count of calls made by this sales agent within the day
        $calls = LeadCall::where('agent_id', Auth::user()->id)
            ->where('created_at', '>=', $startOfDay)
            ->where('created_at', '<=', $endOfDay)
            ->count();

        // Get the count of appointments made by this sales agent within the day
        $appointments = LeadAppointment::where('agent_id', Auth::user()->id)
            ->where('created_at', '>=', $startOfDay)
            ->where('created_at', '<=', $endOfDay)
            ->count();

        // Get the count of Sign ups made by this sales agent within the day
        $signups = LeadSignup::where('agent_id', Auth::user()->id)
            ->where('created_at', '>=', $startOfDay)
            ->where('created_at', '<=', $endOfDay)
            ->count();

        // Get the most recent achievement earned by this sales agent
        $recentAchievement = LeadAchievement::where('agent_id', Auth::user()->id)
            ->with('details')
            ->orderBy('created_at', 'DESC')
            ->first();

        if (auth()->user()->targets === null) {
            $callsTarget = 0;
            $appointmentsTarget = 0;
            $signupsTarget = 0;
        } else {
            $callsTarget = auth()->user()->targets->calls;
            $appointmentsTarget = auth()->user()->targets->appointments;
            $signupsTarget = auth()->user()->targets->signups;
        }

        return response()->json([
            'status' => 'success',
            'leads' => $leads,

            'calls_made' => $calls,
            'target_calls' => $callsTarget,

            'appointments_made' => $appointments,
            'target_appointments' => $appointmentsTarget,

            'signups_made' => $signups,
            'target_signups' => $signupsTarget,

            'recent_achievement' => $recentAchievement,
        ]);
    }

    public function fitnessGoals(Request $request)
    {
        $goals = Goal::get();

        return response()->json([
            'status' => 'success',
            'goals' => $goals,
        ]);
    }

    public function fitnessFocuses(Request $request)
    {
        $focuses = Focus::get();

        return response()->json([
            'status' => 'success',
            'focuses' => $focuses,
        ]);
    }


    /**
     * Store a new Lead
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:leads',
            'phone_number' => 'required',
            'dob_year' => [new DateOfBirth],
            'gender' => 'required',
            'source' => 'required',
            'gym_id' => 'required',
            'send_email' => 'required',
        ]);

        /**
         * Format the date of birth.
         */
        $dateOfBirth = request('dob_year') . '-' . request('dob_month') . '-' . request('dob_day');

        $user = User::create([
            'role_id' => 4,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'date_of_birth' => $dateOfBirth,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'password' => Hash::make(\Str::random(100))
            // A random string is assigned to this password as the recipient will recieve an email to confirm account and set a password
        ]);

        $member = Member::updateOrCreate(['user_id' => $user->id], [
            'home_studio_id' => $request->gym_id,
        ]);

        Lead::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'date_of_birth' => $dateOfBirth,
            'phone_number' => $request->phone_number,
            'status' => 'new',
            'assigned_to' => Auth::user()->id,
            'source' => $request->source,
            'gym_id' => $request->gym_id,
        ]);

        if ($request->fitness_goals) {
            UserFocus::where('user_id', $user->id)->delete();
            foreach ($request->fitness_goals as $id) {
                UserFocus::create([
                    'user_id' => $user->id,
                    'focus_id' => $id,
                ]);
            }
        }

        if ($request->send_email == 'yes') {
            $user->sendInvitationEmail();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Retrieve a single Lead
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Request $request, Lead $lead)
    {
        // Reload lead with relationships.
        $lead = Lead::where('id', $lead->id)->with('assigned')->first();

        return response()->json([
            'status' => 'success',
            'lead' => $lead,
        ]);
    }


    /**
     * Load activities for a single lead
     * made this into its own Controller so we can Limit & load more
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activities(Request $request, Lead $lead)
    {
        $request->validate(['limit' => 'required']);

        $activityLog = collect();
        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);

            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            $activities = LeadActivityLog::where('lead_id', $lead->id)
                ->where('created_at', '>=', $startOfMonth)
                ->where('created_at', '<=', $endOfMonth)
                ->with('type')
                ->orderBy('created_at', 'DESC');

            $countActivities = $activities->count();

            $activities = $activities->limit($request->limit)
                ->get();

            if (count($activities) === 0) {
                continue;
            }

            $activityLog->push([
                'date' => $month->format('F Y'),
                'logs' => $activities,
                'total' => $countActivities,
                'current' => count($activities),
            ]);

        }

        return response()->json([
            'status' => 'success',
            'activities' => $activityLog,
        ]);
    }


    /**
     * Fetch notes for a Lead
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadNotes(Request $request, Lead $lead)
    {
        // Return the notes created for this Lead
        $notes = LeadNote::where('lead_id', $lead->id)
            ->with('agent')
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'notes' => $notes,
        ]);
    }

    /**
     * Store a generic note on a Lead Profile
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeNotes(Request $request, Lead $lead)
    {
        // Validate the note content was passed into the request
        $request->validate(['note' => 'required']);

        // Create the model
        LeadNote::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'type' => 'note',
            'content' => $request->note,
        ]);

        // Get the activity type for notes
        $activityType = LeadActivityType::find(4);

        // Log the activity
        LeadActivityLog::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'details' => $activityType->name,
            'extra_details' => Carbon::now()->format('dS F Y \a\t h:iA'),
            'activity_type' => $activityType->id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }


    /**
     * Store a new Call log
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCall(Request $request, Lead $lead)
    {
        // validate that the call outcome exists
        $request->validate([
            'outcome' => 'required',
            'note' => 'required',
        ]);

        $leadNote = null;
        switch ($request->outcome) {
            case 'Still thinking':
                // Validate we have the fields to schedule a new call
                $request->validate([
                    'scheduled_call_date' => 'required',
                    'scheduled_call_time' => 'required',
                ]);

                // parse the date and time fields into Carbon
                $datetime = Carbon::parse($request->scheduled_call_date . ' ' . $request->scheduled_call_time)->format('Y-m-d H:i:s');

                // create a new LeadCallSchedule (for scheduled calls)
                $scheduledCall = LeadCallSchedule::create([
                    'agent_id' => Auth::user()->id,
                    'lead_id' => $lead->id,
                    'datetime' => $datetime,
                ]);

                // If we have a note, create a new model for this
                if ($request->note) {
                    $leadNote = LeadNote::create([
                        'agent_id' => Auth::user()->id,
                        'lead_id' => $lead->id,
                        'type' => 'call',
                        'content' => $request->note,
                    ]);
                }

                // update the temperature of the lead
                $lead->update(['temperature' => 'warm']);

                break;
            case 'Appointment':
                // Validate we have the fields required to set up a new appointment
                $request->validate([
                    'scheduled_call_date' => 'required',
                    'scheduled_call_time' => 'required',
                    'gym_id' => 'required',
                    'appointment_duration' => 'required',
                ]);

                // if we have a note then create the note model here for the appointment
                if ($request->note) {
                    $leadNote = LeadNote::create([
                        'agent_id' => Auth::user()->id,
                        'lead_id' => $lead->id,
                        'type' => 'appointment',
                        'content' => $request->note,
                    ]);
                }

                // Create the new appointment
                $appointment = LeadAppointment::create([
                    'agent_id' => Auth::user()->id,
                    'lead_id' => $lead->id,
                    'datetime' => Carbon::parse($request->scheduled_call_date . ' ' . $request->scheduled_call_time)->format('Y-m-d H:i:s'),
                    'gym_id' => $request->gym_id,
                    'duration' => $request->appointment_duration,
                    'note_id' => $leadNote ? $leadNote->id : null,
                ]);

                // Log the activity
                $activityType = LeadActivityType::find(1);

                LeadActivityLog::create([
                    'agent_id' => Auth::user()->id,
                    'lead_id' => $lead->id,
                    'details' => $activityType->name,
                    'extra_details' => Carbon::parse($appointment->datetime)->format('dS F Y \a\t h:iA'),
                    'activity_type' => $activityType->id,
                    'note_id' => $leadNote ? $leadNote->id : null,
                ]);

                // Update the temperature for the Lead
                $lead->update(['temperature' => 'hot']);

                break;
            case 'Not interested':
                // The lead is not interested so lets make them cold
                $lead->update(['temperature' => 'cold', 'interested' => 0]);
            default:

                // for the default now we create a note on the call if it exists
                if ($request->note) {
                    $leadNote = LeadNote::create([
                        'agent_id' => Auth::user()->id,
                        'lead_id' => $lead->id,
                        'type' => 'call',
                        'content' => $request->note,
                    ]);
                }

                break;
        }

        // Handle the subscription choices

        if ($request->subscribe_weekly) {
            $lead->subscribeWeekly();
        }

        if ($request->subscribe_monthly) {
            $lead->subscribeMonthly();
        }

        if ($request->unsubscribe) {
            $lead->unsubscribe();
        }

        // Save the lead
        $lead->save();

        if ($request->schedule_id) {
            // if we have a schedule_id on the Request
            // then update the LeadCallSchedule so the outcome is reflected within Day Planner
            LeadCallSchedule::find($request->schedule_id)->update(['outcome' => $request->outcome]);
        }

        // Create the Call log
        $leadCall = LeadCall::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'outcome' => $request->outcome,
            'note_id' => $leadNote ? $leadNote->id : null,
            'datetime' => $request->datetime,
        ]);

        // Log the activity for creating this call
        $activityType = LeadActivityType::find(2);

        LeadActivityLog::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'details' => $activityType->name,
            'extra_details' => $request->datetime ? Carbon::parse($request->datetime)->format('dS F Y \a\t h:iA') : Carbon::now()->format('dS F Y \a\t h:iA'),
            'activity_type' => $activityType->id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Fetch the calls made to this Lead
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadCalls(Request $request, Lead $lead)
    {
        $calls = LeadCall::where('lead_id', $lead->id)
            ->with('agent')
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'calls' => $calls,
        ]);
    }

    /**
     * Fetch the Email activities
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadEmails(Request $request, Lead $lead)
    {
        $emails = LeadActivityLog::where('activity_type', 3)
            ->where('lead_id', $lead->id)
            ->with('agent')
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'emails' => $emails,
        ]);
    }

    /**
     * Fetch the appointments for this Lead
     *
     * @param Request $request
     * @param Lead $lead
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadAppointments(Request $request, Lead $lead)
    {
        $appointments = LeadAppointment::where('lead_id', $lead->id)
            ->with(['agent', 'gym'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'appointments' => $appointments,
        ]);
    }


    /**
     * Return Sales Agent data for the day planner
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dayPlanner(Request $request)
    {
        $request->validate([
            'selected_date' => 'required',
            'filter_appointment' => 'required',
            'filter_followup' => 'required',
            'filter_newlead' => 'required',
            'sort.field' => 'required',
            'sort.order' => 'required',
        ]);

        // Get the currently authenticated user
        $agent = Auth::user();

        $selectedDate = Carbon::parse($request->selected_date);
        // Get the date filters for start and end of day
        $startOfDay = Carbon::parse($request->selected_date)->startOfDay();
        $endOfDay = Carbon::parse($request->selected_date)->endOfDay();

        // Get the date filters for the start and end of the month
        $startOfMonth = Carbon::parse($request->selected_date)->startOfMonth();
        $endOfMonth = Carbon::parse($request->selected_date)->endOfMonth();

        // We create a collection for "Tasks" as we want it to contain different types of models
        $tasks = collect();

        $scheduledCalls = [];
        $monthlyCallDates = [];
        if ($request->filter_followup) {
            $scheduledCalls = LeadCallSchedule::where('agent_id', $agent->id)
                ->where('datetime', '>=', $startOfMonth)
                ->where('datetime', '<=', $endOfMonth)
                ->with('lead')
                ->get();

            $monthlyCallDates = $scheduledCalls->pluck('datetime');
        }

        $scheduledAppointments = [];
        $monthlyAppointmentDates = [];
        if ($request->filter_appointment) {
            $scheduledAppointments = LeadAppointment::where('agent_id', $agent->id)
                ->where('datetime', '>=', $startOfMonth)
                ->where('datetime', '<=', $endOfMonth)
                ->with('lead')
                ->get();

            $monthlyAppointmentDates = $scheduledAppointments->pluck('datetime');
        }

        $newLeads = [];
        $monthlyLeadDates = [];
        if ($request->filter_newlead) {
            $newLeads = Lead::where('assigned_to', $agent->id)
                ->where('created_at', '>=', $startOfMonth)
                ->where('created_at', '<=', $endOfMonth)
                ->get();

            $monthlyLeadDates = $newLeads->pluck('datetime');
        }

        // Merge the Call Schedule, Appointments and New leads into a collection of "tasks"
        // These will be displayed within a list on the front end
        $tasks = $tasks->merge($scheduledCalls);
        $tasks = $tasks->merge($scheduledAppointments);
        $tasks = $tasks->merge($newLeads);

        // Create an array containing the dates where tasks exist in the current month
        $monthlyDates = $tasks->pluck('datetime');

        // We want to filter the tasks to only show the current day...
        // we loaded the whole month so we can pluck the datetimes
        // to be used within the calendar component
        $tasks = $tasks->filter(function ($item, $key) use ($startOfDay, $endOfDay) {
            return $item['datetime'] >= $startOfDay && $item['datetime'] <= $endOfDay;
        });

        // Now we want to sort them in descending order of datetime.. unless we have passed a custom sort
        if ($request->sort) {
            switch ($request->sort['order']) {
                case 'ASC':
                    $tasks = $tasks->sortBy($request->sort['field'])->values()->all();
                    break;
                default:
                    $tasks = $tasks->sortByDesc($request->sort['field'])->values()->all();
                    break;
            }
        }

        return response()->json([
            'status' => 'success',
            'month_schedule' => $monthlyDates,
            'selected_date' => $selectedDate->format('Y-m-d'),
            'selected_date_human' => $selectedDate->format('jS F Y'),
            'tasks' => $tasks,

            'appointment_schedule' => $monthlyAppointmentDates,
            'followup_schedule' => $monthlyCallDates,
            'newlead_schedule' => $monthlyLeadDates,
        ]);
    }

    public function storeAppointment(Request $request, Lead $lead): JsonResponse
    {
        // Validate we have all of the required fields for creating an appointment
        $request->validate([
            'gym_id' => 'required',
            'scheduled_date' => 'required',
            'scheduled_time' => 'required',
            'duration' => 'required',
            'guide' => 'required',
        ]);

        // Check we have a note, if so then create the model
        // and store the ID on the appointment model
        $leadNote = null;
        if ($request->note) {
            $leadNote = LeadNote::create([
                'agent_id' => Auth::user()->id,
                'lead_id' => $lead->id,
                'type' => 'appointment',
                'content' => $request->note,
            ]);
        }

        $appointment = LeadAppointment::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'gym_id' => $request->gym_id,
            'datetime' => Carbon::parse($request->scheduled_date . ' ' . $request->scheduled_time)->format('Y-m-d H:i:s'),
            'duration' => $request->duration,
            'note_id' => $leadNote ? $leadNote->id : null,
        ]);

        // Log the activity for creating a new appointment
        $activityType = LeadActivityType::find(1);

        LeadActivityLog::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'details' => $activityType->name,
            'extra_details' => Carbon::parse($appointment->datetime)->format('dS F Y \a\t h:iA'),
            'activity_type' => $activityType->id,
            'note_id' => $leadNote ? $leadNote->id : null,
        ]);

        $lead->update(['temperature' => 'hot']);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function updateAppointment(Request $request, LeadAppointment $appointment): JsonResponse
    {
        // ASK STEPHEN WHY DATETIME FIELD WAS GIVEN AN ON UPDATE SET TIMESTAMP

        $request->validate([
            'outcome' => 'required',
            'show' => 'required',
        ]);

        if ($request->outcome === 'Not interested') {
            $request->validate([
                'reason' => 'required',
            ]);
        }

        $leadNote = null;
        if ($request->note) {
            $leadNote = LeadNote::create([
                'agent_id' => Auth::user()->id,
                'lead_id' => $appointment->lead_id,
                'type' => 'appointment',
                'content' => $request->note,
            ]);
        }

        $appointment->update([
            'outcome' => $request->outcome,
            'outcome_reason' => $request->reason,
            'show' => $request->show === 'Yes' ? 1 : 0,
            'note_id' => $leadNote ? $leadNote->id : null,
        ]);

        if ($request->scheduled_call_date && $request->scheduled_call_time) {
            // parse the date and time fields into Carbon
            $datetime = Carbon::parse($request->scheduled_call_date . ' ' . $request->scheduled_call_time)->format('Y-m-d H:i:s');

            // create a new LeadCallSchedule (for scheduled calls)
            $scheduledCall = LeadCallSchedule::create([
                'agent_id' => Auth::user()->id,
                'lead_id' => $appointment->lead_id,
                'datetime' => $datetime,
            ]);
        }

        // Handle the subscription choices

        if ($request->subscribe_weekly) {
            $appointment->lead->subscribeWeekly();
        }

        if ($request->subscribe_monthly) {
            $appointment->lead->subscribeMonthly();
        }

        if ($request->unsubscribe) {
            $appointment->lead->unsubscribe();
        }

        // Save the lead
        $appointment->lead->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function storeSignup(Request $request, Lead $lead): JsonResponse
    {
        $user = User::where('email', $lead->email)->first();

        if ($user === null) {
            $user = User::create([
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'email' => $lead->email,
                'phone_number' => $lead->phone_number,
            ]);
        }

        $lead->update([
            'status' => 'won',
        ]);

        $signup = LeadSignup::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'member_id' => $user->id,
        ]);

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'signup' => $signup,
        ]);
    }

    public function uploadContract(Request $request, LeadSignup $signup): JsonResponse
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {
                /**
                 * Upload the file to the live classes images folder in AWS.
                 */
                $file_path = Storage::disk('s3')->putFile('signup/contracts', $file, 'public');

                $user = User::where('id', $signup->member_id)->first();

                if ($user !== null) {
                    $memberProfile = Member::where('user_id', $user->id)->first();

                    if ($memberProfile === null) {
                        $member = Member::create([
                            'user_id' => $user->id,
                        ]);
                    }

                    $member->update([
                        'contract_path' => $file_path,
                    ]);
                }
            }
        }

        return response()->json($signup);
    }

    public function leadReassignableAgents(Lead $lead): JsonResponse
    {
        $agents = User::where('is_sales_agent', 1)->where('id', '!=', $lead->assigned_to)->get();

        return response()->json($agents);
    }

    public function singleNote(Request $request, LeadNote $note): JsonResponse
    {
        return response()->json([
            'note' => $note,
        ]);
    }

    public function reassignLead(Lead $lead): JsonResponse
    {
        $lead->update([
            'assigned_to' => request('agent_id'),
        ]);

        // AdminNotification::create([
        //     'admin_to_id' => request('agent_id'),
        //     'admin_from_id' => auth()->user()->id,
        //     'record_id' => $lead->id,
        //     'record_type' => 'App\Models\Lead',
        //     'title' => 'Lead Reassigned',
        //     'message' => auth()->user()->full_name . ' has reassigned a lead to you'
        // ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function update(Request $request, Lead $lead): JsonResponse
    {
        if ($request->has('email')) {
            $request->validate([
                'email' => 'email:rfc,dns|unique:leads',
            ]);
        }

        if ($request->fitness_goal) {
            UserFocus::where('user_id', $lead->user_id)->delete();
            foreach ($request->fitness_goal as $id) {
                UserFocus::create([
                    'user_id' => $lead->user_id,
                    'focus_id' => $id,
                ]);
            }
        }

        $lead->update($request->all());

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function sendEmail(Request $request, Lead $lead): JsonResponse
    {
        $request->validate([
            'from_address' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        SendEmailJob::dispatch($lead, 'emails.user.lead', $request->subject, ['message' => $request->message],
            $request->from_address);

        $activityType = LeadActivityType::find(3);

        LeadActivityLog::create([
            'agent_id' => Auth::user()->id,
            'lead_id' => $lead->id,
            'details' => $activityType->name,
            'extra_details' => $request->message,
            'activity_type' => $activityType->id,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }
}
