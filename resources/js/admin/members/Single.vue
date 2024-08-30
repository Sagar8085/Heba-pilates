<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        Guests
                    </h1>
                    <h2 class="page-header__sub">Guest #{{ this.member.id }} - {{ this.member.name }}</h2>
                </div>

                <div class="page-header__col">
                     <button @click="sendEmail()" class="button">
                                  Send welcome email
                        <i class="material-icons"  >send</i>
                    </button>

                    <button @click="displayUpgradeModal = true" class="button"
                            v-if="this.authUser.superadmin && (this.member.role_id !== 1 && this.member.role_id !== 2)">
                        Upgrade to Admin
                        <i class="material-icons">add_circle</i>
                    </button>
                    <!-- <button @click="displayUpgradeModal = true" class="button"
                            v-if="this.authUser.superadmin && (this.member.role_id !== 1 && this.member.role_id !== 2)">
                        Upgrade to Admin
                        <i class="material-icons">add_circle</i>
                    </button> -->
                    <button @click="displayEditModal = true" class="button">Edit Guest<i
                        class="material-icons">edit</i></button>
                </div>
            </div>
        </section>
        <section class="memberstab">
            <div class="wrapper">
                <ul>
                    <li :class="this.tabtwo === 'member' ? 'active' : ''"><a href="#member" @click="tabtwo = 'member'">Guest
                        Information</a></li>
                    <li :class="this.tabtwo === 'lead' ? 'active' : ''"><a href="#lead" @click="tabtwo = 'lead'">Lead
                        Information</a></li>

                </ul>
            </div>
        </section>
        <div v-if="tabtwo === 'member'">
            <section class="page-content" style="padding-bottom: 0;">
                <div class="info info--bottom" v-if="this.$route.query.membership_purchase === 'complete'">
                    <p><strong><i class="fas fa-info-circle"></i> Purchase Complete</strong></p>
                    <p>Membership has now been attached to this members account.</p>
                </div>

                <div class="row">
                    <div class="columns twelve" style="margin-bottom: 0;">
                        <div class="card" style="padding: 1.5rem 1.5rem 0 1.5rem;">
                            <div class="row">
                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Name</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{ member.name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Email</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{ member.email }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Phone Number</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{ member.phone_number }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Membership Type</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static" v-if="member.subscription === null">None -
                                                <a @click="showPurchaseMembershipModal = true">Add Membership</a></p>
                                            <p class="form-element__static" v-else>{{ member.subscription.name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Registered On</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{ member.created_at }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Date of Birth</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{ member.dob_human }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Fitness Level</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{
                                                    member.member_profile ? member.member_profile.fitness_level + ' / 5' : 'Not Supplied'
                                                }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Pilates Experience</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{
                                                    member.member_profile ? member.member_profile.pilates_experience + ' / 5' : 'Not Supplied'
                                                }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Gender</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">{{ member.gender }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Key Focuses</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static" v-for="focus in member.focuses">
                                                {{ focus.name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Home Studio</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">
                                                {{ homeStudioName }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="four columns">
                                    <div class="form-element form-element--read-only">
                                        <span class="form-element__label">Preferred Studio</span>
                                        <div class="form-element__control">
                                            <p class="form-element__static">
                                                {{ preferredStudioName }}
                                            </p>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ memberships.length === 0 ? 'No ' : '' }}Recent Membership History</h3>

                        <router-link class="table-list__header-button"
                                     :to="'/admin/members/' + this.$route.params.id + '/memberships'"
                                     v-if="memberships.length > 0">View All&nbsp;&nbsp;>
                        </router-link>
                        <a class="table-list__header-button" @click="showPurchaseMembershipModal = true"
                           v-if="memberships.length === 0">Add Membership</a>
                    </div>

                    <div class="table-list__scroll" v-if="memberships.length > 0">
                        <table class="table-list__table">
                            <thead>
                            <tr>
                                <th>Tier</th>
                                <th>Online Credits</th>
                                <th>Studio Credits</th>
                                <th>Expires</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(membership, index) in memberships" :key="index">
                                <td>{{ membership.name }}</td>
                                <td>{{ membership.online_credits_human }}</td>
                                <td>{{ membership.studio_credits_human }}</td>
                                <td>{{ membership.expires_human }}</td>
                                <td>
                                    <i class="fas fa-pencil-alt colour--blue cursor--pointer"
                                       @click="editMembership(membership.id)" style="margin-right: 1rem;"></i>
                                    <i class="far fa-trash-alt colour--red cursor--pointer"
                                       @click="confirmDeleteMembership(membership.id, index)"></i>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ creditPacks.length === 0 ? 'No ' : '' }}Recent Credit Pack Purchases</h3>

                        <router-link class="table-list__header-button"
                                     :to="'/admin/members/' + this.$route.params.id + '/credit-packs'"
                                     v-if="creditPacks.length > 0">View All&nbsp;&nbsp;>
                        </router-link>
                        <button class="table-list__header-button" @click="showPurchaseCreditPackModal = true"
                                style="margin-right: 1.5rem;">Add Pack
                        </button>
                    </div>

                    <div class="table-list__scroll" v-if="creditPacks.length > 0">
                        <table class="table-list__table">
                            <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Pack</th>
                                <th>Studio Credits</th>
                                <th>Status</th>
                                <th>Expires</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(creditPack, index) in creditPacks" :key="index"
                                :class="{'strike-through': creditPack.deleted_at === null ? false : true}">
                                <td>
                                    <router-link :to="'/admin/orders/' + creditPack.id">#{{
                                            creditPack.id
                                        }}
                                    </router-link>
                                </td>
                                <td>{{ creditPack.pack.name }}</td>
                                <td>{{ creditPack.studio_credits + ' / ' + creditPack.pack.studio_credits }} Remaining
                                </td>
                                <td><span
                                    :class="{'tag': true, 'tag--green': !creditPack.expired, 'tag--red': creditPack.expired}">{{
                                        creditPack.expired ? 'Expired' : 'Active'
                                    }}</span></td>
                                <td>{{ creditPack.expires_human }}</td>
                                <td v-if="creditPack.deleted_at === null">
                                    <i class="fas fa-pencil-alt colour--blue cursor--pointer"
                                       @click="editCreditPack(creditPack.id, index)" style="margin-right: 1rem;"></i>
                                    <i class="far fa-trash-alt colour--red cursor--pointer"
                                       @click="confirmDeleteCreditPack(creditPack.id, index)"></i>
                                </td>
                                <td v-else>Deleted by {{ creditPack.deleter.name }}<br>{{ creditPack.deleted_at_human }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ reservations.length === 0 ? 'No ' : '' }}Recent Studio Reservations</h3>

                        <router-link class="table-list__header-button"
                                     :to="'/admin/members/' + this.$route.params.id + '/studio-reservations'"
                                     v-if="reservations.length > 0">View All&nbsp;&nbsp;>
                        </router-link>
                    </div>

                    <div class="table-list__scroll" v-if="reservations.length > 0">
                        <table class="table-list__table">
                            <thead>
                            <tr>
                                <th>Gym</th>
                                <th>Machine</th>
                                <th>Booking Date</th>
                                <th>Booking Time</th>
                                <th>Booking Created</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(reservation, index) in reservations" :key="index"
                                :class="{'strike-through': reservation.deleted_at === null ? false : true}">
                                <td>
                                    <router-link :to="'/admin/gyms/' + reservation.gym_id">{{
                                            reservation.gym_name
                                        }}
                                    </router-link>
                                </td>
                                <td>{{ reservation.reformer.name }}</td>
                                <td>{{ reservation.date_human }}</td>
                                <td>{{ reservation.time_human }}</td>
                                <td>{{ reservation.created_at_human }}</td>
                                <td v-if="reservation.deleted_at === null"><i
                                    class="far fa-trash-alt colour--red cursor--pointer"
                                    @click="confirmDeleteBooking(reservation.id, index)"></i></td>
                                <td v-else>Cancelled <span v-if="reservation.deleter && reservation.deleter !== null">by {{
                                        reservation.deleter.name
                                    }}</span><br>{{ reservation.deleted_at_human }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-list table-list--top">
                    <div class="table-list__header">
                        <h3>{{ contracts.length === 0 ? 'No ' : '' }}Contracts</h3>
                        <button class="table-list__header-button" @click="showUploadContractModal = true">Add Contract
                        </button>
                    </div>

                    <div class="table-list__scroll" v-if="contracts.length > 0">
                        <table class="table-list__table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date Uploaded</th>
                                <th>Expires</th>
                                <th>Created by</th>
                                <th></th>
                                <!-- <th></th> -->
                                <th></th>
                            </tr>
                            </thead>

                            <tbody v-if="contracts.length > 0">
                            <tr v-for="(contract, index) in contracts" :key="index">
                                <td>{{ contract.contract.name }}</td>
                                <td>{{ contract.contract.created_human }}</td>
                                <td>{{ contract.contract.expiry_human }}</td>
                                <td>{{ contract.author.first_name }} {{ contract.author.last_name }}</td>
                                <td><a :href="contract.contract.file" target="_blank"><i
                                    class="fas fa-download"></i></a></td>
                                <!-- <td><i class="far fa-edit colour--blue cursor--pointer" @click="editContract(contract.contract.id, index, contract.contract.name, contract.contract.expires)"></i></td> -->
                                <td><i class="fas fa-trash colour--red cursor--pointer"
                                       @click="confirmDeleteContract(contract.contract.id,index)"></i></td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="columns twelve" style="margin-bottom: 0;">
                        <div class="table-list table-list--top">
                            <div class="table-list__header">
                                <h3>PARQ and Health Commitment Statement</h3>
                                <button @click="showParqModal = true" class="table-list__header-button">Add New PARQ
                                </button>
                                <p class="form-element__static" v-if="parqs === null">The member has not yet filled out
                                    this information.</p>
                            </div>

                            <table class="table-list__table">
                                <thead>
                                <tr>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>View</th>

                                    <th></th>

                                </tr>
                                </thead>

                                <tbody v-for="items in parqs">
                                <tr>
                                    <td width="50%">{{ items.parq.created_at_human }}</td>
                                    <td width="30%">{{ items.author }}</td>
                                    <td width="10%">
                                        <i class="fas fa-eye" v-if="!opened.includes(items.parq.id)" title="View"
                                           @click="toggle(items.parq.id)"></i>
                                        <i class="fas fa-eye-slash" v-if="opened.includes(items.parq.id)" title="View"
                                           @click="toggle(items.parq.id)"></i>
                                    </td>

                                    <td width="10%"><i class="fas fa-trash" title="Delete"
                                                       @click="confirmDeleteParq(items.parq.id,index)"></i></td>

                                </tr>
                                <tr v-if="opened.includes(items.parq.id)">
                                    <td colspan="4">
                                        <div class="card" style="padding: 1.5rem 1.5rem 0 1.5rem;">
                                            <div class="row">
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Do you have any current injuries or medical conditions that could have an effect on your exercise participation in any way?</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">
                                                                {{ items.parq.current_injuries ? 'Yes' : 'No' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Additional Details</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">{{
                                                                    items.parq.current_injuries_details ? items.parq.current_injuries_details : 'None Given.'
                                                                }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Are you currently taking any medication or drugs that could have an effect on your exercise participation?</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">
                                                                {{ items.parq.taking_medication ? 'Yes' : 'No' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Additional Details</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">{{
                                                                    items.parq.taking_medication_details ? items.parq.taking_medication_details : 'None Given.'
                                                                }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Have you ever been advised that you should only do physical activity recommended by a doctor? (Reasons may include having a diagnosed heart condition or chronic bone/joint problems).</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">
                                                                {{ items.parq.advised_by_doctor ? 'Yes' : 'No' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Additional Details</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">{{
                                                                    items.parq.advised_by_doctor_details ? items.parq.advised_by_doctor_details : 'None Given.'
                                                                }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Are you currently pregnant or have you been pregnant in the last 3 months.</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">
                                                                {{ items.parq.currently_pregnant ? 'Yes' : 'No' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="six columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Additional Details</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">{{
                                                                    items.parq.currently_pregnant_details ? items.parq.currently_pregnant_details : 'None Given.'
                                                                }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-list__header" v-if="items.parq">
                                            <h3>Emergency Contact</h3>
                                        </div>

                                        <div class="card" style="padding: 0 1.5rem 0 1.5rem;" v-if="items.parq">
                                            <div class="row">
                                                <div class="four columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Name</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">{{
                                                                    items.parq.contact_first_name + ' ' + items.parq.contact_last_name
                                                                }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="four columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Phone Number</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">
                                                                {{ items.parq.contact_phone_number }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="four columns">
                                                    <div class="form-element form-element--read-only">
                                                        <span class="form-element__label">Email Address</span>
                                                        <div class="form-element__control">
                                                            <p class="form-element__static">{{
                                                                    items.parq.contact_email
                                                                }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>

                <div class="row" v-if="this.member.notification_preferences">
                    <div class="columns twelve" style="margin-bottom: 0;">
                        <div class="table-list table-list--top">
                            <div class="table-list__header">
                                <h3>Notification Settings</h3>
                                <p class="form-element__static">You MUST ONLY change these settings if you have been
                                    instructed to by the account holder. Changing these settings without their
                                    permission can be a violation of GDPR practices.</p>
                            </div>

                            <div class="card" style="padding: 0 1.5rem 0 1.5rem;">
                                <div class="row">
                                    <div class="three columns">
                                        <div class="form-element form-element--read-only">
                                            <span
                                                class="form-element__label">Notify me about changes to my account</span>
                                            <div class="form-element__control">
                                                <p class="form-element__static">{{
                                                        this.member.notification_preferences ? (this.member.notification_preferences.account === 1 ? 'Yes' : 'No') : 'No'
                                                    }}</p>
                                            </div>

                                            <button
                                                :class="{'button': true, 'button--red': !this.member.notification_preferences.account, 'button--small': true, 'button--top': true}"
                                                @click="togglePreference('account')">{{
                                                    this.member.notification_preferences.account ? 'Turn Off' : 'Turn On'
                                                }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="three columns">
                                        <div class="form-element form-element--read-only">
                                            <span class="form-element__label">Notify me about new content you think i'd like</span>
                                            <div class="form-element__control">
                                                <p class="form-element__static">{{
                                                        this.member.notification_preferences ? (this.member.notification_preferences.new_content === 1 ? 'Yes' : 'No') : 'No'
                                                    }}</p>
                                            </div>

                                            <button
                                                :class="{'button': true, 'button--red': !this.member.notification_preferences.new_content, 'button--small': true, 'button--top': true}"
                                                @click="togglePreference('new_content')">{{
                                                    this.member.notification_preferences.new_content ? 'Turn Off' : 'Turn On'
                                                }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="three columns">
                                        <div class="form-element form-element--read-only">
                                            <span class="form-element__label">Notify and remind me about my upcoming bookings and cancellations</span>
                                            <div class="form-element__control">
                                                <p class="form-element__static">{{
                                                        this.member.notification_preferences ? (this.member.notification_preferences.bookings === 1 ? 'Yes' : 'No') : 'No'
                                                    }}</p>
                                            </div>

                                            <button
                                                :class="{'button': true, 'button--red': !this.member.notification_preferences.bookings, 'button--small': true, 'button--top': true}"
                                                @click="togglePreference('bookings')">{{
                                                    this.member.notification_preferences.bookings ? 'Turn Off' : 'Turn On'
                                                }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="three columns">
                                        <div class="form-element form-element--read-only">
                                            <span class="form-element__label">Send me marketing emails including tips and offers</span>
                                            <div class="form-element__control">
                                                <p class="form-element__static">{{
                                                        this.member.notification_preferences ? (this.member.notification_preferences.marketing === 1 ? 'Yes' : 'No') : 'No'
                                                    }}</p>
                                            </div>

                                            <button
                                                :class="{'button': true, 'button--red': !this.member.notification_preferences.marketing, 'button--small': true, 'button--top': true}"
                                                @click="togglePreference('marketing')">{{
                                                    this.member.notification_preferences.marketing ? 'Turn Off' : 'Turn On'
                                                }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <tab-list v-model="tab" :tabs="[ 'Studio Access Logs', 'Tagging', 'Notes' ]"/>

            <section class="page-content">
                <div class="row">
                    <div class="columns column--trim-bottom twelve">
                        <section v-if="tab === 'Studio Access Logs'">
                            <section class="forbidden forbidden--no-padding" v-if="doorAccessLog.length === 0">
                                <div>
                                    <img src="/images/illustrations/no-data.svg" class="small">
                                    <h2>No Visits Yet</h2>
                                    <p>Once {{ member.first_name }} visits the studio, a log of their access will appear
                                        here.</p>
                                </div>
                            </section>

                            <div class="table-list table-list--no-top" v-if="doorAccessLog.length > 0">
                                <div class="table-list__scroll">
                                    <table class="table-list__table">
                                        <thead>
                                        <tr>
                                            <th>Entry Time</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <tr v-for="(log, index) in doorAccessLog" :key="index">
                                            <td>{{ log.scanned_at_human }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <section v-if="tab === 'Tagging'">

                            <h2>Modify Existing Tags</h2>
                            <small class="extra-small bottom">Add and remove tags that have been previously
                                used.</small>

                            <div class="row">
                                <div class="ten columns">
                                    <multiselect v-if="allTags.length > 0" v-model="selectedTags" :options="allTags"
                                                 :multiple="true" :close-on-select="false" :clear-on-select="false"
                                                 :preserve-search="true" placeholder="Choose some tags" label="name"
                                                 track-by="slug" :preselect-first="false"></multiselect>
                                </div>

                                <div class="two columns">
                                    <button class="button button--full" @click="saveTags()">Save</button>
                                </div>
                            </div>

                            <hr>

                            <h3>Add New Tag</h3>
                            <small class="extra-small bottom">Add and assign a completely new tag that has never been
                                used before.</small>

                            <div class="row">
                                <div class="ten columns">
                                    <div class="form-element">
                                        <input placeholder="Medical Condition - Bad Back" v-model="newTag">
                                    </div>
                                </div>

                                <div class="two columns">
                                    <button class="button button--full" @click="createTag()">Create</button>
                                </div>
                            </div>
                        </section>

                        <section v-if="tab === 'Notes'">

                            <h2>Member Notes</h2>
                            <small class="extra-small bottom">View notes for Member</small>

                            <div class="row">
                                <div class="ten columns">
                                    <data-table
                                        title="Notes"
                                        :cols="adminsNotesHeadings"
                                        :rows="memberNotes">

                                        <template v-slot:cell-created="{ item, cell }">
                                            {{ item.created_at_human }}
                                        </template>

                                        <template v-slot:cell-author="{ item }">
                                    <span :class="'status status--' + cell">
                                        {{ item.author }}
                                    </span>
                                        </template>

                                        <template v-slot:cell-content="{ item }">
                                            {{ item.content }}
                                        </template>

                                    </data-table>
                                </div>
                            </div>

                            <hr>

                            <h3>Add New Note</h3>


                            <div class="row">
                                <div class="twelve columns">
                                    <div class="form-row">
                                        <form-input v-model="newNote" type="textarea" label=""
                                                    style="padding-top: 10px;"/>
                                    </div>
                                </div>

                                <div class="two columns">
                                    <button class="button button--full" @click="createNote()">Add Note</button>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
        <div v-if="tabtwo === 'lead'">
            <lead-profile-member :authUser="this.authUser" :member="this.member"
                                 :leadID="this.member.lead_id"></lead-profile-member>
        </div>
        <!-- END TABS -->
        <!-- START MODALS -->


        <modal v-model="showParqModal" :size="'large'">
            <template>
                <span class="modal__title">Add New Parq for {{ member.name }}</span>

                <div class="form-element">

                    <span class="form-element__label">Do you have any current injuries or medical conditions that could have an effect on your exercise participation in any way?
                    <span v-if="errors['current_injuries']">{{ errors['current_injuries'][0] }}</span>
                    </span>
                    <div class="form-element__control">
                        <select v-model="parqInjuries" @change="changeSelect($event,'injuries')">
                            <option value="" selected>Please Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div v-if="parqInjuriesMore">
                        <span class="form-element__label">Please Provide Additional Information
                              <span v-if="this.errors['current_injuries_details']">{{
                                      this.errors['current_injuries_details']
                                  }}</span>
                        </span>

                        <div class="form-element__control">
                            <textarea v-model="parqInjuriesExtra" required type='textarea'/>
                        </div>

                    </div>
                </div>

                <div class="form-element">

                    <span class="form-element__label">Are you currently taking any medication or drugs that could have an effect on your exercise participation?
                    <span v-if="this.errors['taking_medication']">{{ this.errors['taking_medication'] }}</span>
                    </span>

                    <div class="form-element__control">
                        <select v-model="parqMedication" @change="changeSelect($event,'medication')">
                            <option value="" selected>Please Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div v-if="parqMedicationMore">
                        <span class="form-element__label">Please Provide Additional Information
                              <span v-if="this.errors['taking_medication_details']">{{
                                      this.errors['taking_medication_details']
                                  }}</span>
                        </span>

                        <div class="form-element__control">
                            <textarea v-model="parqMedicationExtra" required type='textarea'/>
                        </div>

                    </div>
                </div>

                <div class="form-element">

                    <span class="form-element__label">Have you ever been advised that you should only do physical activity recommended by a doctor? (Reasons may include having a diagnosed heart condition or chronic bone/joint problems).
                    <span v-if="this.errors['advised_by_doctor']">{{ this.errors['advised_by_doctor'] }}</span>
                    </span>

                    <div class="form-element__control">
                        <select v-model="parqPhysical" @change="changeSelect($event,'physical')">
                            <option value="" selected>Please Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div v-if="parqPhysicalMore">
                        <span class="form-element__label">Please Provide Additional Information
                              <span v-if="this.errors['advised_by_doctor_details']">{{
                                      this.errors['advised_by_doctor_details']
                                  }}</span>
                        </span>

                        <div class="form-element__control">
                            <textarea v-model="parqPhysicalExtra" required type='textarea'/>
                        </div>

                    </div>
                </div>

                <div class="form-element">

                    <span class="form-element__label">Are you currently pregnant or have you been pregnant in the last 3 months
                    <span v-if="this.errors['currently_pregnant']">{{ this.errors['currently_pregnant'] }}</span>
                    </span>

                    <div class="form-element__control">
                        <select v-model="parqPregnant" @change="changeSelect($event,'pregnant')">
                            <option value="" selected>Please Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div v-if="parqPregnantMore">
                        <span class="form-element__label">Please Provide Additional Information
                              <span v-if="this.errors['currently_pregnant_details']">{{
                                      this.errors['currently_pregnant_details']
                                  }}</span>
                        </span>

                        <div class="form-element__control">
                            <textarea v-model="parqPregnantExtra" required type='textarea'/>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="six columns">
                        <div class="form-element">
                            <div class="form-element__control">
                                <admin-input v-model="parqFirstName" label="First Name" required
                                             :error="this.errors['first_name'] ? this.errors['first_name'] : ''"/>
                            </div>
                            <div class="form-element__control">
                                <admin-input v-model="parqEmail" label="Email Address" type="email" required
                                             :error="this.errors['email'] ? this.errors['email'] : ''"/>
                            </div>

                        </div>
                    </div>
                    <div class="six columns">
                        <div class="form-element">
                            <div class="form-element__control">
                                <admin-input v-model="parqLastName" label="Last Name" required
                                             :error="this.errors['last_name'] ? this.errors['last_name'] : ''"/>
                            </div>
                            <div class="form-element__control">
                                <admin-input v-model="parqPhoneNumber" label="Phone Number" required
                                             :error="this.errors['phone_number'] ? this.errors['phone_number'] : ''"/>
                            </div>
                        </div>
                    </div>
                </div>


                <button slot="buttons" class="button" :disabled="formInvalid" @click="saveParq()">Save</button>
            </template>
        </modal>

        <purchase-membership-modal
            v-model="showPurchaseMembershipModal"
            :member="memberId"
            v-on:created="membershipAdded"
            :available-memberships="availableMemberships"
        />

        <purchase-credit-pack-modal
            v-model="showPurchaseCreditPackModal"
            :member="memberId"
            v-on:added="packAdded"
            :available-packs="availableCreditPacks"
        />

        <modal v-model="showDeleteBookingConfirmationModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Are you sure you wish to delete this booking?</span>

                <div class="modal__buttons">
                    <button class="button" @click="showDeleteBookingConfirmationModal = false">No, Cancel</button>
                    <button class="button button--red" @click="deleteBooking">Yes, Delete</button>
                </div>
            </template>
        </modal>

        <modal v-model="showDeleteMembershipConfirmationModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Are you sure you wish to delete this membership?</span>

                <div class="modal__buttons">
                    <button class="button" @click="showDeleteMembershipConfirmationModal = false">No, Cancel</button>
                    <button class="button button--red" @click="deleteMembership">Yes, Delete</button>
                </div>
            </template>
        </modal>

        <modal v-model="showDeleteCreditPackConfirmationModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Are you sure you wish to delete this credit pack?</span>

                <div class="modal__buttons">
                    <button class="button" @click="showDeleteCreditPackConfirmationModal = false">No, Cancel</button>
                    <button class="button button--red" @click="deleteCreditPack">Yes, Delete</button>
                </div>
            </template>
        </modal>
        <modal v-model="showDeleteParqModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Are you sure you wish to delete this PARQ?</span>

                <div class="modal__buttons">
                    <button class="button" @click="showDeleteParqModal = false">No, Cancel</button>
                    <button class="button button--red" @click="deleteParq">Yes, Delete</button>
                </div>
            </template>
        </modal>

        <edit-member-modal v-on:cancel="displayEditModal = false" :formData="member"
                           v-on:complete="displayEditModal = false; loadMember()"
                           :class="displayEditModal ? 'modal modal--active' : 'modal'" v-model="displayEditModal"/>

        <upgrade-to-admin-modal v-on:cancel="displayUpgradeModal = false" :formData="member"
                                v-on:complete="displayUpgradeModal = false; loadMember()"
                                :class="displayUpgradeModal ? 'modal modal--active' : 'modal'"/>

        <modal v-model="showEditContractModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Edit Contract {{ this.contractName }} </span>
                <div class="form-element">
                    <label class="form-element__label">
                        Expiry Date

                    </label>
                    <div class="form-element__control">
                        <div class="row">
                            <div class="four columns">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <input type="number" required v-model="expires.day" placeholder="25"
                                               maxlength="2">
                                    </div>
                                </div>
                            </div>

                            <div class="four columns">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <select v-model="expires.month">
                                            <option value="">-- Month</option>
                                            <option value="01">Jan</option>
                                            <option value="02">Feb</option>
                                            <option value="03">Mar</option>
                                            <option value="04">Apr</option>
                                            <option value="05">May</option>
                                            <option value="06">Jun</option>
                                            <option value="07">Jul</option>
                                            <option value="08">Aug</option>
                                            <option value="09">Sept</option>
                                            <option value="10">Oct</option>
                                            <option value="11">Nov</option>
                                            <option value="12">Dec</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="four columns">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <select v-model="expires.year">
                                            <option value="">-- Year</option>
                                            <option :value="year" v-for="year in years">{{ year }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal__buttons">
                    <button class="button" @click="showEditContractModal = false">Cancel</button>
                    <button class="button button--red" @click="saveContract()">Save</button>
                </div>
            </template>
        </modal>

        <modal v-model="showUploadContractModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Upload New Contract</span>
                <div class="form-element">
                    <label>Contract name
                        <span v-if="this.errors['name']">{{ this.errors['name'] }}</span>
                    </label>
                    <input type="text" v-model="contractName">
                </div>
                <div class="form-element">
                    <label class="form-element__label">
                        Expiry Date

                    </label>
                    <div class="form-element__control">
                        <div class="row">
                            <div class="four columns">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <input type="number" required v-model="expires.day" placeholder="25"
                                               maxlength="2">
                                    </div>
                                </div>
                            </div>

                            <div class="four columns">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <select v-model="expires.month">
                                            <option value="">-- Month</option>
                                            <option value="01">Jan</option>
                                            <option value="02">Feb</option>
                                            <option value="03">Mar</option>
                                            <option value="04">Apr</option>
                                            <option value="05">May</option>
                                            <option value="06">Jun</option>
                                            <option value="07">Jul</option>
                                            <option value="08">Aug</option>
                                            <option value="09">Sept</option>
                                            <option value="10">Oct</option>
                                            <option value="11">Nov</option>
                                            <option value="12">Dec</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="four columns">
                                <div class="form-element">
                                    <div class="form-element__control">
                                        <select v-model="expires.year">
                                            <option value="">-- Year</option>
                                            <option :value="year" v-for="year in years">{{ year }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-element">
                    <label>Upload Contract
                        <span v-if="this.errors['file']">{{ this.errors['file'] }}</span>
                    </label>
                    <input type="file" id="file" ref="file" v-on:change="handleFileUpload()">
                </div>

                <div class="modal__buttons">
                    <button class="button" @click="showUploadContractModal = false">Cancel</button>
                    <button class="button button--red" :disabled="this.contractName.length == 0"
                            @click="uploadContract()">Save
                    </button>
                </div>
            </template>
        </modal>
        <modal v-model="showDeleteContractModal" hideClose hideCancel>
            <template>
                <span class="modal__title">Are you sure you wish to delete this Contract?</span>

                <div class="modal__buttons">
                    <button class="button" @click="showDeleteContractModal = false">No, Cancel</button>
                    <button class="button button--red" @click="deleteContract">Yes, Delete</button>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import ActivityList from './ActivityList.vue';
import LineGraph from '../graphs/LineGraph.vue';
import TabList from '../layout/TabList.vue';
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import DataTable from '../layout/DataTable.vue';
import EditMemberModal from './EditMemberModal.vue';
import SendNotificationModal from './SendNotificationModal.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';
import Modal from '../../components/Modal.vue';
import AdminInput from '../layout/AdminInput.vue'
import FormInput from '../../components/FormInput.vue';
import UpgradeToAdminModal from './UpgradeToAdminModal.vue';
import LeadProfileMember from '../leads/LeadProfileMember.vue';
import PurchaseMembershipModal from './PurchaseMembershipModal.vue';
import PurchaseCreditPackModal from './PurchaseCreditPackModal.vue'

export default {
    components: {
        ActivityList,
        LineGraph,
        TabList,
        FilterableDataTable,
        EditMemberModal,
        ProfilePicture,
        SendNotificationModal,
        DataTable,
        Modal,
        PurchaseMembershipModal,
        PurchaseCreditPackModal,
        FormInput,
        AdminInput,
        UpgradeToAdminModal,
        LeadProfileMember
    },
    props: {
        authUser: Object
    },
    data() {
        return {
            id: this.$route.params.id,
            tab: 'Studio Access Logs',
            tabtwo: 'member',
            loading: true,
            member: {
                subscription: {},
                notification_preferences: {}
            },
            displayUpgradeModal: false,

            allTags: [],
            selectedTags: [],
            newTag: '',

            activityData: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Activity',
                        borderColor: '#6517FF',
                        backgroundColor: 'rgba(101,23,255, 0.05)',
                        data: [0, 2, 3, 0, 3, 3, 0, 8, 4, 0, 3, 0]
                    }
                ]
            },

            stats: [],

            parqs: [],
            parqsHeadings: {
                created: 'Created at',
                author: 'Created By',
                actions: ''
            },

            purchaseHistoryData: [],
            purchaseHistoryPagination: {},
            purchaseHistoryHeadings: {
                order: 'Order',
                orderable_type_human: 'Type',
                value_human: 'Paid',
                method_human: 'Payment Method',
                created_human: 'Date'
            },
            adminsNotesHeadings: {
                created: 'Created',
                author: 'Author',
                content: 'Content'
            },
            memberNotes: {},
            newNote: '',

            errors: {},
            isActive: false,

            displayEditModal: false,
            showNotificationModal: false,
            showParqModal: false,

            parqInjuriesMore: false,
            parqMedicationMore: false,
            parqPregnantMore: false,
            parqPhysicalMore: false,

            parqInjuries: '',
            parqMedication: '',
            parqPregnant: '',
            parqPhysical: '',
            parqInjuriesExtra: '',
            parqMedicationExtra: '',
            parqPregnantExtra: '',
            parqPhysicalExtra: '',
            parqFirstName: '',
            parqLastName: '',
            parqEmail: '',
            parqPhoneNumber: '',

            notifications: [],
            notificationPagination: {},

            reservations: [],
            memberships: [],
            creditPacks: [],
            contracts: [],

            showPurchaseMembershipModal: false,
            purchaseMembershipTier: '',
            purchaseMembershipType: '',
            availableMemberships: [],

            showPurchaseCreditPackModal: false,
            purchaseCreditPack: '',
            purchaseCreditPackType: '',
            availableCreditPacks: [],

            showDeleteBookingConfirmationModal: false,
            bookingToDelete: null,
            bookingToDeleteRef: null,

            showDeleteMembershipConfirmationModal: false,
            membershipToDelete: null,
            membershipToDeleteRef: null,

            showDeleteCreditPackConfirmationModal: false,
            creditPackToDelete: null,
            creditPackToDeleteRef: null,

            showDeleteContractModal: false,
            contractToDelete: null,
            contractToDeleteRef: null,

            contractID: '',
            showEditContractModal: false,
            contractName: '',
            showUploadContractModal: false,
            contractExpires: '',
            expires: {
                day: '',
                month: '',
                year: '',
            },
            years: '',
            file: '',

            showDeleteParqModal: false,
            parqToDelete: null,
            parqToDeleteRef: null,

            doorAccessLog: [],
            opened: [],


        }
    },

    computed: {
        memberId() {
            return this.$route.params.id
        },

        profileInfo() {
            return [
                {
                    label: 'Email',
                    value: this.member.email
                },
                {
                    label: 'Phone number',
                    value: this.member.phone_number
                },
                {
                    label: 'Type',
                    value: 'Standard Member'
                },
                {
                    label: 'Registered',
                    value: this.formatDate(this.member.created_at)
                }
            ]
        },

        formInvalid() {
            return this.parqFirstName.length === 0
                || this.parqLastName.length === 0
                || this.parqPhoneNumber.length === 0
                || this.parqEmail.length === 0
                || this.parqInjuries.length === 0
                || this.parqMedication.length === 0
                || this.parqPregnant.length === 0
                || this.parqPhysical.length === 0;
        },

        homeStudioName() {
            return this.member?.member_profile?.home_studio?.name ?? 'Not Set';
        },

        preferredStudioName() {
            return this.member?.member_profile?.preferred_studio?.name ?? 'Not Enough Data';
        },
    },

    mounted() {
        this.loadMember()
            .then(
                () => {
                    this.loadCreditPacks(this.member.member_profile.home_studio_id ?? 1);
                    this.loadSubscriptionTiers(this.member.member_profile.home_studio_id ?? 1);
                },
            );

        this.loadMemberStats();
        this.loadPurchaseHistory();
        this.loadTags();
        this.loadMemberTags();
        this.loadReservations();
        this.loadContracts();
        this.loadMembershipHistory();
        this.loadCreditPackPurchases();
        this.loadDoorAccessLog();
        this.loadMemberNotes();
        this.loadParqs();
        const year = new Date().getFullYear();
        this.years = Array.from({length: year - 2000}, (value, index) => 2020 + index);
    },

    methods: {
        /*
         * Fetch the single member on page load.
         * @param {none}
         */

 showSuccessModal(message) {
            this.successMessage = message;
            this.displaySuccessModal = true;

            console.log($('.feedback-modal'));
            $('.feedback-modal').css('right', '-340px');

            $('.feedback-modal').animate({
                right: '50px'
            }, {
                complete: function() {
                    this.displaySuccessModal = false;


                    setTimeout(function() {
                        if(!this.displaySuccessModal) {
                            $('.feedback-modal').animate({
                                right: '-340px'
                            });
                        }
                    }.bind(this), 2000);
                }.bind(this)
            });
        },
        sendEmail(){

            axios.post('https://stage.hebapilates.com/api/send-email', {
                from_address: this.member.email,
                name: this.member.name
                
            })
            .then(response => {
                console.log("email response>>>",response)
                // this.hideModals();
                // this.fetchLeadEmails();
                this.showSuccessModal('Email sent');
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            })


// -----------------



            // console.log("Name>>>>>>>>", this.member.name)
            // console.log("Email>>>>>>>>", this.member.email)
            // axios.post('/api/admin/leads/' + this.leadID + '/email', {
            //     from_address: this.authUser.email,
            //     subject: this.mail.subject,
            //     message: this.mail.message
            // })
            // .then(response => {
            //     this.hideModals();
            //     this.fetchLeadEmails();
            //     this.showSuccessModal('Email sent');
            // })
            // .catch(error => {
            //     console.error(error);
            //     this.errors = error.response.data.errors;
            // })

        },





        loadMember() {
            return axios.get('/api/admin/members/' + this.$route.params.id).then(response => {
                this.member = response.data;

                if (window.location.hash === '#lead') {
                    this.tabtwo = 'lead';
                }
            })
                .catch(error => {
                    if (error.response.status === 403) {
                        this.$router.push('/admin/permission-denied');
                    }
                })
                .finally(() => this.loading = false);
        },

        loadMemberNotes() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/notes').then(response => {
                this.memberNotes = response.data;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                })
                .finally(() => this.loading = false);
        },

        loadParqs() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/parqs').then(response => {
                this.parqs = response.data;

            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                })
                .finally(() => this.loading = false);
        },

        loadMemberTags() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/tags').then(response => {
                this.selectedTags = response.data;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                });
        },

        /*
         * Fetch member profile stats.
         * @param {none}
         */
        loadMemberStats() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/stats').then(response => {
                this.stats = response.data;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                })
                .finally(() => this.loading = false);
        },

        /*
         * Fetch member profile purchase history.
         * @param {none}
         */
        loadPurchaseHistory() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/purchase-history').then(response => {
                this.purchaseHistoryPagination = response.data;
                this.purchaseHistoryData = response.data.data;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                })
                .finally(() => this.loading = false);
        },

        /*
         * Fetch a members notifications.
         * @param {none}
         */
        loadNotifications(page = 1) {
            axios.get('/api/admin/members/' + this.$route.params.id + '/notifications?page=' + page).then(response => {
                this.notifications = response.data.data;
                this.notificationPagination = response.data;
            })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                })
                .finally(() => this.loading = false);
        },

        formatDate(dateString) {
            return moment(dateString).format('DD/MM/YYYY')
        },

        loadTags() {
            axios.get('/api/admin/members/tags').then(response => {
                this.allTags = response.data;
            });
        },

        loadCreditPacks(forGymId) {
            return axios.get(`/api/admin/gyms/${forGymId}/credit-packs`)
                .then(({data}) => this.availableCreditPacks = data);
        },

        loadSubscriptionTiers(forGymId) {
            return axios.get(`/api/admin/gyms/${forGymId}/subscription-tiers`)
                .then(({data}) => this.availableMemberships = data);
        },

        saveTags() {
            axios.patch('/api/admin/members/' + this.$route.params.id + '/tags', {
                tags: this.selectedTags
            }).then(response => {
                alert('Tags Saved');
            });
        },

        createTag() {
            axios.post('/api/admin/members/tags', {
                tag: this.newTag,
                user_id: this.$route.params.id
            }).then(response => {
                this.selectedTags.push(response.data.tag);
                this.newTag = '';
            })
        },

        createNote() {
            axios.post('/api/admin/members/' + this.$route.params.id + '/notes', {
                content: this.newNote,

            }).then(response => {
                this.loadMemberNotes();
                this.newNote = '';
            })
        },

        loadReservations() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/reservations/recent').then(response => {
                this.reservations = response.data;
            });
        },

        loadContracts() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/contracts')
                .then(response => {
                    this.contracts = response.data;
                });
        },

        loadMembershipHistory() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/memberships/recent').then(response => {
                this.memberships = response.data;
            });
        },

        loadCreditPackPurchases() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/credit-packs/recent').then(response => {
                this.creditPacks = response.data;
            });
        },

        membershipAdded() {
            alert('Membership Created');
            this.loadMembershipHistory();
            this.showPurchaseMembershipModal = false;
        },

        packAdded() {
            alert('Credit Pack Created');
            this.loadCreditPackPurchases();
            this.showPurchaseCreditPackModal = false;
        },

        confirmDeleteBooking(booking_id, ref) {
            this.bookingToDelete = booking_id;
            this.bookingToDeleteRef = ref;
            this.showDeleteBookingConfirmationModal = true;
        },

        deleteBooking() {
            axios.delete('/api/admin/gyms/reservations/' + this.bookingToDelete).then(response => {
                alert('Booking Deleted');
                this.reservations.splice(this.bookingToDeleteRef, 1);
                this.bookingToDelete = null;
                this.bookingToDeleteRef = null;
                this.showDeleteBookingConfirmationModal = false;
            })
        },

        confirmDeleteMembership(membership_id, ref) {
            this.membershipToDelete = membership_id;
            this.membershipToDeleteRef = ref;
            this.showDeleteMembershipConfirmationModal = true;
        },

        deleteMembership() {
            axios.delete('/api/admin/members/' + this.$route.params.id + '/memberships/' + this.membershipToDelete).then(response => {
                alert('Membership Deleted! If you would like to refund this purchase, please do it through Stripe.');
                this.memberships.splice(this.membershipToDeleteRef, 1);
                this.membershipsToDelete = null;
                this.membershipsToDeleteRef = null;
                this.showDeleteMembershipConfirmationModal = false;
            })
        },

        confirmDeleteCreditPack(credit_pack_id, ref) {
            this.creditPackToDelete = credit_pack_id;
            this.creditPackToDeleteRef = ref;
            this.showDeleteCreditPackConfirmationModal = true;
        },

        editCreditPack(creditPackId) {
            this.$router.push('/admin/orders/' + creditPackId);
        },

        editMembership(membership) {
            this.$router.push('/admin/memberships/' + membership);
        },

        deleteCreditPack() {
            axios.delete('/api/admin/members/' + this.$route.params.id + '/credit-packs/' + this.creditPackToDelete).then(response => {
                alert('Credit Pack! If you would like to refund this purchase, please do it through Stripe.');
                this.creditPacks.splice(this.creditPackToDeleteRef, 1);
                this.creditPackToDelete = null;
                this.creditPackToDeleteRef = null;
                this.showDeleteCreditPackConfirmationModal = false;
            })
        },

        togglePreference(id) {
            axios.patch('/api/admin/members/' + this.$route.params.id + '/marketing-preferences', {
                id: id
            }).then(response => {
                this.member.notification_preferences = response.data;
            })
        },

        loadDoorAccessLog() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/access-log').then(response => {
                this.doorAccessLog = response.data;
            });
        },
        toggle(id) {
            this.isActive = !this.isActive;
            if (this.opened.includes(id)) {
                for (var i = 0; i < this.opened.length; i++) {

                    if (this.opened[i] === id) {

                        this.opened.splice(i, 1);

                    }

                }
            } else {
                this.opened.push(id);

            }
            console.log(id);
            console.log(JSON.stringify(this.opened));
        },

        confirmDeleteParq(parq_id, ref) {
            this.parqToDelete = parq_id;
            this.parqToDeleteRef = ref;
            this.showDeleteParqModal = true;
        },
        deleteParq() {
            axios.post('/api/admin/members/' + this.$route.params.id + '/parqs/delete', {
                id: this.parqToDelete
            }).then(response => {
                this.loadParqs();
                this.showDeleteParqModal = false;
            })
        },
        saveParq() {
            axios.post('/api/admin/members/' + this.$route.params.id + '/parqs/save', {
                first_name: this.parqFirstName,
                last_name: this.parqLastName,
                phone_number: this.parqPhoneNumber,
                email: this.parqEmail,
                current_injuries: this.parqInjuries,
                current_injuries_details: this.parqInjuriesExtra,
                taking_medication: this.parqMedication,
                taking_medication_details: this.parqMedicationExtra,
                advised_by_doctor: this.parqPhysical,
                advised_by_doctor_details: this.parqPhysicalExtra,
                currently_pregnant: this.parqPregnant,
                currently_pregnant_details: this.parqPregnantExtra,
            }).then(response => {
                console.log(response);
                this.loadParqs();
                this.showParqModal = false;
            }).catch(error => {
                console.log('ERROR');

                this.errors = error.response.data.errors;
                console.log(this.errors);
            })

        },

        changeSelect(event, type) {
            if (type == 'injuries' && event.target.value == 'yes') {
                this.parqInjuriesMore = true;
            } else if (type == 'injuries' && event.target.value == 'no') {
                this.parqInjuriesMore = false;
            }
            if (type == 'medication' && event.target.value == 'yes') {
                this.parqMedicationMore = true;
            } else if (type == 'medication' && event.target.value == 'no') {
                this.parqMedicationMore = false;
            }
            if (type == 'pregnant' && event.target.value == 'yes') {
                this.parqPregnantMore = true;
            } else if (type == 'pregnant' && event.target.value == 'no') {
                this.parqPregnantMore = false;
            }
            if (type == 'physical' && event.target.value == 'yes') {
                this.parqPhysicalMore = true;
            } else if (type == 'physical' && event.target.value == 'no') {
                this.parqPhysicalMore = false;
            }
        },

        editContract(contract_id, ref, contractName, contractExpires) {

            var exp = contractExpires.split("-");
            this.contractID = contract_id;
            this.expires.year = exp[0];
            this.expires.month = exp[1];
            this.expires.day = exp[2];

            this.contractName = contractName
            this.showEditContractModal = true;
        },

        saveContract() {
            axios.patch('/api/admin/contracts/' + this.$route.params.id, {
                id: this.contractID,
                day: this.expires.day,
                month: this.expires.month,
                year: this.expires.year,

            }).then(response => {
                this.showEditContractModal = false;
                this.loadContracts();
                this.expires.year = '';
                this.expires.month = '';
                this.expires.day = '';
            });
        },


        confirmDeleteContract(contract_id, ref) {
            this.contractToDelete = contract_id;
            this.contractToDeleteRef = ref;
            this.showDeleteContractModal = true;
        },
        deleteContract() {
            axios.delete('api/admin/contracts/delete', {
                id: this.contractToDelete,
            }).then(response => {
                this.loadContracts();
                this.showDeleteContractModal = false;
            })
        },

        uploadContract() {
            var _this = this;
            if (this.file.length == '') {
                alert('You need to choose a file to upload');
                return false;
            }
            axios.post('/api/admin/contracts', {
                day: this.expires.day,
                month: this.expires.month,
                year: this.expires.year,
                name: this.contractName,
                user_id: this.$route.params.id
            }).then(response => {
                this.uploadContractFile(response.data.id);
            })
                .catch(function (error) {
                    _this.errors = error.response.data.errors;
                });
        },

        uploadContractFile(id) {


            let formData = new FormData();
            formData.append('file', this.file);
            formData.append('id', id);

            /**
             * Make the request to the POST /multiple-files URL
             */
            axios.post('/api/admin/contracts/upload', formData, {

                headers: {
                    'Content-Type': 'multipart/form-data'
                },

            }).then(response => {
                this.loadContracts();
                this.showUploadContractModal = false;
                this.contractFiles = '';
                this.contractName = '';
                this.expires.year = '';
                this.expires.month = '';
                this.expires.day = '';
                console.log(response.data);
            })
                .catch(function (error) {
                    console.log(error);
                    this.errors = error.response.data.errors;

                });
        },
        sendToUrl(url) {
            window.location.href = url
        },
        handleFileUpload() {
            this.file = this.$refs.file.files[0];

        }


    }
}
</script>
