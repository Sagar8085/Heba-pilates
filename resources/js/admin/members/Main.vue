<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--member">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        Guests
                    </h1>

                    <h2 class="page-header__sub">All Guests</h2>
                </div>

                <div class="page-header__col" v-if="this.authUser.privileges.includes('member-create')">

                    <button @click="displayCreateModal = true" class="button">Create Guest / Lead <i
                        class="material-icons">add_circle</i></button>
                </div>
            </div>
        </section>

        <!-- <section class="memberstab"> -->
        <section class="tab tab--top">
            <div class="wrapper">
                <ul>
                    <li :class="this.tab === 'all' ? 'active' : ''"><a href="#all"
                                                                       @click="tab = 'all', switchTab('all');">All
                        Guests</a></li>
                    <li :class="this.tab === 'assigned' ? 'active' : ''"><a href="#assigned" @click="tab = 'assigned'">Assigned
                        Leads</a></li>
                    <li :class="this.tab === 'unassigned' ? 'active' : ''"><a href="#unassigned"
                                                                              @click="tab = 'unassigned'">Unassigned
                        Leads</a></li>
                    <li :class="this.tab === 'source' ? 'active' : ''"><a href="#source" @click="tab = 'source'">Lead
                        Sources</a></li>
                </ul>
            </div>
        </section>

        <div v-if="tab === 'all' || tab === 'idle' || tab === 'unassigned' || tab === 'assigned'">
            <section class="page-content">
                <div class="filters">
                    <div class="filters__placeholder" @click="toggleFilterDropdown()" v-if="tab === 'all'"><i
                        class="fas fa-filter"></i></div>
                    <div class="filters__placeholder" @click="loadMembers(pagination.current_page)"><i
                        :class="loading ? 'fas fa-sync-alt fa-spin' : 'fas fa-sync-alt'"></i></div>
                    <div class="filters__placeholder" @click="downloadGuests"><i
                        :class="loading ? 'fas fa-cloud-download-alt' : 'fas fa-cloud-download-alt'"></i></div>

                    <div :class="filterDropdown ? 'filters__dropdown filters__dropdown--active' : 'filters__dropdown'">
                        <div class="row">
                            <div class="twelve columns">
                                <h3>Profile Creation Date</h3>
                                <div class="row">
                                    <div class="four columns">
                                        <div class="filters__dropdown-item">
                                            <label for="creationDateAll">All time</label>
                                            <input id="creationDateAll" type="checkbox" value=""
                                                   v-model="creationDateAll">
                                        </div>
                                    </div>

                                    <div class="four columns" :class="creationDateAll ? 'form-element--blur' : ''">
                                        <datepicker :value="start_date" @selected="this.select_start"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>

                                    <div class="four columns" :class="creationDateAll ? 'form-element--blur' : ''">
                                        <datepicker :value="end_date" @selected="this.select_end"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>
                                </div>

                                <h3>Age Brackets</h3>
                                <multiselect v-model="selectedAgeBrackets" :options="ageBrackets" :multiple="true"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by age bracket" label="name" track-by="slug"
                                             :preselect-first="false"></multiselect>

                                <br>

                                <h3>Gender</h3>
                                <multiselect v-model="selectedGenders" :options="genders" :multiple="true"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by gender" label="name" track-by="slug"
                                             :preselect-first="false"></multiselect>
                                <br>

                                <h3>Subscription Status <i class="fas fa-info-circle info-alert"
                                                           @click="showAlert('Filtering by an Expired or Deleted Subscription Status while also filtering Subscription Types will cause unexpected or in-correct results.')"></i>
                                </h3>
                                <multiselect v-model="selectedSubscriptionStatuses" :options="subscriptionStatuses"
                                             :multiple="true" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by subscription status"
                                             label="name" track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Subscription Type</h3>

                                <div class="row">
                                    <div class="eight columns">
                                        <multiselect v-model="selectedMembershipTypes" :options="membershipTypes"
                                                     :multiple="true" :close-on-select="true" :clear-on-select="false"
                                                     :preserve-search="true" placeholder="Filter by subscription type"
                                                     label="name" track-by="slug"
                                                     :preselect-first="false"></multiselect>
                                    </div>

                                    <div class="four columns"
                                         :class="selectedMembershipTypes.length == 0 ? 'form-element--blur' : ''">
                                        <multiselect v-model="selectedMembershipExpired" :searchable="false"
                                                     :show-labels="false" :allow-empty="false" :options="expiredTypes"
                                                     :multiple="false" :close-on-select="true" :clear-on-select="false"
                                                     :preserve-search="true" placeholder="Filter by expiry status"
                                                     label="name" track-by="name"
                                                     :preselect-first="false"></multiselect>
                                    </div>
                                </div>

                                <!-- <h3>Available Membership Credits (IN DEV)</h3>
                                <multiselect v-model="selectedAvailableMembershipCredits" :options="availableMembershipCredits" :multiple="false" :close-on-select="true" :clear-on-select="false" :preserve-search="true" placeholder="Filter by available membership credits" label="name" track-by="name" :preselect-first="false"></multiselect>

                                <br> -->

                                <h3>Credit Pack Type</h3>
                                <div class="row">
                                    <div class="eight columns">
                                        <multiselect v-model="selectedCreditPackTypes" :options="creditPackTypes"
                                                     :multiple="true" :close-on-select="true" :clear-on-select="false"
                                                     :preserve-search="true" placeholder="Filter by credit pack type"
                                                     label="name" track-by="name"
                                                     :preselect-first="false"></multiselect>
                                    </div>

                                    <div class="four columns"
                                         :class="selectedCreditPackTypes.length == 0 ? 'form-element--blur' : ''">
                                        <multiselect v-model="selectedCreditPackExpired" :searchable="false"
                                                     :show-labels="false" :allow-empty="false" :options="expiredTypes"
                                                     :multiple="false" :close-on-select="true" :clear-on-select="false"
                                                     :preserve-search="true" placeholder="Filter by expiry status"
                                                     label="name" track-by="name"
                                                     :preselect-first="false"></multiselect>
                                    </div>
                                </div>

                                <br>

                                <h3>Guest status</h3>
                                <multiselect v-model="selectedGuestStatus" :options="guestStatusOpt" :multiple="true"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by Guest status" label="name" track-by="name"
                                             :preselect-first="false"></multiselect>

                                <br>

                                <h3>PARQ Status</h3>
                                <multiselect v-model="parqStatus" :options="parqStatuses" :multiple="false"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="false"
                                             placeholder="Filter by PARQ status" label="name" track-by="name"
                                             :preselect-first="false"></multiselect>

                                <br>

                                <h3>Preferred Studio</h3>
                                <div class="row">
                                    <div class="twelve columns">
                                        <multiselect v-if="studios.length > 0" v-model="home_studio" :options="studios"
                                                     :multiple="false" :close-on-select="false" :clear-on-select="false"
                                                     :preserve-search="true" placeholder="Preferred Studio" label="name"
                                                     track-by="id" :preselect-first="false"></multiselect>

                                    </div>
                                </div>

                                <h3>No. Sessions Completed</h3>
                                <multiselect v-model="selectedSessionsCompleted" :options="sessionsCompleted"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true"
                                             placeholder="Filter by number of sessions completed" label="name"
                                             track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Last Visit</h3>
                                <multiselect v-model="selectedLastVisit" :options="lastVisitOptions" :multiple="false"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by last visit" label="name" track-by="name"
                                             :preselect-first="false"></multiselect>

                                <br>

                                <div class="row" v-if="this.selectedLastVisit.value === 'range'">
                                    <div class="six columns"
                                         :class="selectedLastVisit.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :value="lastVisitStartDate"
                                                    @selected="this.selectLastVisitStartDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>

                                    <div class="six columns"
                                         :class="selectedLastVisit.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :value="lastVisitEndDate" @selected="this.selectLastVisitEndDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>
                                </div>

                                <h3>Next Booked Session</h3>
                                <multiselect v-model="selectedNextBookedSession" :options="nextBookedSessionOptions"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by next booked session"
                                             label="name" track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <div class="row" v-if="this.selectedNextBookedSession.value === 'range'">
                                    <div class="six columns"
                                         :class="selectedNextBookedSession.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ to: new Date(Date.now() - 8640000) }"
                                                    :value="nextBookedSessionStartDate"
                                                    @selected="this.selectNextBookedSessionStartDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>

                                    <div class="six columns"
                                         :class="selectedNextBookedSession.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ to: new Date(Date.now() - 8640000) }"
                                                    :value="nextBookedSessionEndDate"
                                                    @selected="this.selectNextBookedSessionEndDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>
                                </div>

                                <h3>Key Focus</h3>
                                <multiselect v-if="allFocuses.length > 0" v-model="selectedFocuses"
                                             :options="allFocuses" :multiple="true" :close-on-select="false"
                                             :clear-on-select="false" :preserve-search="true"
                                             placeholder="Choose some Key Focuses" label="name" track-by="slug"
                                             :preselect-first="false"></multiselect>

                                <br>

                                <h3>Custom Tags</h3>
                                <multiselect v-if="allTags.length > 0" v-model="selectedTags" :options="allTags"
                                             :multiple="true" :close-on-select="false" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Choose some tags" label="name"
                                             track-by="slug" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Exclude Tags</h3>
                                <multiselect v-if="allTags.length > 0" v-model="excludedTags" :options="allTags"
                                             :multiple="true" :close-on-select="false" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Choose some tags to exclude"
                                             label="name" track-by="slug" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Lead Sources</h3>

                                <div class="row">
                                    <div class="twelve columns">
                                        <div class="filters__dropdown-item">
                                            <label for="specificSources">Specific Sources</label>
                                            <input id="specificSources" type="checkbox" value=""
                                                   v-model="specificSources">
                                        </div>
                                    </div>
                                </div>

                                <multiselect v-if="specificSources" v-model="selectedLeadSources"
                                             :options="leadSourceOptions" :multiple="true" :close-on-select="true"
                                             :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by lead source" label="name" track-by="value"
                                             :preselect-first="false"></multiselect>

                                <h3>Average weekly visits</h3>
                                <multiselect v-model="averageVisits" :options="averageVisitOpt" :multiple="false"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by average weekly visits" label="name" track-by="name"
                                             :preselect-first="false"></multiselect>

                                <br>

                                <h3>Total credits</h3>
                                <multiselect v-model="selectedTotalCredits" :options="totalCreditsOpt" :multiple="false"
                                             :close-on-select="true" :clear-on-select="false" :preserve-search="true"
                                             placeholder="Filter by number of total credits" label="name"
                                             track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Credit Expiry</h3>
                                <datepicker
                                    v-model="creditExpiry"
                                    :format="'yyyy-MM-dd'"
                                    clear-button
                                ></datepicker>

                                <br>

                                <h3>Last call date</h3>
                                <multiselect v-model="selectedLastCallDate" :options="lastCallDateOptions"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by last call date" label="name"
                                             track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <div class="row" v-if="this.selectedLastCallDate.value === 'range'">
                                    <div class="six columns"
                                         :class="selectedLastCallDate.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ from: new Date() }" :value="lastCallStartDate"
                                                    @selected="this.selectLastCallStartDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>

                                    <div class="six columns"
                                         :class="selectedLastCallDate.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ from: new Date() }" :value="lastCallEndDate"
                                                    @selected="this.selectLastCallEndDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>
                                </div>

                                <h3>No. Calls Made</h3>
                                <multiselect v-model="selectedNumberOfCallsMade" :options="numberOfCallsMadeOptions"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by number of calls made"
                                             label="name" track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Last email date</h3>
                                <multiselect v-model="selectedLastEmailDate" :options="lastEmailDateOptions"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by last call date" label="name"
                                             track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <div class="row" v-if="this.selectedLastEmailDate.value === 'range'">
                                    <div class="six columns"
                                         :class="selectedLastEmailDate.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ from: new Date() }" :value="lastEmailStartDate"
                                                    @selected="this.selectLastEmailStartDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>

                                    <div class="six columns"
                                         :class="selectedLastEmailDate.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ from: new Date() }" :value="lastEmailEndDate"
                                                    @selected="this.selectLastEmailEndDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>
                                </div>

                                <h3>No. Emails Sent</h3>
                                <multiselect v-model="selectedNumberOfEmailsSent" :options="numberOfEmailsSentOptions"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by number of emails sent"
                                             label="name" track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <h3>Appointment</h3>
                                <multiselect v-model="selectedAppointmentDate" :options="appointmentDateOptions"
                                             :multiple="false" :close-on-select="true" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by last call date" label="name"
                                             track-by="name" :preselect-first="false"></multiselect>

                                <br>

                                <div class="row" v-if="this.selectedAppointmentDate.value === 'range'">
                                    <div class="six columns"
                                         :class="selectedAppointmentDate.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ to: new Date() }" :value="appointmentStartDate"
                                                    @selected="this.selectAppointmentStartDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>

                                    <div class="six columns"
                                         :class="selectedAppointmentDate.value !== 'range' ? 'form-element--blur' : ''">
                                        <datepicker :disabled-dates="{ to: new Date() }" :value="appointmentEndDate"
                                                    @selected="this.selectAppointmentEndDate"
                                                    :format="'yyyy-MM-dd'"></datepicker>
                                    </div>
                                </div>

                                <h3>Lifetime value</h3>
                                <multiselect v-model="selectedLifetimeValue" :options="lifetimeValueOptions"
                                             :multiple="false" :close-on-select="false" :clear-on-select="false"
                                             :preserve-search="true" placeholder="Filter by lifetime value" label="name"
                                             track-by="name" :preselect-first="false"></multiselect>

                                <div class="row">
                                    <div class="six columns">
                                        <button style="margin-top: 1.5rem;" class="button button--full"
                                                @click="filterDropdown = false; loadMembers(1)">Apply Filters
                                        </button>
                                    </div>
                                    <div class="six columns">
                                        <button style="margin-top: 1.5rem;" class="button button--full button--outline"
                                                @click="setDefaultFilters()">Clear Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="members.length > 0">
                    <div class="table-list table-list--top">
                        <div class="table-list__header">
                            <h3><span v-if="pagination.from">{{
                                    pagination.from + ' - ' + pagination.to
                                }} of </span>{{ pagination.total }} {{ tab === 'all' ? 'Guests' : 'Leads' }}</h3>

                            <div class="table-list__header-pagination">
                                Show
                                <span
                                    :class="{
                                        'material-icons-outlined': true,
                                        'disabled': perPage === 25,
                                    }"
                                    style="font-family: revert"
                                    @click="changePerPage(25)"
                                >
                                    25
                                </span>
                                <span
                                    :class="{
                                        'material-icons-outlined': true,
                                        'disabled': perPage === 50,
                                    }"
                                    style="font-family: revert"
                                    @click="changePerPage(50)"
                                >
                                    50
                                </span>
                                <span
                                    :class="{
                                        'material-icons-outlined': true,
                                        'disabled': perPage === 100,
                                    }"
                                    style="font-family: revert"
                                    @click="changePerPage(100)"
                                >
                                    100
                                </span>

                                <span
                                    :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === 1}"
                                    @click="loadMembers((pagination.current_page - 1))">navigate_before</span>
                                <span
                                    :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === pagination.last_page}"
                                    @click="loadMembers((pagination.current_page + 1))">navigate_next</span>
                            </div>
                        </div>
                        <div class="table-list__scroll">
                            <table class="table-list__table">
                                <thead>
                                <tr>
                                    <th
                                        v-for="(column, index) in columns"
                                        :key="index"
                                        @click="sort(column.slug)"
                                        :class="{ 'active': sortField === column.slug }"
                                        class="sortable"
                                    >
                                        <div class="title">
                                            {{ column.name }}
                                            <template v-if="sortField === column.slug">
                                                <i v-if="sortDirection === 'asc'" class="material-icons">expand_less</i>
                                                <i v-else class="material-icons">expand_more</i>
                                            </template>
                                            <i v-else class="material-icons">unfold_more</i>
                                        </div>
                                    </th>
                                    <th v-if="tab === 'unassigned'"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in members" :key="index">
                                    <td>
                                        <router-link :to="'/admin/members/' + item.id + (tab === 'all' ? '' : '#lead')">
                                            {{ item.first_name }}
                                        </router-link>
                                    </td>
                                    <td>
                                        <router-link :to="'/admin/members/' + item.id + (tab === 'all' ? '' : '#lead')">
                                            {{ item.last_name }}
                                        </router-link>
                                    </td>
                                    <td>{{ item.email }}</td>
                                    <td>{{ item.phone_number || '---' }}</td>
                                    <td>{{ item.lead ? item.lead.appointment : '---' }}</td>
                                    <td>{{ item.created_at_human }}</td>
                                    <td>{{ item.gym ? item.gym.name : '---' }}</td>
                                    <td>{{ item.guest_status || '---' }}</td>
                                    <td>{{ item.subscription_status_human || '---' }}</td>
                                    <td>{{ item.subscription ? item.subscription.name : '---' }}</td>
                                    <td>{{
                                            item.credit_pack_purchase ? item.credit_pack_purchase.credit_pack.name : '---'
                                        }}
                                    </td>
                                    <td>{{
                                            item.credit_pack_purchase ? item.credit_pack_purchase.expires_human : '---'
                                        }}
                                    </td>
                                    <td>{{ item.calls_made || 0 }}</td>
                                    <td>{{ item.emails_sent || 0 }}</td>
                                    <td>{{ item.available_credits || 0 }}</td>
                                    <td>{{
                                            item.most_recent_past_reformer_booking ? item.most_recent_past_reformer_booking.formatted_csv_date : '---'
                                        }}
                                    </td>
                                    <td>{{ item.last_call ? item.last_call.datetime_human : '---' }}</td>
                                    <td>{{ item.last_sent_email ? item.last_sent_email.created_human : '---' }}</td>
                                    <td>{{ item.lead ? item.lead.source : '---' }}</td>
                                    <td>&pound;{{ item.total_spend }}</td>
                                    <td>&pound;{{ item.expected_future_value }}</td>
                                    <td>{{ item.parq ? 'Completed' : 'Not Completed' }}</td>
                                    <td>{{
                                            item.next_reformer_booking ? item.next_reformer_booking.formatted_csv_date : '---'
                                        }}
                                    </td>
                                    <td>{{ item.total_studio_visits }}</td>
                                    <td>{{ item.visits_per_week }}</td>
                                    <td>{{ item.gender || '---' }}</td>
                                    <td>{{ item.dob_human || '---' }}</td>
                                    <td>{{ item.age || '---' }}</td>
                                    <td>{{ item.member_profile ? item.member_profile.pilates_experience : '---' }}</td>
                                    <td>{{ item.member_profile ? item.member_profile.fitness_level : '---' }}</td>
                                    <td v-if="tab === 'unassigned'">
                                        <a
                                            href="javascript: void();"
                                            @click="openReassignLeadModal(item.lead)"
                                        >
                                            Assign Lead
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <section class="forbidden forbidden--full" v-if="!this.loading && this.members.length == 0">
                    <div>
                        <img src="/images/illustrations/video-streaming.svg">
                        <h2>No {{ tab === 'all' ? 'Guests' : 'Leads' }} Found</h2>
                        <p>We couldn't find any {{ tab === 'all' ? 'Guests' : 'Leads' }} matching your search
                            criteria.</p>
                    </div>
                </section>
            </section>
        </div>


        <div v-if="tab === 'assignedOLD'">
            <div class="page-content lead-management">
                <div v-if="leads.length > 0">
                    <div class="table-list table-list--top">
                        <div class="table-list__header">
                            <h3>{{ leads.length }} Assigned Leads</h3>
                        </div>
                        <div class="table-list__scroll">
                            <table class="table-list__table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Assigned To</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Calls Made</th>
                                    <th>Appointment</th>
                                    <th>Fitness Goal</th>
                                    <th>Lead Source</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(lead, index) in leads" :key="index">
                                    <td>
                                        <router-link :to="'/admin/members/' + lead.user_id + '#lead'">{{
                                                lead.full_name
                                            }}
                                        </router-link>
                                    </td>
                                    <td>
                                        <router-link :to="'/admin/admins/' + lead.assigned_to">{{
                                                lead.assigned.name
                                            }}
                                        </router-link>
                                    </td>
                                    <td>{{ lead.email }}</td>
                                    <td>{{ lead.phone_number }}</td>
                                    <td>{{ lead.calls_made }}</td>
                                    <td>{{ lead.appointment }}</td>
                                    <td>{{ lead.fitness_goal_human }}</td>
                                    <td>{{ lead.source }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="tab === 'unassignedOLD'">
            <div class="page-content lead-management">
                <div v-if="leads.length > 0">
                    <div class="table-list table-list--top">
                        <div class="table-list__header">
                            <h3>{{ leads.length }} Unassigned Leads</h3>
                        </div>
                        <div class="table-list__scroll">
                            <table class="table-list__table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Fitness Goal</th>
                                    <th>Lead Source</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(lead, index) in leads" :key="index">
                                    <td>
                                        <router-link :to="'/admin/members/' + lead.user_id + '#lead'">{{
                                                lead.full_name
                                            }}
                                        </router-link>
                                    </td>
                                    <td>{{ lead.email }}</td>
                                    <td>{{ lead.phone_number }}</td>
                                    <td>{{ lead.fitness_goal_human }}</td>
                                    <td>{{ lead.source }}</td>
                                    <td><a href="javascript: void();" @click="openReassignLeadModal(lead)">Assign
                                        Lead</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <section class="forbidden" v-else>
                    <img src="/images/member-portal/empty.svg">
                    <h2>No Leads</h2>
                    <p>We don't have any unassigned leads to show you at the moment.</p>
                </section>
            </div>
        </div>

        <div v-if="tab === 'source'" class="wrapper">
            <div class="page-content lead-management">
                <div>
                    <div class="row">
                        <div style="float: right; margin-top: 2rem;">
                            <a :class="['button ', period !== 'today' ? ' button--outline' : 'button']"
                               @click="setPeriod('today')">Today</a>
                            <a :class="['button ', period !== 'week' ? ' button--outline' : 'button']"
                               @click="setPeriod('week')">This Week</a>
                            <a :class="['button ', period !== 'month' ? ' button--outline' : 'button']"
                               @click="setPeriod('month')">This Month</a>
                            <a :class="['button ', period !== 'year' ? ' button--outline' : 'button']"
                               @click="setPeriod('year')">This Year</a>
                        </div>
                    </div>

                    <section class="lead-charts">
                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Lead Source Distribution</label>

                            <div class="lead-chart">
                                <div v-if="this.leadSourceDistributionHasData">
                                    <pie-graph :key="componentKey" v-if="this.leadSourceDistributionChartData !== null"
                                               :data="this.leadSourceDistributionChartData" :isDoughnut="true"
                                               :isLeadManagement="true"></pie-graph>
                                </div>

                                <div class="forbidden forbidden--no-padding" v-else>
                                    <img src="/images/member-portal/empty.svg">
                                    <h2>Not Enough Data</h2>
                                    <p>We don't have enough lead data to show you distribution of leads via source at
                                        the moment.</p>
                                </div>
                            </div>
                        </div>

                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Lead KPIs</label>

                            <div class="lead-chart">
                                <div v-if="this.leadKPIHasData">
                                    <pie-graph :key="componentKey" v-if="this.leadKPIChartData !== null"
                                               :filter="this.filter" :data="this.leadKPIChartData" :isDoughnut="true"
                                               :isLeadManagement="true"></pie-graph>
                                </div>

                                <div class="forbidden forbidden--no-padding" v-else>
                                    <img src="/images/member-portal/empty.svg">
                                    <h2>Not Enough Data</h2>
                                    <p>We don't have enough lead data to show you leads KPIs at the moment.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="lead-charts">
                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Converted Leads</label>

                            <div class="lead-chart">
                                <bar-graph :key="componentKey" v-if="!this.loading" :filter="this.filter"
                                           :data="this.convertedLeadsChartData" :stacked="true"></bar-graph>
                            </div>
                        </div>

                        <div class="lead-charts__col">
                            <label class="lead-charts__label">Lead Sources</label>

                            <div class="lead-chart">
                                <bar-graph :key="componentKey" v-if="this.leadSourcesChartData !== null"
                                           :filter="this.filter" :options="this.leadSourcesOptions"
                                           :data="this.leadSourcesChartData" :stacked="true"></bar-graph>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div v-if="this.displayAddLeadModal === true">
            <add-lead-modal v-on:cancel="displayAddLeadModal = false"></add-lead-modal>
        </div>

        <div class="modal-container" v-if="displayAssignEvenlyModal">
            <div class="sidebar-modal">
                <div class="sidebar-modal__header">
                    <label class="sidebar-modal__header__title">Assign Evenly</label>
                </div>
                <div class="sidebar-modal__body">
                    <div class="sidebar-modal__body__text">
                        Here's how these selected leads will be distributed
                    </div>

                    <div class="sidebar-modal__body__assign-container" v-for="row in assignEvenly" v-bind:key="row.id">
                        <div>
                            <div class="avatar-with-name">
                                <img src="/images/placeholders/taylor.jpg">
                                <span>{{ row.name }}</span>
                            </div>

                            <div class="form-element__control">
                                    <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">Current Leads</div>
                                            {{ row.current_leads }}
                                        </div>
                                    </span>
                                <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">After Assigning</div>
                                            {{ row.total_leads }} <small>(+{{ row.new_leads }})</small>
                                        </div>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-modal__footer">
                    <button class="button" @click="assignLeadsEvenly(true)">Assign Leads</button>
                    <button class="button button--white" @click="displayAssignEvenlyModal = false">Cancel</button>
                </div>
            </div>
        </div>

        <div class="modal-container" v-if="displayAssignBalancedModal">
            <div class="sidebar-modal">
                <div class="sidebar-modal__header">
                    <label class="sidebar-modal__header__title">Assign Balanced</label>
                </div>
                <div class="sidebar-modal__body">
                    <div class="sidebar-modal__body__text">
                        Here's how these selected leads will be distributed
                    </div>

                    <div class="sidebar-modal__body__assign-container" v-for="row in assignBalanced"
                         v-bind:key="row.id">
                        <div>
                            <div class="avatar-with-name">
                                <img src="/images/placeholders/taylor.jpg">
                                <span>{{ row.name }}</span>
                            </div>

                            <div class="form-element__control">
                                    <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">Current Leads</div>
                                            {{ row.current_leads }}
                                        </div>
                                    </span>
                                <span class="form-element__control__assign">
                                        <div>
                                            <div class="form-element__control__assign__text">After Assigning</div>
                                            {{ row.total_leads }} <small>(+{{ row.new_leads }})</small>
                                        </div>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-modal__footer">
                    <button class="button" @click="processAssignBalanced(false)">Assign Leads</button>
                    <button class="button button--white" @click="displayAssignBalancedModal = false">Cancel</button>
                </div>
            </div>
        </div>


        <!-- MODALS -->

        <create-lead-modal v-on:cancel="displayCreateModal = false"
                           v-on:complete="displayCreateModal = false; loadMembers(1)"
                           :class="displayCreateModal ? 'modal modal--active' : 'modal'"/>

        <reassign-modal v-on:cancel="loadLeads(); displayReassignLeadModal = false"
                        v-on:complete="loadLeads(); displayReassignLeadModal = false;"
                        :class="displayReassignLeadModal ? 'modal modal--active' : 'modal'"
                        :full_name="reassigningLead.full_name" :image_url="reassigningLead.image_url"
                        :lead_id="reassigningLead.id"/>

    </div>
</template>

<script>
import axios from 'axios';
import FilterableDataTable from '../layout/FilterableDataTable.vue'
import ProfilePicture from '../../components/ProfilePicture.vue';
import CreateMemberModal from './CreateMemberModal.vue';
import Datepicker from 'vuejs-datepicker';
import FormInput from '../../components/FormInput.vue';
import PieGraph from '../graphs/PieGraph.vue';
import BarGraph from '../graphs/BarGraph.vue';

import ReassignModal from '../leads/ReassignModal.vue';

import CreateLeadModal from '../leads/CreateLeadModal.vue';

import Filter from './MemberFilter.js';
import {
    defaultAgeBrackets,
    defaultGenders,
    defaultSubscriptionTypes,
    defaultExpiredTypes,
    defaultCreditPackTypes,
    defaultSessionsCompleted,
    defaultLastVisitOptions,
    defaultNextBookedSessionOptions,
    defaultAverageVisitOptions,
    defaultTotalCreditOptions,
    defaultGuestStatusOptions,
    defaultParqStatuses,
    defaultSubscriptionStatuses,
    defaultLastCallDateOptions,
    defaultLastEmailDateOptions,
    defaultAppointmentDateOptions,
    defaultLifetimeValueOptions,
    defaultNumberOfCallsMadeOptions,
    defaultNumberOfEmailsSentOptions
} from './MemberFilter.js';
import TheHeader from "../../tablet/layout/TheHeader";

export default {
    props: {
        authUser: Object
    },

    components: {
        TheHeader,
        CreateLeadModal,
        FilterableDataTable,
        ProfilePicture,
        Datepicker,
        CreateMemberModal,
        FormInput,
        ReassignModal,
        BarGraph,
    },
    data() {
        return {
            sortField: null,
            sortDirection: 'asc',
            loading: true,
            downloading: false,
            displayCreateModal: false,
            displayCreateLeadModal: false,
            filterDropdown: false,
            creationDateAll: true,
            member: {},
            studios: {},
            home_studio: '',
            tab: 'all',

            members: [],
            allTags: [],
            selectedTags: [],
            excludedTags: [],
            allFocuses: [],
            selectedFocuses: [],

            pagination: {},
            perPage: 25,

            start_date: '',
            end_date: '',

            // LEADS SPECFIC

            period: 'today',
            componentKey: 1,
            // loading: false,

            // period: '',

            displayAddLeadModal: false,
            agent: {},

            leads: [],


            displayAssignBalancedModal: false,
            displayAssignEvenlyModal: false,

            newLead: {},
            errors: {},
            profile_completion: 52,

            leadSourceDistributionHasData: false,
            leadSourceDistributionChartData: null,

            leadKPIHasData: false,
            leadKPIChartData: null,

            convertedLeadsChartData: {
                labels: ['11/09/2020', '12/09/2020', '13/09/2020', '14/09/2020'],
                datasets: [
                    {
                        label: 'Leads',
                        backgroundColor: ['#3DC4E5', '#3DC4E5', '#3DC4E5', '#3DC4E5', '#3DC4E5', '#3DC4E5'],
                        data: [10, 10, 20, 30, 50, 20,],
                    },
                    {
                        label: 'Converted Leads',
                        backgroundColor: ['#FD7501', '#FD7501', '#FD7501', '#FD7501', '#FD7501', '#FD7501'],
                        data: [5, 10, 15, 20, 50, 10,],
                    }
                ]
            },

            leadSourcesChartData: {
                labels: ['11/09/2020', '12/09/2020', '13/09/2020', '14/09/2020', '15/09/2020', '16/09/2020', '17/09/2020'],
                datasets: [
                    {
                        label: 'Website',
                        backgroundColor: ['#6617ff', '#6617ff', '#6617ff', '#6617ff', '#6617ff', '#6617ff', '#6617ff'],
                        data: [10, 0, 5, 0, 0, 0, 0],
                    },
                    {
                        label: 'Walk-in',
                        backgroundColor: ['#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5', '#3bc4e5'],
                        data: [0, 3, 0, 0, 0, 5, 5],
                    },
                    {
                        label: 'Phone',
                        backgroundColor: ['#ea50de', '#ea50de', '#ea50de', '#ea50de', '#ea50de', '#ea50de', '#ea50de'],
                        data: [25, 40, 5, 5, 10, 5, 20],
                    },
                    {
                        label: 'Referrals',
                        backgroundColor: ['#f67612', '#f67612', '#f67612', '#f67612', '#f67612', '#f67612', '#f67612'],
                        data: [0, 0, 5, 0, 0, 5, 0],
                    },
                    {
                        label: 'Social Media',
                        backgroundColor: ['#f7be03', '#f7be03', '#f7be03', '#f7be03', '#f7be03', '#f7be03', '#f7be03'],
                        data: [15, 0, 15, 30, 15, 15, 0],
                    },
                    {
                        label: 'Outreach',
                        backgroundColor: ['#abe076', '#abe076', '#abe076', '#abe076', '#abe076', '#abe076', '#abe076'],
                        data: [0, 7, 5, 0, 0, 0, 0],
                    },
                    {
                        label: 'Promotion',
                        backgroundColor: ['#eb7171', '#eb7171', '#eb7171', '#eb7171', '#eb7171', '#eb7171', '#eb7171'],
                        data: [0, 0, 0, 0, 76, 26, 10],
                    }
                ]
            },

            leadSourcesOptions: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    labels: {}
                }
            },

            displayReassignLeadModal: false,
            displayLeadReassignedModal: false,
            successMessage: null,
            displaySuccessModal: false,
            reassignTo: {},
            reassignableAgents: [],
            reassigningLead: {},

            assignEvenly: [],
            assignBalanced: [],

            /*
             Load in the default filter options from the imported MemberFile file.
             */
            defaultAgeBrackets,
            defaultGenders,
            defaultSubscriptionTypes,
            defaultExpiredTypes,
            defaultCreditPackTypes,
            defaultSessionsCompleted,
            defaultLastVisitOptions,
            defaultNextBookedSessionOptions,
            defaultAverageVisitOptions,
            defaultTotalCreditOptions,
            defaultGuestStatusOptions,
            defaultParqStatuses,
            defaultSubscriptionStatuses,
            defaultLastCallDateOptions,
            defaultLastEmailDateOptions,
            defaultAppointmentDateOptions,
            defaultLifetimeValueOptions,
            defaultNumberOfCallsMadeOptions,
            defaultNumberOfEmailsSentOptions,

            ageBrackets: [],
            selectedAgeBrackets: [],

            genders: [],
            selectedGenders: [],

            subscriptionStatuses: [],
            selectedSubscriptionStatuses: [],

            membershipTypes: [],
            selectedMembershipTypes: [],

            expiredTypes: [],
            selectedMembershipExpired: {},

            creditPackTypes: [],
            selectedCreditPackTypes: [],
            selectedCreditPackExpired: {},

            availableMembershipCredits: [
                {
                    name: 'Any',
                    value: 'any'
                },
            ],
            selectedAvailableMembershipCredits: {
                name: 'Any',
                value: 'any'
            },

            sessionsCompleted: [],
            selectedSessionsCompleted: {},

            averageVisitOpt: [],
            averageVisits: {},

            totalCreditsOpt: [],
            selectedTotalCredits: {},

            creditExpiry: null,

            guestStatusOpt: [],
            selectedGuestStatus: {},

            parqStatuses: [],
            parqStatus: null,

            lifetimeValueOptions: [],
            selectedLifetimeValue: {},

            lastVisitOptions: [],
            selectedLastVisit: {},

            lastVisitStartDate: null,
            lastVisitEndDate: null,

            nextBookedSessionOptions: [],
            selectedNextBookedSession: {},

            lastCallDateOptions: [],
            selectedLastCallDate: {},

            lastEmailDateOptions: [],
            selectedLastEmailDate: {},

            appointmentDateOptions: [],
            selectedAppointmentDate: {},

            nextBookedSessionStartDate: null,
            nextBookedSessionEndDate: null,

            lastCallStartDate: null,
            lastCallEndDate: null,

            lastEmailStartDate: null,
            lastEmailEndDate: null,

            appointmentStartDate: null,
            appointmentEndDate: null,

            numberOfCallsMadeOptions: [],
            selectedNumberOfCallsMade: {},

            numberOfEmailsSentOptions: [],
            selectedNumberOfEmailsSent: {},

            specificSources: false,
            leadSourceOptions: [
                {
                    name: 'Referral',
                    value: 'referral'
                },
                {
                    name: 'Google',
                    value: 'google'
                },
                {
                    name: 'Social Media',
                    value: 'social-media'
                },
                {
                    name: 'Website',
                    value: 'website'
                },
                {
                    name: 'Leaflet / Flyer',
                    value: 'leaflet-flyer'
                },
                {
                    name: 'Local Events',
                    value: 'local-event'
                },
                {
                    name: 'Local Signs',
                    value: 'local-signs'
                },
                {
                    name: 'Radio',
                    value: 'radio'
                },
                {
                    name: 'TV',
                    value: 'tv'
                },
                {
                    name: 'Returning Guest',
                    value: 'returning-guest'
                }
            ],
            selectedLeadSources: [],
            columns: [
                {
                    'name': 'First Name',
                    'slug': 'first_name'
                },
                {
                    'name': 'Last Name',
                    'slug': 'last_name'
                },
                {
                    'name': 'Email Address',
                    'slug': 'email'
                },
                {
                    'name': 'Phone Number',
                    'slug': 'phone_number'
                },
                {
                    'name': 'Appointment',
                    'slug': 'appointment'
                },
                {
                    'name': 'Profile Registered',
                    'slug': 'profile_registered'
                },
                {
                    'name': 'Hub Studio',
                    'slug': 'hub_studio'
                },
                {
                    'name': 'Guest Status',
                    'slug': 'guest_status'
                },
                {
                    'name': 'Subscription Status',
                    'slug': 'subscription_status'
                },
                {
                    'name': 'Subscription Type',
                    'slug': 'subscription_type'
                },
                {
                    'name': 'Credit Pack Type',
                    'slug': 'credit_pack_type'
                },
                {
                    'name': 'Credit Pack Expiry',
                    'slug': 'credit_pack_expiry'
                },
                {
                    'name': 'No. of Calls Made',
                    'slug': 'no_of_calls_made'
                },
                {
                    'name': 'No. of Emails Sent',
                    'slug': 'no_of_emails_sent'
                },
                {
                    'name': 'Credits Available',
                    'slug': 'credits_available'
                },
                {
                    'name': 'Last Visit',
                    'slug': 'last_visit'
                },
                {
                    'name': 'Last Call Date',
                    'slug': 'last_call_date'
                },
                {
                    'name': 'Last Email Date',
                    'slug': 'last_email_date'
                },
                {
                    'name': 'Lead Source',
                    'slug': 'lead_source'
                },
                {
                    'name': 'Lifetime Value',
                    'slug': 'lifetime_value'
                },
                {
                    'name': 'Expected Future Value',
                    'slug': 'expected_future'
                },
                {
                    'name': 'PARQ',
                    'slug': 'parq_status'
                },
                {
                    'name': 'Next Session',
                    'slug': 'next_session'
                },
                {
                    'name': 'No. of Visits',
                    'slug': 'no_of_visits'
                },
                {
                    'name': 'Visit Frequency',
                    'slug': 'visit_frequency'
                },
                {
                    'name': 'Gender',
                    'slug': 'gender'
                },
                {
                    'name': 'Date of Birth',
                    'slug': 'date_of_birth'
                },
                {
                    'name': 'Age',
                    'slug': 'age'
                },
                {
                    'name': 'Pilates Experience',
                    'slug': 'pilates_experience'
                },
                {
                    'name': 'Fitness Level',
                    'slug': 'fitness_level'
                }
            ],
        }
    },

    mounted() {
        this.setDefaultFilters();

        var date = new Date();
        var month = ("0" + (date.getMonth() + 1)).slice(-2);

        this.start_date = date.getFullYear() + '-' + month + '-01';
        this.end_date = date.getFullYear() + '-' + month + '-' + ("0" + date.getDate()).slice(-2);

        if (this.$route.hash) {
            this.switchTab(this.$route.hash.substring(1));
        }

        this.getStudios();
        this.loadTags();
        this.loadFocuses();
        this.loadMembers(1);


        // LEAD SPECIFIC
        this.period = this.$route.params.period ? this.$route.params.period : 'today';
        this.fetchLeadSourceDistribution();
        this.fetchLeadKPIs();
    },
    watch: {
        '$route.query.keyword': function (id) {
            this.loadMembers(1);
        },

        tab: function (tab) {
            if (tab == 'subscribed') {
                this.loadMembers(1);

            } else if (tab == 'all') {
                this.loadMembers(1);

            } else if (tab == 'credit') {
                this.loadMembers(1);
            } else if (tab == 'unassigned') {
                this.loadMembers(1);
            } else if (tab == 'assigned') {
                this.loadMembers(1);
            }
        }
    },

    methods: {
        sort(field) {
            if (this.sortField == field) {
                if (this.sortDirection == 'asc') {
                    this.sortDirection = 'desc';
                } else {
                    this.sortDirection = 'asc';
                }
            } else {
                this.sortDirection = 'asc';
            }

            this.sortField = field;
            this.loadMembers(1);
        },
        setDefaultFilters() {
            this.creationDateAll = true;

            this.ageBrackets = this.defaultAgeBrackets;
            this.selectedAgeBrackets = [];

            this.genders = this.defaultGenders;
            this.selectedGenders = [];

            this.subscriptionStatuses = this.defaultSubscriptionStatuses;
            this.selectedSubscriptionStatuses = [];

            this.membershipTypes = this.defaultSubscriptionTypes;
            this.selectedMembershipTypes = [];

            this.creditPackTypes = this.defaultCreditPackTypes;
            this.selectedCreditPackTypes = [];

            this.home_studio = '';

            this.sessionsCompleted = this.defaultSessionsCompleted;
            this.selectedSessionsCompleted = {
                name: 'Any',
                value: 'any'
            };

            this.lastVisitOptions = this.defaultLastVisitOptions;
            this.selectedLastVisit = {
                name: 'All Time / Any',
                value: 'any'
            };

            this.nextBookedSessionOptions = this.defaultNextBookedSessionOptions;
            this.selectedNextBookedSession = {
                name: 'All Time / Any',
                value: 'any'
            };

            this.selectedFocuses = [];
            this.selectedTags = [];
            this.excludedTags = [];
            this.specificSources = false;
            this.selectedLeadSources = [];

            this.averageVisitOpt = this.defaultAverageVisitOptions;
            this.averageVisits = {
                name: 'Any',
                value: 'any'
            };

            this.totalCreditsOpt = this.defaultTotalCreditOptions;
            this.selectedTotalCredits = {
                name: 'Any',
                value: 'any'
            };

            this.lastCallDateOptions = this.defaultLastCallDateOptions;
            this.selectedLastCallDate = {
                name: 'All Time / Any',
                value: 'any'
            };

            this.lastEmailDateOptions = this.defaultLastEmailDateOptions;
            this.selectedLastEmailDate = {
                name: 'All Time / Any',
                value: 'any'
            };

            this.appointmentDateOptions = this.defaultAppointmentDateOptions;
            this.selectedAppointmentDate = {
                name: 'All Time / Any',
                value: 'any'
            };

            this.guestStatusOpt = this.defaultGuestStatusOptions;
            this.selectedGuestStatus = [];

            this.parqStatuses = this.defaultParqStatuses;
            this.parqStatus = null;

            this.lifetimeValueOptions = this.defaultLifetimeValueOptions;
            this.selectedLifetimeValue = [];

            this.numberOfCallsMadeOptions = this.defaultNumberOfCallsMadeOptions;
            this.selectedNumberOfCallsMade = [];

            this.numberOfEmailsSentOptions = this.defaultNumberOfEmailsSentOptions;
            this.selectedNumberOfEmailsSent = [];

            this.expiredTypes = this.defaultExpiredTypes;
            this.selectedMembershipExpired = {
                name: 'Active',
                value: 'active'
            };
            this.selectedCreditPackExpired = {
                name: 'Active',
                value: 'active'
            };
        },

        scrollToTop() {
            document.getElementById('app').scrollIntoView();
        },

        loadTags() {
            axios.get('/api/admin/members/tags').then(response => {
                this.allTags = response.data;
            });
        },
        loadFocuses() {
            axios.get('/api/admin/members/focuses').then(response => {
                this.allFocuses = response.data;
            });
        },
        getStudios() {
            axios.get('/api/gyms').then(({data}) => this.studios = data);
        },

        downloadGuests() {
            this.loading = true;

            if (this.downloading) {
                return false;
            }

            axios.get('/api/admin/guest', {
                responseType: 'arraybuffer',
                params: {
                    ages: this.selectedAgeBrackets,
                    genders: this.selectedGenders,
                    subscriptionStatuses: this.selectedSubscriptionStatuses,
                    membershipTypes: this.selectedMembershipTypes,
                    creditPackTypes: this.selectedCreditPackTypes,
                    availableMembershipCredits: this.selectedAvailableMembershipCredits,
                    sessionsCompleted: this.selectedSessionsCompleted,
                    lastVisit: this.selectedLastVisit,
                    lastVisitStartDate: this.lastVisitStartDate,
                    lastVisitEndDate: this.lastVisitEndDate,
                    nextBookedSession: this.selectedNextBookedSession,
                    nextBookedSessionStartDate: this.nextBookedSessionStartDate,
                    nextBookedSessionEndDate: this.nextBookedSessionEndDate,
                    specificSources: this.specificSources,
                    leadSources: this.selectedLeadSources,
                    averageVisits: this.averageVisits,
                    totalCredits: this.selectedTotalCredits,
                    creditExpiry: this.creditExpiry,
                    guestStatus: this.selectedGuestStatus,
                    parqStatus: this.parqStatus,
                    lastCallDate: this.selectedLastCallDate,
                    lastCallStartDate: this.lastCallStartDate,
                    lastCallEndDate: this.lastCallEndDate,
                    lastEmailDate: this.selectedLastEmailDate,
                    lastEmailStartDate: this.lastEmailStartDate,
                    lastEmailEndDate: this.lastEmailEndDate,
                    appointmentDate: this.selectedAppointmentDate,
                    appointmentStartDate: this.appointmentStartDate,
                    appointmentEndDate: this.appointmentEndDate,
                    lifetimeValue: this.selectedLifetimeValue,
                    numberOfCallsMade: this.selectedNumberOfCallsMade,
                    numberOfEmailsSent: this.selectedNumberOfEmailsSent,
                    membershipExpired: this.selectedMembershipExpired,
                    creditPackExpired: this.selectedCreditPackExpired,
                    keywordSearch: this.$route.query.keyword,
                    tab: this.tab,
                    download: true,
                    tags: this.selectedTags,
                    excludedTags: this.excludedTags,
                    focuses: this.selectedFocuses,
                    homeStudio: this.home_studio ? this.home_studio.id : null,
                    purchased: this.tab,
                    creationDateAll: this.creationDateAll,
                    start_date: this.start_date,
                    end_date: this.end_date,
                    sortField: this.sortField,
                    sortDirection: this.sortDirection,
                }
            }).then(response => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'Guest Export.csv');
                document.body.appendChild(fileLink);

                fileLink.click();
                this.downloading = false;
            }).catch(error => {
                if (error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
                .finally(() => this.loading = false);
        },

        loadMembers(page) {
            this.loading = true;

            if (this.downloading) {
                return false;
            }

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/guest?page=' + page, {
                params: {
                    ages: this.selectedAgeBrackets,
                    genders: this.selectedGenders,
                    subscriptionStatuses: this.selectedSubscriptionStatuses,
                    membershipTypes: this.selectedMembershipTypes,
                    creditPackTypes: this.selectedCreditPackTypes,
                    availableMembershipCredits: this.selectedAvailableMembershipCredits,
                    sessionsCompleted: this.selectedSessionsCompleted,
                    lastVisit: this.selectedLastVisit,
                    lastVisitStartDate: this.lastVisitStartDate,
                    lastVisitEndDate: this.lastVisitEndDate,
                    nextBookedSession: this.selectedNextBookedSession,
                    nextBookedSessionStartDate: this.nextBookedSessionStartDate,
                    nextBookedSessionEndDate: this.nextBookedSessionEndDate,
                    specificSources: this.specificSources,
                    leadSources: this.selectedLeadSources,
                    averageVisits: this.averageVisits,
                    totalCredits: this.selectedTotalCredits,
                    creditExpiry: this.creditExpiry,
                    guestStatus: this.selectedGuestStatus,
                    parqStatus: this.parqStatus,
                    lastCallDate: this.selectedLastCallDate,
                    lastCallStartDate: this.lastCallStartDate,
                    lastCallEndDate: this.lastCallEndDate,
                    lastEmailDate: this.selectedLastEmailDate,
                    lastEmailStartDate: this.lastEmailStartDate,
                    lastEmailEndDate: this.lastEmailEndDate,
                    appointmentDate: this.selectedAppointmentDate,
                    appointmentStartDate: this.appointmentStartDate,
                    appointmentEndDate: this.appointmentEndDate,
                    lifetimeValue: this.selectedLifetimeValue,
                    numberOfCallsMade: this.selectedNumberOfCallsMade,
                    numberOfEmailsSent: this.selectedNumberOfEmailsSent,
                    membershipExpired: this.selectedMembershipExpired,
                    creditPackExpired: this.selectedCreditPackExpired,
                    keywordSearch: this.$route.query.keyword,
                    tab: this.tab,
                    tags: this.selectedTags,
                    excludedTags: this.excludedTags,
                    focuses: this.selectedFocuses,
                    homeStudio: this.home_studio ? this.home_studio.id : null,
                    purchased: this.tab,
                    creationDateAll: this.creationDateAll,
                    start_date: this.start_date,
                    end_date: this.end_date,
                    sortField: this.sortField,
                    sortDirection: this.sortDirection,
                    perPage: this.perPage,
                }
            }).then(response => {
                this.members = response.data.data;
                this.pagination = response.data.meta;
            })
                .catch(error => {
                    if (error.response.status === 403) {
                        this.$router.push('/admin/permission-denied');
                    }
                })
                .finally(() => this.loading = false);
        },

        changePerPage(perPage) {
            this.perPage = perPage;
            this.loadMembers(1);
        },

        toggleFilterDropdown() {
            if (this.filterDropdown) {
                this.filterDropdown = false;
            } else {
                this.filterDropdown = true;
            }
        },

        selectLastVisitStartDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.lastVisitStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectLastVisitEndDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.lastVisitEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectNextBookedSessionStartDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.nextBookedSessionStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectNextBookedSessionEndDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.nextBookedSessionEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectLastCallStartDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.lastCallStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectLastCallEndDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.lastCallEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectLastEmailStartDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.lastEmailStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectLastEmailEndDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.lastEmailEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectAppointmentStartDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.appointmentStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectAppointmentEndDate: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.appointmentEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        select_start: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.start_date = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        select_end: function (payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.end_date = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        // LEAD SPECIFIC
        setPeriod(period) {
            this.period = period;
            this.fetchLeadSourceDistribution();
            this.fetchLeadKPIs();
            // this.fetchLeadSources();
        },

        /*
         * Fetch all leads.
         * @param {page | optional}
         */
        loadLeads(page) {
            if (typeof page === 'undefined') {
                page = 1;
            }

            var unassigned = this.tab === 'assigned' ? 'false' : 'true';

            axios.get('/api/admin/leads/manage/leads?unassigned=' + unassigned + '&page=' + page).then(response => {
                this.leads = response.data.data;
                this.pagination = response.data;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        /*
         * Called from the pagination component when user is attempting to go forward or back a page.
         * @param {type} string
         */
        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadLeads((this.pagination.current_page - 1));
            } else {
                this.loadLeads((this.pagination.current_page + 1));
            }
        },

        switchTab(tab) {
            this.tab = tab;

            if (this.tab === 'leads') {

            }

            if (this.tab === 'credit') {
                this.loadMembers(1);
            }

            if (this.tab === 'subscribed') {
                this.loadMembers(1);
            }

            if (this.tab === 'idle') {
                this.loadMembers(1);
            }

            if (this.tab === 'unassigned' || this.href) {
            }

            if (this.tab === 'source') {
            }
        },

        showAddLeadModal() {
            this.displayAddLeadModal = true;
        },

        showAssignBalancedModal() {
            this.displayAssignBalancedModal = true;
            this.processAssignBalanced(true);
        },

        showAssignEvenlyModal() {
            this.displayAssignEvenlyModal = true;
            this.processAssignEvenly(true);
        },

        /*
         * Process leads to assign balacned.
         * @param {dry_run} boolean
         */
        processAssignBalanced(dry_run) {
            axios.post('/api/admin/leads/manage/assign-balanced', {
                dry_run: dry_run
            })
                .then(response => {
                    this.assignBalanced = response.data.dryrun

                    if (dry_run == false) {
                        this.displayAssignBalancedModal = false;
                        this.loadLeads();
                        this.showSuccessModal('Those leads have been assigned!');
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },

        /*
         * Process leads to assign evenly.
         * @param {dry_run} boolean
         */
        processAssignEvenly(dry_run) {
            axios.post('/api/admin/leads/manage/assign-even', {
                dry_run: true
            })
                .then(response => {
                    this.assignEvenly = response.data.dryrun
                })
                .catch(error => {
                    console.error(error);
                });
        },

        hideModals() {
            this.displayAddLeadModal = false;
            this.displayAssignEvenlyModal = false;
        },

        storeLead() {
            axios.post('/api/admin/leads/new', this.newLead)
                .then(response => {
                    this.hideModals();
                })
                .catch(error => {
                    console.error(error);
                    this.errors = error.response.data.errors;
                });
        },

        assignLeadsEvenly() {
            axios.post('/api/admin/leads/manage/assign-even')
                .then(response => {
                    this.hideModals();
                    this.showSuccessModal('Those leads have been assigned!');
                })
                .catch(error => {
                    console.error(error);
                });
        },
        assignLeadsBalanced() {

        },

        showSuccessModal(message) {
            this.successMessage = message;
            this.displaySuccessModal = true;

            $('.feedback-modal').css('right', '-340px');

            $('.feedback-modal').animate({
                right: '50px'
            }, {
                complete: function () {
                    this.displaySuccessModal = false;


                    setTimeout(function () {
                        if (!this.displaySuccessModal) {
                            $('.feedback-modal').animate({
                                right: '-340px'
                            });
                        }
                    }.bind(this), 2000);
                }.bind(this)
            });
        },

        /*
         * Fetch raw API chart data for lead source distribution.
         * Loop through the results and create the chart data object.
         * @param {none}
         */
        fetchLeadSourceDistribution() {
            axios.get('/api/admin/leads/manage/graphs/lead-source-distribution/team?period=' + this.period).then(response => {
                var leadSourceLabels = [];
                var leadSourceDatapoints = [];
                var hasData = false;

                response.data.forEach(function (item) {
                    leadSourceLabels.push(item.source);
                    leadSourceDatapoints.push(item.total);

                    if (item.total > 0) {
                        hasData = true;
                    }
                });

                this.leadSourceDistributionHasData = hasData;
                this.leadSourceDistributionChartData = {
                    labels: leadSourceLabels,
                    datasets: [
                        {
                            label: 'Lead Sources',
                            backgroundColor: ['#a377fa', '#3dc4e5', '#ea51de', '#fd7501', '#f7be02', '#b0e679', '#ec7171'],
                            data: leadSourceDatapoints,
                            pointStyle: 'rectRot',
                            weight: 1,
                            borderWidth: 1
                        }
                    ]
                };

                this.componentKey += 1;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                });
        },

        /*
         * Fetch raw API chart data for lead KPIs.
         * Loop through the results and create the chart data object.
         * @param {none}
         */
        fetchLeadKPIs() {
            axios.get('/api/admin/leads/manage/graphs/lead-kpis/team?period=' + this.period).then(response => {

                if (response.data.calls > 0 || response.data.appointments > 0 || response.data.signups > 0) {
                    this.leadKPIHasData = true;
                }

                this.leadKPIChartData = {
                    labels: ['Calls', 'Appointments', 'Sign Ups'],
                    datasets: [
                        {
                            label: 'Lead KPIs',
                            backgroundColor: ['#3DC4E5', '#FD7501', '#F7BE02'],
                            data: [response.data.calls, response.data.appointments, response.data.signups],
                            pointStyle: 'rectRot',
                            weight: 1,
                            borderWidth: 1
                        }
                    ]
                };

                this.componentKey += 1;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                });
        },

        /*
         * Open the reassign lead modal.
         * @param {lead} Object
         */
        openReassignLeadModal(lead) {
            this.reassigningLead = lead;
            this.displayReassignLeadModal = true;
        },

        /*
         * Select agent for lead to be reassigned to.
         * @param {agent} object
         */
        selectReassignAgent(agent) {
            this.reassignTo = agent;
        },

        /*
         * Reassign lead to a new agent.
         * @param {none}
         */
        reassignLead() {
            axios.patch('/api/admin/leads/' + this.reassigningLead.id + '/reassign', {agent_id: this.reassignTo.id}).then(response => {
                this.displayReassignLeadModal = false;
                this.reassignTo = {};
                this.displayLeadReassignedModal = true;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                });
        },

        showAlert(value) {
            alert(value);
        }
    }
}
</script>

<style scoped lang="scss">
th {
    cursor: pointer;

    &.sortable {
        .title {
            display: flex;
            align-items: center;
        }
    }

    &.active,
    &:hover {
        color: #000000;
    }
}
</style>
