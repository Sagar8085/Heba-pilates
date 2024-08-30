<?php

use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeadAdminController;

Route::group(['prefix' => 'leads'], function() {
    Route::post('/', [LeadController::class, 'dashboard']);
    Route::post('new', [LeadController::class, 'store']);
    Route::post('day-planner', [LeadController::class, 'dayPlanner']);

    Route::get('goals', [LeadController::class, 'fitnessGoals']);
    Route::get('focuses', [LeadController::class, 'fitnessFocuses']);
    Route::post('/update-appointment/{appointment}', [LeadController::class, 'updateAppointment']);
    Route::get('/notes/{note}', [LeadController::class, 'singleNote']);

    Route::group(['prefix' => '{lead}'], function() {
        Route::get('/', [LeadController::class, 'single']);
        Route::get('/appointments', [LeadController::class, 'leadAppointments']);
        Route::get('/calls', [LeadController::class, 'leadCalls']);
        Route::get('/emails', [LeadController::class, 'leadEmails']);
        Route::get('/notes', [LeadController::class, 'leadNotes']);

        Route::post('/notes', [LeadController::class, 'storeNotes']);
        Route::post('/activities', [LeadController::class, 'activities']);
        Route::post('/log-call', [LeadController::class, 'storeCall']);
        Route::post('/appointment', [LeadController::class, 'storeAppointment']);
        Route::post('/signup', [LeadController::class, 'storeSignup']);
        Route::post('/save', [LeadController::class, 'update']);
        Route::post('/email', [LeadController::class, 'sendEmail']);

        Route::get('/reassignable-agents', [LeadController::class, 'leadReassignableAgents']);
        Route::patch('/reassign', [LeadController::class, 'reassignLead']);
    });

    Route::post('/{signup}/upload-contract', [LeadController::class, 'uploadContract']);

    Route::group(['prefix' => 'manage'], function() {

        /**
         * List of all leads to manage.
         */
        Route::get('leads', [LeadAdminController::class, 'leads']);

        /**
         * Team performance stats (Calls, Appointments, Signups, Close Ratio)
         * @param none
         */
        Route::get('team/performance', [LeadAdminController::class, 'teamPerformance']);

        /**
         * Team Dashboard leads recently signed up.
         * @param none
         */
        Route::get('team/recent-signups', [LeadAdminController::class, 'teamSignups']);

        /**
         * Team Dashboard new leads vs contacted leads graph.
         * @param none
         */
        Route::get('team/new-vs-contacted', [LeadAdminController::class, 'newVsContacted']);

        /**
         * List of all sales agents.
         * @param none
         */
        Route::get('agents', [LeadAdminController::class, 'agents']);

        /**
         * Create a new sales agent.
         * @param none
         */
        Route::post('agents', [LeadAdminController::class, 'storeAgent']);

        /**
         * Load sales agent profile.
         * @param Admin $agent
         */
        Route::get('agents/{agent}', [LeadAdminController::class, 'agentProfile']);

        /**
         * Agent performance stats (Calls, Appointments, Signups, Close Ratio)
         * @param Admin $agent
         */
        Route::get('agents/{agent}/performance', [LeadAdminController::class, 'agentPerformance']);

        /**
         * Leads assigned to a specific agent.
         * @param Admin $agent
         */
        Route::get('agents/{agent}/leads', [LeadAdminController::class, 'agentLeads']);

        /**
         * Leads agent has converted into members.
         * @param Agent $agent
         */
        Route::get('agents/{agent}/conversions', [LeadAdminController::class, 'agentConversions']);

        /**
         * Update sales agent targets.
         * @param Agent $agent
         */
        Route::patch('agents/{agent}/targets', [LeadAdminController::class, 'updateAgentTargets']);

        /**
         * Lead distribution - Assign Balance.
         * @param none
         */
        Route::post('assign-balanced', [LeadAdminController::class, 'assignBalanced']);

        /**
         * Lead distribution - Assign Even.
         * @param none
         */
        Route::post('assign-even', [LeadAdminController::class, 'assignEven']);

        /**
         * Graph APIs.
         */
        Route::group(['prefix' => 'graphs'], function() {

            /**
             * Lead Source Distribution by type (Team or Specific Agent)
             * @param string $id
             */
            Route::get('lead-source-distribution/{id}', [LeadAdminController::class, 'leadSourceDistribution']);

            /**
             * Lead KPIs by type (Team or Specific Agent)
             * @param string $id
             */
            Route::get('lead-kpis/{id}', [LeadAdminController::class, 'leadKPIs']);

            /**
             * New Leads by type (Team or Specifc Agent)
             * @param string $id
             */
            Route::get('new-leads/{id}', [LeadAdminController::class, 'newLeadsGraph']);

            /**
             * Converted Leadsby type (Team or Specific Agent)
             * @param string $id
             */
            Route::get('converted-leads/{id}', [LeadAdminController::class, 'convertedLeadsGraph']);

            /**
             * Lead Sources by type (Team or Specific Agent)
             * @param string $id
             */
            Route::get('lead-sources/{id}', [LeadAdminController::class, 'leadSources']);

            /**
             * Presentations (Team or Specific Agent)
             * @param string $id
             */
            Route::get('presentations/{id}', [LeadAdminController::class, 'presentations']);
        });
    });
});
