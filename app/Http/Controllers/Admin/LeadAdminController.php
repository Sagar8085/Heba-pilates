<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadAppointment;
use App\Models\LeadCall;
use App\Models\LeadSignup;
use App\Models\LeadTarget;
use App\Models\User;
use App\Rules\DateOfBirth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LeadAdminController extends Controller
{
    //
    public $searchStartDate;
    public $searchEndDate;

    /**
     * Instantiate a new LeadAdminController instance.
     */
    public function __construct()
    {
        $this->setSearchDates();
    }

    /**
     * Set search dates.
     *
     * @param none
     */
    private function setSearchDates()
    {
        $this->searchEndDate = Carbon::now()->format('Y-m-d H:i:s');

        if (request('period') === 'today') {
            $this->searchStartDate = Carbon::now()->format('Y-m-d') . ' 00:00:00';
        } else {
            if (request('period') === 'week') {
                $this->searchStartDate = Carbon::now()->startOfWeek()->format('Y-m-d') . ' 00:00:00';
            } else {
                if (request('period') === 'month') {
                    $this->searchStartDate = Carbon::now()->startOfMonth()->format('Y-m-d') . ' 00:00:00';
                } else {
                    if (request('period') === 'year') {
                        $this->searchStartDate = Carbon::now()->startOfYear()->format('Y-m-d') . ' 00:00:00';
                    } else {
                        $this->searchStartDate = Carbon::now()->startOfMonth();
                        $this->searchEndDate = Carbon::now()->endOfMonth();
                    }
                }
            }
        }
    }

    public function leads(): JsonResponse
    {
        $leads = Lead::orderBy('created_at', 'DESC')
            ->where('interested', 1)
            ->with('assigned');

        if (request('unassigned') === 'true') {
            $leads = $leads->whereNull('assigned_to');
        } else {
            $leads = $leads->whereNotNull('assigned_to');
        }

        $leads = $leads->paginate(25);

        return response()->json($leads);
    }

    public function teamPerformance(): JsonResponse
    {
        $calls = LeadCall::where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        $appointments = LeadAppointment::where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        $signups = LeadSignup::where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        /**
         * Fetch targets for whole team.
         */
        $callsTarget = LeadTarget::sum('calls');
        $appointmentsTarget = LeadTarget::sum('appointments');
        $signupsTarget = LeadTarget::sum('signups');

        return response()->json([
            'calls_made' => $calls,
            'calls_target' => $callsTarget,
            'calls_remaining' => ($callsTarget - $calls),
            'appointments_made' => $appointments,
            'appointments_target' => $appointmentsTarget,
            'appointments_remaining' => ($appointmentsTarget - $appointments),
            'signups_made' => $signups,
            'signups_target' => $signupsTarget,
            'signups_remaining' => ($signupsTarget - $signups),
        ]);
    }

    public function teamSignups(): JsonResponse
    {
        $leads = Lead::join('leads_signups', 'leads_signups.lead_id', 'leads.id')->get();

        return response()->json($leads);
    }

    /**
     * Team Dashboard new leads vs contacted leads graph data.
     *
     * @param none
     *
     * @return Json
     */
    public function newVsContacted()
    {
        $legend = collect();
        $newLeads = collect();
        $contactedLeads = collect();

        /**
         * Loop through the past 7 days and get stats for that specific day.
         */
        for ($i = 1; $i <= 7; $i++) {
            $startDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 23:59:59';

            $legend->push(Carbon::now()->subDays($i)->format('d/m/Y'));

            /**
             * Fetch total number of new leads for date range and add to $newLeads collection.
             */
            $leads = Lead::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count();
            $newLeads->push($leads);

            /**
             * Fetch total number of contacted leads for date range and add to $contactedLeads collection.
             */
            $leads = LeadCall::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count();
            $contactedLeads->push($leads);
        }

        return response()->json([
            'legend' => $legend,
            'new_leads' => $newLeads,
            'contacted_leads' => $contactedLeads,
        ]);
    }

    public function agents(): JsonResponse
    {
        $agents = User::where('is_sales_agent', 1)->get();

        return response()->json($agents);
    }

    public function storeAgent(): JsonResponse
    {
        $this->validate(request(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:administrators',
            'phone_number' => 'required',
            'dob_year' => [new DateOfBirth],
            'gender' => 'required',
            'gym_id' => 'required',
        ]);

        /**
         * Format the date of birth.
         */
        $dateOfBirth = request('dob_year') . '-' . request('dob_month') . '-' . request('dob_day');

        $agent = User::create([
            'first_name' => request('first_name'),
            'last_name' => request('last_name'),
            'email' => request('email'),
            'phone_number' => request('phone_number'),
            'gender' => request('gender'),
            'date_of_birth' => $dateOfBirth,
            'role_id' => 2,
            'is_sales_agent' => 1,
        ]);

        Privilege::create([
            'user_id' => $agent->id,
            'privilege' => 'lead-read',
        ]);

        LeadTarget::create([
            'agent_id' => $agent->id,
        ]);

        $agent->createBearerToken();
        $agent->sendWelcomeEmail();

        return response()->json([
            'status' => 'success',
            'agent' => $agent,
        ]);
    }

    public function agentProfile(User $agent): JsonResponse
    {
        $agent = User::with('targets')->where('id', $agent->id)->first();

        return response()->json($agent);
    }

    public function agentPerformance(User $agent): JsonResponse
    {
        $calls = LeadCall::where('agent_id', $agent->id)
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        $appointments = LeadAppointment::where('agent_id', $agent->id)
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        $signups = LeadSignup::where('agent_id', $agent->id)
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        /**
         * Calculate close ratio.
         */
        $totalLeads = Lead::where('assigned_to', $agent->id)->count();
        $convertedLeads = LeadSignup::where('agent_id', $agent->id)->count();
        $currentCloseRatio = $totalLeads > 0 ? round($convertedLeads / ($totalLeads / 100), 2) : 0;

        if ($agent->targets === null) {
            $callsTarget = 0;
            $appointmentsTarget = 0;
            $signupsTarget = 0;
            $closeRatio = 0;
        } else {
            $callsTarget = $agent->targets->calls;
            $appointmentsTarget = $agent->targets->appointments;
            $signupsTarget = $agent->targets->signups;
            $closeRatio = $agent->targets->close_ratio;
        }

        return response()->json([
            'calls_made' => $calls,
            'calls_target' => $callsTarget,
            'calls_remaining' => ($callsTarget - $calls),
            'appointments_made' => $appointments,
            'appointments_target' => $appointmentsTarget,
            'appointments_remaining' => ($appointmentsTarget - $appointments),
            'signups_made' => $signups,
            'signups_target' => $signupsTarget,
            'signups_remaining' => ($signupsTarget - $signups),
            'close_ratio_current' => round($currentCloseRatio, 1),
            'close_ratio_target' => $closeRatio,
            'close_ratio_remaining' => round($closeRatio - $currentCloseRatio, 1),
        ]);
    }

    public function agentLeads(User $agent): JsonResponse
    {
        $leads = Lead::where('assigned_to', $agent->id)->where('interested', 1)->orderBy('created_at',
            'DESC')->paginate(25);

        return response()->json($leads);
    }

    public function agentConversions(User $agent): JsonResponse
    {
        $leads = Lead::select('leads.*', 'leads_signups.agent_id')
            ->join('leads_signups', 'leads_signups.lead_id', 'leads.id')
            ->where('leads_signups.agent_id', $agent->id)
            ->get();

        return response()->json($leads);
    }

    public function leadSourceDistribution($id): JsonResponse
    {
        $sources = \DB::table('leads')
            ->select('source', \DB::raw('count(*) as total'))
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->groupBy('source');

        /**
         * If a sales agent ID has been passed, used that to filter.
         */
        if ($id !== 'team') {
            $sources = $sources->where('assigned_to', $id);
        }

        $sources = $sources->get();

        return response()->json($sources);
    }

    public function leadKPIs($id): JsonResponse
    {
        $calls = LeadCall::withAgent($id)
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        $appointments = LeadAppointment::withAgent($id)
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();

        $signups = LeadSignup::withAgent($id)
            ->where('created_at', '>=', $this->searchStartDate)
            ->where('created_at', '<=', $this->searchEndDate)
            ->count();


        return response()->json([
            'calls' => $calls,
            'appointments' => $appointments,
            'signups' => $signups,
        ]);
    }

    public function newLeadsGraph($id): JsonResponse
    {
        $legend = collect();
        $newLeads = collect();
        $leadsContacted = collect();

        /**
         * Loop through the past 7 days and add the dates to the legend
         * Also calculated how many New Leads and Contacted Leads there have been for that day.
         */
        for ($i = 0; $i < 7; $i++) {
            $legend->push(Carbon::now()->subDays($i)->format('d/m/Y'));

            $startDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 23:59:59';

            /**
             * Fetch number of new leads into the system for this specific day and append to the collection.
             */
            $newLeadsCount = Lead::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->count();

            $newLeads->push($newLeadsCount);

            /**
             * Fetch number of contacted leads for this specific day and append to the collection.
             */
            $leadsContactedCount = LeadCall::where('datetime', '>=', $startDate)
                ->where('datetime', '<=', $endDate)
                ->count();

            $leadsContacted->push($leadsContactedCount);
        }

        return response()->json([
            'legend' => $legend,
            'new_leads' => $newLeads,
            'leads_contacted' => $leadsContacted,
        ]);
    }

    /**
     * Converted Leads Graph data.
     *
     * @param string $id
     *
     * @return Json
     */
    public function convertedLeadsGraph($id)
    {
        $legend = collect();
        $leadsAssigned = collect();
        $leadsConverted = collect();

        /**
         * Loop through the past 7 days and add the dates to the legend
         * Also calculate how many leads have been assigned and how many have been converted for that day.
         */
        for ($i = 0; $i < 7; $i++) {
            $legend->push(Carbon::now()->subDays($i)->format('d/m/Y'));

            $startDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 00:00:00';
            $endDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 23:59:59';

            /**
             * Fetch number of leads that have been assigned to agents.
             */
            $leadsAssignedCount = Lead::where('assigned_at', '>=', $startDate)
                ->where('assigned_at', '<=', $endDate)
                ->count();

            $leadsAssigned->push($leadsAssignedCount);

            /**
             * Fetch number of leads that have been converted (signed up) in date range.
             */
            $leadsConvertedCount = LeadSignup::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->count();

            $leadsConverted->push($leadsConvertedCount);
        }

        return response()->json([
            'legend' => $legend,
            'leads_assigned' => $leadsAssigned,
            'leads_converted' => $leadsConverted,
        ]);
    }

    /**
     * Bar chart data for Lead Sources over past 7 days.
     *
     * @param String $id
     *
     * @return Json
     */
    public function leadSources($id)
    {
        $legend = collect();
        $datasets = collect();

        /**
         * Loop through the past 7 days and add the dates to the legend
         */
        for ($i = 1; $i <= 7; $i++) {
            $legend->push(Carbon::now()->subDays($i)->format('d/m/Y'));
        }

        /**
         * Loop through each lead source and get the number of leads with that source for each of the 7 days.
         */
        $leadSources = Lead::select('source')->groupBy('source')->get()->pluck('source')->toArray();

        foreach ($leadSources as $source) {
            $sourceData = collect();

            for ($i = 1; $i <= 7; $i++) {
                $startDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 00:00:00';
                $endDate = Carbon::now()->subDays($i)->format('Y-m-d') . ' 23:59:59';

                $count = Lead::where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('source', $source)
                    ->count();

                $sourceData->push($count);
            }

            $datasets->push([
                'label' => $source,
                'backgroundColor' => $this->getSourceBackground($source),
                'data' => $sourceData,
            ]);
        }

        return response()->json([
            'legend' => $legend,
            'sources' => $leadSources,
            'datasets' => $datasets,
        ]);
    }

    /**
     * Presentations graph data.
     *
     * @param String $id
     *
     * @return Json
     */
    public function presentations($id)
    {
        $shows = LeadAppointment::where('show', 1)->count();
        $noshows = LeadAppointment::where('show', 0)->count();

        return response()->json([
            'shows' => $shows,
            'noshows' => $noshows,
        ]);
    }

    /**
     * Get the background colour for a source.
     *
     * @param String $source
     *
     * @return String
     */
    private function getSourceBackground($source)
    {
        if ($source === 'website') {
            return '#6617ff';
        }

        if ($source === 'walk-in') {
            return '#3bc4e5';
        }

        if ($source === 'phone') {
            return '#ea50de';
        }

        if ($source === 'referrals') {
            return '#f67612';
        }

        if ($source === 'facebook') {
            return '#f7be03';
        }

        if ($source === 'outreach') {
            return '#abe076';
        }

        if ($source === 'promotion') {
            return '#eb7171';
        }
    }

    public function updateAgentTargets(User $agent): JsonResponse
    {
        if (!$agent->targets) {
            LeadTarget::create(['agent_id' => $agent->id]);
        }

        if (request('type') == 'Calls') {
            $agent->targets->update([
                'calls' => request('daily'),
            ]);
        }

        if (request('type') == 'Appointments') {
            $agent->targets->update([
                'appointments' => request('daily'),
            ]);
        }

        if (request('type') == 'Sign Ups') {
            $agent->targets->update([
                'signups' => request('daily'),
            ]);
        }

        if (request('type') == 'Close Ratio') {
            $agent->targets->update([
                'close_ratio' => request('daily'),
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function assignBalanced(): JsonResponse
    {
        $dryRun = request('dry_run');

        $unassignedLeads = Lead::whereNull('assigned_to')->get();
        $agents = Admin::where('is_sales_agent', 1)->get();
        $ranLeads = [];

        $dryRunAgents = [];

        /**
         * Loop through all the agents and add them to an array with specific values,
         * We will use this below to figure out which leads to assign to which agent.
         */
        foreach ($agents as $index => $dryRunAgent) {
            $dryRunAgents[$dryRunAgent->id] = [
                'id' => $dryRunAgent->id,
                'name' => $dryRunAgent->full_name,
                'current_leads' => Lead::where('assigned_to', $dryRunAgent->id)->count(),
                'new_leads' => 0,
                'total_leads' => Lead::where('assigned_to', $dryRunAgent->id)->count(),
            ];
        }

        /**
         * Now there might be a couple of leads left over becuase,
         * the chunk splits evenly and there might be an odd number of leads to split.
         * So we'll just assign the remaining leads randomly.
         */
        $remainingLeads = Lead::whereNull('assigned_to')->whereNotIn('id', $ranLeads)->get();

        foreach ($remainingLeads as $lead) {

            /**
             * Randomly select an agent within the array.
             * TODO: Could probably use array_rand here.
             */
            $randomAgentId = array_rand($dryRunAgents);

            $lowestAgent = [];

            /**
             * Which ever agent has the lowest number of new leads, give it to them.
             */
            foreach ($dryRunAgents as $dryRunAgent) {
                if (!isset($lowestAgent['total_leads'])) {
                    $lowestAgent = $dryRunAgent;
                } else {
                    if ($dryRunAgent['total_leads'] < $lowestAgent['total_leads']) {
                        $lowestAgent = $dryRunAgent;
                    }
                }
            }

            /**
             * If we are doing a dry run, just add them to the collection instead of assigning the lead.
             */
            $dryRunAgents[$lowestAgent['id']]['new_leads'] = ($dryRunAgents[$lowestAgent['id']]['new_leads'] + 1);
            $dryRunAgents[$lowestAgent['id']]['total_leads'] = ($dryRunAgents[$lowestAgent['id']]['total_leads'] + 1);

            if (!$dryRun) {
                $lead->update([
                    'assigned_to' => $lowestAgent['id'],
                    'assigned_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'dryrun' => $dryRunAgents,
        ]);
    }

    public function assignEven(): JsonResponse
    {
        $dryRun = request('dry_run');

        $unassignedLeads = Lead::whereNull('assigned_to')->get();
        $agents = Admin::where('is_sales_agent', 1)->get();
        $ranLeads = [];

        $dryRunAgents = [];

        /**
         * Loop through all the agents and add them to an array with specific values,
         * We will use this below to figure out which leads to assign to which agent.
         */
        foreach ($agents as $index => $dryRunAgent) {
            $dryRunAgents[$dryRunAgent->id] = [
                'id' => $dryRunAgent->id,
                'name' => $dryRunAgent->full_name,
                'current_leads' => Lead::where('assigned_to', $dryRunAgent->id)->count(),
                'new_leads' => 0,
                'total_leads' => Lead::where('assigned_to', $dryRunAgent->id)->count(),
            ];
        }

        /**
         * Split the collection of unassigned leads into an EVEN chunk,
         * depending on how many agents there are.
         */
        $leadsChunk = $unassignedLeads->chunk(round($unassignedLeads->count() / $agents->count()));

        foreach ($leadsChunk as $index => $chunk) {

            /**
             * Each chunk will contain an array of leads,
             * So we'll loop through that too.
             */
            foreach ($chunk as $chunkIndex => $lead) {

                /**
                 * We'll use the main chunk index as the index for which agent to pick for these leads.
                 * If the agent exists in the array we'll assign all the leads in this chunk only, to him.
                 */
                if (isset($agents[$index])) {
                    $agent = $agents[$index];

                    /**
                     * If we are doing a dry run, just add them to the collection instead of assigning the lead.
                     */
                    $dryRunAgents[$agent->id]['new_leads'] = ($dryRunAgents[$agent->id]['new_leads'] + 1);
                    $dryRunAgents[$agent->id]['total_leads'] = ($dryRunAgents[$agent->id]['total_leads'] + 1);

                    array_push($ranLeads, $lead->id);

                    if (!$dryRun) {
                        Lead::find($lead->id)->update([
                            'assigned_to' => $agent->id,
                            'assigned_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }

        /**
         * Now there might be a couple of leads left over becuase,
         * the chunk splits evenly and there might be an odd number of leads to split.
         * So we'll just assign the remaining leads randomly.
         */
        $remainingLeads = Lead::whereNull('assigned_to')->whereNotIn('id', $ranLeads)->get();

        foreach ($remainingLeads as $lead) {

            /**
             * Randomly select an agent within the array.
             * TODO: Could probably use array_rand here.
             */
            $randomAgentId = array_rand($dryRunAgents);

            $lowestAgent = [];

            /**
             * Which ever agent has the lowest number of new leads, give it to them.
             */
            foreach ($dryRunAgents as $dryRunAgent) {
                if (!isset($lowestAgent['total_leads'])) {
                    $lowestAgent = $dryRunAgent;
                } else {
                    if ($dryRunAgent['total_leads'] < $lowestAgent['total_leads']) {
                        $lowestAgent = $dryRunAgent;
                    }
                }
            }

            /**
             * If we are doing a dry run, just add them to the collection instead of assigning the lead.
             */
            $dryRunAgents[$lowestAgent['id']]['new_leads'] = ($dryRunAgents[$lowestAgent['id']]['new_leads'] + 1);
            $dryRunAgents[$lowestAgent['id']]['total_leads'] = ($dryRunAgents[$lowestAgent['id']]['total_leads'] + 1);

            if (!$dryRun) {
                $lead->update([
                    'assigned_to' => $lowestAgent['id'],
                    'assigned_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'dryrun' => $dryRunAgents,
        ]);
    }
}
