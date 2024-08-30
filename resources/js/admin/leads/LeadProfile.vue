<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">supervised_user_circle</i>
                        </div>
                        Lead Management
                    </h1>

                    <h2 class="page-header__sub">Profile</h2>
                </div>
                <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">
                        Create Lead
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <div class="lead-management page-content">
            <div class="info info--bottom" v-if="this.lead.status === 'won'">
                This lead has been converted into a member.
            </div>

            <section class="lead-management__profile">
                <div class="wrapper">
                    <div class="lead-management__profile__header">
                        <img class="lead-management__profile__header__image" :src="this.image_url"/>
                        <div>
                            <label class="title">{{this.full_name}}</label>
                            <div class="lead-management__profile__stats">
                                <div :class="'lead-management__profile__status lead-management__profile__status--' + this.status">
                                    {{this.status}}
                                </div>

                                <div :class="'lead-management__profile__status lead-management__profile__status--' + this.temperature">
                                    {{this.temperature}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lead-management__profile__buttons">
                        <button class="button--icon" @click="showAddNoteModal()">
                            <img class="button--icon__icon" src="/images/icons/note-24px.svg" /><br/>
                            Note
                        </button>

                        <button class="button--icon" @click="switchTab('emails')">
                            <img class="button--icon__icon" src="/images/icons/email-24px.svg" /><br/>
                            Email
                        </button>

                        <button class="button--icon" @click="showAddCallModal()">
                            <img class="button--icon__icon" src="/images/icons/call-24px.svg" /><br/>
                            Call
                        </button>

                        <button class="button--icon" @click="showAddAppointmentModal()">
                            <img class="button--icon__icon" src="/images/icons/appointment-24px.svg" /><br/>
                            Meet
                        </button>

                        <button class="button--icon" @click="showSignUpModal()" v-if="this.lead.status !== 'won' && this.lead.status !== 'lost'">
                            <img class="button--icon__icon" src="/images/icons/signup-24px.svg" /><br/>
                            Sign up
                        </button>
                    </div>

                    <div class="account__container account__container--top">
                        <div class="icon" v-if="this.lead.assigned !== null"><i class="material-icons">badge</i></div>
                        <span class="account__container__text">{{this.lead.assigned === null ? 'No Assigned Agent Yet' : 'Assigned to ' + this.lead.assigned.name}}</span>
                        <button class="button button--icon" @click="openReassignLeadModal()" style="margin-left: 0.5rem; vertical-align: middle;"><i class="material-icons">edit</i></button>
                    </div>

                    <div class="lead-management__profile__details">
                        <div>
                            <label class="subtitle">PROFILE COMPLETION</label>
                            <label class="lead-management__profile__details__stat">{{this.profile_completion}}%</label>

                            <div class="lead-management__profile__details__indicator">
                                <div class="number-indicator" :style="'width: ' + this.profile_completion + '%'"></div>
                            </div>

                            <div class="lead-management__profile__details__about">
                                <label class="subtitle">ABOUT THIS CONTACT</label>
                                <div class="lead-management__profile__details__about__box">
                                    <label class="lead-management__profile__details__about__box__label">
                                        First Name
                                        <span v-if="this.errors['first_name']">{{this.errors['first_name'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({first_name: $event.target.value}, 'First Name')" class="lead-management__profile__details__about__box__input" type="text" :value="first_name" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Last Name
                                        <span v-if="this.errors['last_name']">{{this.errors['last_name'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({last_name: $event.target.value}, 'Last Name')" class="lead-management__profile__details__about__box__input" type="text" :value="last_name" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Email
                                        <span v-if="this.errors['email']">{{this.errors['email'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({email: $event.target.value}, 'Email')" class="lead-management__profile__details__about__box__input" type="text" :value="email" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Phone Number
                                        <span v-if="this.errors['phone_number']">{{this.errors['phone_number'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({phone_number: $event.target.value}, 'Phone Number')" class="lead-management__profile__details__about__box__input" type="text" :value="phone_number" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Date of Birth
                                        <span v-if="this.errors['date_of_birth']">{{this.errors['date_of_birth'][0]}}</span>
                                    </label>
                                    <!-- <input v-on:keyup.enter="blur($event)" @blur="saveLead({date_of_birth: date_of_birth}, 'Date of Birth')" class="lead-management__profile__details__about__box__input" type="text" v-model="date_of_birth" /> -->

                                    <datepicker
                                        :value="new Date(date_of_birth)"
                                        v-model="date_of_birth"
                                        input-class="lead-management__profile__details__about__box__input"
                                        style="width: 100%; box-sizing: border-box;"
                                        @closed="saveLead({date_of_birth: new Date(date_of_birth)}, 'Date of Birth');"
                                    />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Gender
                                        <span v-if="this.errors['gender']">{{this.errors['gender'][0]}}</span>
                                    </label>
                                    <div class="form-element">
                                        <div>
                                            <select v-on:keyup.enter="blur($event)" @blur="saveLead({gender: $event.target.value}, 'Gender')" :value="gender">
                                                <option value="">-- Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <input v-on:keyup.enter="blur($event)" @blur="saveLead({gender: gender}, 'Gender')" class="lead-management__profile__details__about__box__input" type="text" v-model="gender" /> -->

                                    <label class="lead-management__profile__details__about__box__label">
                                        Gym locations of interest
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({gym_locations: $event.target.value}, 'Gym locations of interest')" class="lead-management__profile__details__about__box__input" type="text" :value="gym_locations" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Lead Source
                                        <span v-if="this.errors['source']">{{this.errors['source'][0]}}</span>
                                    </label>
                                    <div class="form-element">
                                        <div>
                                            <select :value="source" v-on:keyup.enter="blur($event)" @blur="saveLead({source: $event.target.value}, 'Lead Source')">
                                                <option value="">-- Lead Source</option>
                                                <option value="facebook">Facebook</option>
                                                <option value="walk-in">Walk-In</option>
                                                <option value="phone">Phone</option>
                                                <option value="promotion">Promotion</option>
                                                <option value="website">Website</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <input v-on:keyup.enter="blur($event)" @blur="saveLead({source: source}, 'Lead Source')" class="lead-management__profile__details__about__box__input" type="text" v-model="source" /> -->

                                    <label class="lead-management__profile__details__about__box__label">
                                        Where did they hear about us?
                                        <span v-if="this.errors['heard_from']">{{this.errors['heard_from'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({heard_from: $event.target.value}, 'Heard from')" class="lead-management__profile__details__about__box__input" type="text" :value="heard_from" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Fitness Goals
                                        <span v-if="this.errors['fitness_goal']">{{this.errors['fitness_goal'][0]}}</span>
                                    </label>
                                    <div class="form-element">
                                        <div class="form-element__control">
                                            <div class="form-element__checkbox lead-management__profile__fitness-goal" :title="goal.name" v-for="goal in goals">
                                                <input type="checkbox" :value="goal.id.toString()"  v-model="fitness_goal" /> <label class="form-element__label">{{goal.name}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <input v-on:keyup.enter="blur($event)" @blur="saveLead({fitness_goal: $event.target.value}, 'Fitness Goal')" class="lead-management__profile__details__about__box__input" type="text" :value="fitness_goal" /> -->

                                    <label class="lead-management__profile__details__about__box__label">
                                        Fitness history
                                        <span v-if="this.errors['fitness_history']">{{this.errors['fitness_history'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({fitness_history: $event.target.value}, 'Fitness history')" class="lead-management__profile__details__about__box__input" type="text" :value="fitness_history" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Previous gym memberships?
                                        <span v-if="this.errors['previous_gyms']">{{this.errors['previous_gyms'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({previous_gyms: $event.target.value}, 'Previous gym memberships')" class="lead-management__profile__details__about__box__input" type="text" :value="previous_gyms" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Time since they last exercised
                                        <span v-if="this.errors['last_exercise']">{{this.errors['last_exercise'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({last_exercise: $event.target.value}, 'Time since they last exercised')" class="lead-management__profile__details__about__box__input" type="text" :value="last_exercise" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Upcoming events
                                        <span v-if="this.errors['upcoming_events']">{{this.errors['upcoming_events'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({upcoming_events: $event.target.value}, 'Upcoming events')" class="lead-management__profile__details__about__box__input" type="text" :value="upcoming_events" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Family situation
                                        <span v-if="this.errors['family_situation']">{{this.errors['family_situation'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({family_situation: $event.target.value}, 'Family situation')" class="lead-management__profile__details__about__box__input" type="text" :value="family_situation" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Sleep pattern / Wellness
                                        <span v-if="this.errors['sleep_pattern']">{{this.errors['sleep_pattern'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({sleep_pattern: $event.target.value}, 'Sleep pattern')" class="lead-management__profile__details__about__box__input" type="text" :value="sleep_pattern" />

                                    <label class="lead-management__profile__details__about__box__label">
                                        Preferred fitness activities
                                        <span v-if="this.errors['fitness_activities']">{{this.errors['fitness_activities'][0]}}</span>
                                    </label>
                                    <input v-on:keyup.enter="blur($event)" @blur="saveLead({fitness_activities: $event.target.value}, 'Preferred fitness activities')" class="lead-management__profile__details__about__box__input" type="text" :value="fitness_activities" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <section class="tab">
                                <ul>
                                    <li :class="this.tab === 'all' ? 'active' : ''"><a @click="switchTab('all')">All Activity</a></li>
                                    <li :class="this.tab === 'notes' ? 'active' : ''"><a @click="switchTab('notes')">Notes</a></li>
                                    <li :class="this.tab === 'emails' ? 'active' : ''"><a @click="switchTab('emails')">Emails</a></li>
                                    <li :class="this.tab === 'calls' ? 'active' : ''"><a @click="switchTab('calls')">Calls</a></li>
                                    <li :class="this.tab === 'appointments' ? 'active' : ''"><a @click="switchTab('appointments')">Appointments</a></li>
                                </ul>
                            </section>

                            <div v-if="tab === 'all' && activities.length > 0" class="lead-management__profile__details__about" v-for="group in activities">
                                <label class="subtitle">{{group.date}}</label>
                                <div class="lead-management__profile__details__about__box">
                                    <div class="lead-management__profile__details__about__box__activity" v-for="log in group.logs">
                                        <div class="lead-management__profile__details__about__box__activity__border"></div>

                                        <img
                                            class="lead-management__profile__details__about__box__activity__icon"
                                            :src="log.type ? log.type.image_path : '/images/icons/note-24px.svg'"
                                        />

                                        <div class="lead-management__profile__details__about__box__activity__container">
                                            <label class="lead-management__profile__details__about__box__activity__details">{{log.details}}</label>
                                            <label class="lead-management__profile__details__about__box__activity__extra">
                                                {{log.extra_details}}
                                                <span v-if="log.note_id"> &bull; <a href="javascript:void(0)" @click="showNoteModal(log)">View note</a></span>
                                            </label>
                                        </div>
                                    </div>


                                    <button class="button button--mt50" v-if="group.logs.length < group.total" @click="loadActivities(group.current + 10)">Load more</button>
                                </div>
                            </div>

                            <div v-if="tab === 'all' && activities.length === 0" class="lead-management__profile__details__about">
                                <label class="subtitle">ACTIVITY</label>
                                <div class="lead-management__profile__details__about__box empty">
                                    <div class="lead-management__profile__details__about__box__activity">
                                        <label>This lead has no recent activity</label>
                                    </div>
                                </div>
                            </div>

                            <div v-if="tab === 'notes'" class="lead-management__profile__details__about">
                                <label class="subtitle">Notes</label>
                                <div class="lead-management__profile__details__about__box" v-if="notes.length > 0">
                                    <div class="lead-management__profile__details__about__box__activity" v-for="note in notes">
                                        <div class="lead-management__profile__details__about__box__activity__border"></div>

                                        <img
                                            class="lead-management__profile__details__about__box__activity__icon"
                                            :src="'/images/icons/' + note.type + '-24px.svg'"
                                        />

                                        <div class="lead-management__profile__details__about__box__activity__container">
                                            <label class="lead-management__profile__details__about__box__activity__details">Note made by {{note.agent.name}}</label>
                                            <label class="lead-management__profile__details__about__box__activity__extra">{{note.content}}</label>
                                        </div>
                                    </div>
                                    <!-- <div class="form-element" style="padding-top: 0;">
                                        <div class="form-element__control">
                                            <textarea class="lead__profile__notes" placeholder="Add notes here" v-model="notes"></textarea>
                                        </div>
                                    </div>
                                    <button class="button" @click="storeNotes()">Save</button> -->
                                </div>
                                <div class="lead-management__profile__details__about__box empty" v-else>
                                    <div class="lead-management__profile__details__about__box__activity">
                                        <label>Add notes to help keep track of important information</label>
                                        <button class="button" @click="showAddNoteModal()">Add note</button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="tab === 'emails'" class="lead-management__profile__details__about">
                                <label class="subtitle">Email</label>
                                <div class="lead-management__profile__details__about__box emails">
                                    <div class="lead-management__profile__form-element__email">
                                        <label>To</label>
                                        <input type="email" :value="this.email" disabled/>
                                    </div>
                                    <hr>
                                    <div class="lead-management__profile__form-element__email">
                                        <label>From</label>
                                        <input type="email" :value="this.authUser.email" disabled/>
                                    </div>
                                    <hr>
                                    <div class="lead-management__profile__form-element__email">
                                        <label>Subject</label>
                                        <input type="text" v-model="mail.subject" placeholder="Type a subject..."/>
                                    </div>
                                    <hr>
                                    <div class="lead-management__profile__form-element__email">
                                        <label>Message</label>
                                        <textarea v-model="mail.message" placeholder="Type a message..."></textarea>
                                    </div>
                                    <button class="button" @click="sendEmail()">Send email</button>

                                    <div class="lead-management__profile__details__about__box__activity" v-for="email in emails">
                                        <div class="lead-management__profile__details__about__box__activity__border"></div>

                                        <img
                                            class="lead-management__profile__details__about__box__activity__icon"
                                            src="/images/icons/email-24px.svg"
                                        />

                                        <div class="lead-management__profile__details__about__box__activity__container">
                                            <label class="lead-management__profile__details__about__box__activity__extra">{{email.created_human}}</label>
                                            <label class="lead-management__profile__details__about__box__activity__details calls">Email sent by {{email.agent.full_name}}</label>
                                            <label class="lead-management__profile__details__about__box__activity__extra">
                                                {{email.extra_details}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="tab === 'calls'" class="lead-management__profile__details__about">
                                <label class="subtitle">Calls</label>
                                <div class="lead-management__profile__details__about__box" v-if="calls.length > 0">
                                    <div class="lead-management__profile__details__about__box__activity" v-for="call in calls">
                                        <div class="lead-management__profile__details__about__box__activity__border"></div>

                                        <img
                                            class="lead-management__profile__details__about__box__activity__icon"
                                            src="/images/icons/call-24px.svg"
                                        />

                                        <div class="lead-management__profile__details__about__box__activity__container">
                                            <label class="lead-management__profile__details__about__box__activity__extra">
                                                {{call.datetime_human ? call.datetime_human : call.created_human}}
                                            </label>
                                            <label class="lead-management__profile__details__about__box__activity__details calls">Call made by {{call.agent.full_name}}</label>
                                            <label class="lead-management__profile__details__about__box__activity__extra">
                                                {{call.outcome}}
                                                <span v-if="call.note_id"> &bull; <a href="javascript:void(0)" @click="showNoteModal(call)">View note</a></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="lead-management__profile__details__about__box empty" v-else>
                                    <div class="lead-management__profile__details__about__box__activity">
                                        <label>No calls have been logged with this lead</label>
                                        <button class="button" @click="showAddCallModal()">Add call</button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="tab === 'appointments'" class="lead-management__profile__details__about">
                                <label class="subtitle">Appointments</label>
                                <div class="lead-management__profile__details__about__box" v-if="appointments.length > 0">
                                    <div class="lead-management__profile__details__about__box__activity" v-for="appointment in appointments">
                                        <div class="lead-management__profile__details__about__box__activity__border"></div>

                                        <img
                                            class="lead-management__profile__details__about__box__activity__icon"
                                            src="/images/icons/appointment-24px.svg"
                                        />

                                        <div class="lead-management__profile__details__about__box__activity__container">
                                            <label class="lead-management__profile__details__about__box__activity__extra">
                                                {{appointment.datetime_human}}
                                                <span v-if="appointment.outcome">
                                                    &bull; {{appointment.show === 1 ? 'Attended' : 'No show'}}
                                                    &bull; {{appointment.outcome}}
                                                </span>
                                                <span v-if="appointment.outcome_reason"> - {{appointment.outcome_reason}}</span>
                                            </label>
                                            <label class="lead-management__profile__details__about__box__activity__details calls">
                                                Appointment Booked by {{appointment.agent.full_name}}
                                            </label>
                                            <label class="lead-management__profile__details__about__box__activity__extra">
                                                {{appointment.gym.name}}
                                                <span v-if="appointment.note_id"> &bull; <a href="javascript:void(0)" @click="showNoteModal(appointment)">View note</a></span>
                                                <span> &bull; <a href="javascript:void(0)" @click="showUpdateAppointmentModal(appointment)">Update</a></span>
                                            </label>

                                        </div>
                                    </div>
                                </div>
                                <div class="lead-management__profile__details__about__box empty" v-else>
                                    <div class="lead-management__profile__details__about__box__activity">
                                        <label>There are no upcoming appointments with this lead</label>
                                        <button class="button" @click="showAddAppointmentModal()">Create appointment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </section>

            <div class="modal-container" v-if="this.displaySendEmailModal">
                <div class="sidebar-modal">
                    <div class="sidebar-modal__header">
                        <label class="sidebar-modal__header__title">Send an email</label>
                    </div>

                    <div class="sidebar-modal__body">
                        <div class="lead-management__profile__form-element__email">
                            <label>To</label>
                            <input type="email" :value="this.email" disabled/>
                        </div>
                        <hr>
                        <div class="lead-management__profile__form-element__email">
                            <label>From</label>
                            <input type="email" :value="this.authUser.email" disabled/>
                        </div>
                        <hr>
                        <div class="lead-management__profile__form-element__email">
                            <label>Subject</label>
                            <input type="text" v-model="mail.subject" placeholder="Type a subject..."/>
                        </div>
                        <hr>
                        <div class="lead-management__profile__form-element__email">
                            <label>Message</label>
                            <textarea v-model="mail.message" placeholder="Type a message..."></textarea>
                        </div>
                    </div>

                    <div class="sidebar-modal__footer">
                        <button class="button" @click="sendEmail()">Send email</button>
                        <button class="button button--grey" @click="hideModals()">Close</button>
                    </div>
                </div>
            </div>

            <div class="feedback-modal">
                <i class="fas fa-check-circle"></i>
                <label>{{this.successMessage}}</label>
            </div>
        </div>

        <create-lead-modal v-on:cancel="loadProfile(); displayCreateModal = false" v-on:complete="loadProfile(); displayCreateModal = false;" :class="displayCreateModal ? 'modal modal--active' : 'modal'" />

        <add-note-modal v-on:cancel="loadProfile(); displayAddNoteModal = false" v-on:complete="loadProfile(); displayAddNoteModal = false;" :class="displayAddNoteModal ? 'modal modal--active' : 'modal'" :edit="true" />

        <add-note-modal v-if="note_id" v-on:cancel="loadProfile(); displayEditNotesModal = false" v-on:complete="loadProfile(); displayEditNotesModal = false;" :class="displayEditNotesModal ? 'modal modal--active' : 'modal'" :edit="false" :note_id="note_id"/>

        <add-call-modal v-on:cancel="loadProfile(); displayAddCallModal = false" v-on:complete="loadProfile(); displayAddCallModal = false;" :class="displayAddCallModal ? 'modal modal--active' : 'modal'" :full_name="full_name" :image_url="image_url" :calls_made="calls_made" />

        <add-appointment-modal v-on:cancel="loadProfile(); displayAddAppointmentModal = false" v-on:complete="loadProfile(); displayAddAppointmentModal = false;" :class="displayAddAppointmentModal ? 'modal modal--active' : 'modal'" :full_name="full_name" :image_url="image_url" :calls_made="calls_made" />

        <signup-modal v-on:cancel="loadProfile(); displaySignUpModal = false" v-on:complete="loadProfile(); displaySignUpModal = false;" :class="displaySignUpModal ? 'modal modal--active' : 'modal'" :full_name="full_name" :image_url="image_url" :calls_made="calls_made" />

        <reassign-modal v-on:cancel="loadProfile(); displayReassignLeadModal = false" v-on:complete="loadProfile(); displayReassignLeadModal = false;" :class="displayReassignLeadModal ? 'modal modal--active' : 'modal'" :full_name="full_name" :image_url="image_url" />

        <log-outcome-modal v-on:cancel="displayUpdateAppointmentModal = false" v-on:complete="displayUpdateAppointmentModal = false;" :class="displayUpdateAppointmentModal ? 'modal modal--active' : 'modal'" :appointment_id="appointment_id" />
    </div>
</template>
<script>
import axios from 'axios';
import Datepicker from 'vuejs-datepicker';

import VueTimepicker from 'vue2-timepicker';
import 'vue2-timepicker/dist/VueTimepicker.css';

import CreateLeadModal from './CreateLeadModal.vue';
import AddNoteModal from './AddNoteModal.vue';
import AddCallModal from './AddCallModal.vue';
import AddAppointmentModal from './AddAppointmentModal.vue';
import SignupModal from './SignUpModal.vue';
import ReassignModal from './ReassignModal.vue';
import LogOutcomeModal from './LogAppointmentOutcomeModal.vue';

export default {
    components: {
        Datepicker,
        VueTimepicker,
        CreateLeadModal,
        AddNoteModal,
        AddCallModal,
        AddAppointmentModal,
        SignupModal,
        ReassignModal,
        LogOutcomeModal
    },

    props: {
        authUser: Object,

    },

    data() {
        return {
            lead: {
                assigned: {}
            },

            displayCreateModal: false,
            image_url: null,

            full_name: null,
            first_name: null,
            last_name: null,
            email: null,
            phone_number: null,
            date_of_birth: null,
            gender: null,
            source: null,
            fitness_goal: null,

            gym_locations: null,
            heard_from: null,
            fitness_goal: [],
            fitness_history: null,
            previous_gyms: null,
            last_exercise: null,
            upcoming_events: null,
            family_situation: null,
            sleep_pattern: null,
            fitness_activities: null,

            calls_made: null,
            call_outcome: 'Not interested',
            subscribe_weekly: null,
            subscribe_monthly: null,
            unsubscribe: null,

            scheduled_call_date: new Date(Date.now()).toDateString(),
            scheduled_call_time: '12:00:00',

            appointment_about: null,
            appointment_duration: '',
            appointment_notes: null,

            signup_keyfob_id: null,
            contract: [],

            activities: [],
            emails: [],
            calls: [],
            appointments: [],
            notes: [],

            new_note: null,
            note: {},
            note_id: null,

            gyms: [],
            selected_gym: '',

            status: null,
            temperature: null,

            profile_completion: 0,

            tab: 'all',
            displayAddNoteModal: false,
            displaySendEmailModal: false,
            displayAddCallModal: false,
            displayEditNotesModal: false,
            displayAddAppointmentModal: false,
            displayUpdateAppointmentModal: false,
            displaySignUpModal: false,

            appointment_id: null,
            appointment_outcome: null,
            appointment_outcome_reason: '',
            appointment_show: null,
            update_appointment_note: null,

            signup_date_of_birth: new Date(Date.now()).toDateString(),

            successMessage: null,
            displaySuccessModal: false,

            uploadPercentage: 0,

            mail: {
                from_address: this.authUser.email
            },

            loading: true,
            errors: {},

            displayReassignLeadModal: false,
            displayLeadReassignedModal: false,
            reassignTo: {},
            reassignableAgents: [],

            goals: [],

            componentKey: 1,
            appointmentKey: 2,
            signupKey: 3,
            reassignKey: 4,
        };
    },

    mounted() {
        this.loadProfile();
    },

    watch: {
        '$route.params.id': function (id) {
            this.loadProfile();
        },

        subscribe_weekly: function(value) {
            if(value === true) {
                this.unsubscribe = false;
            }
        },
        subscribe_monthly: function(value) {
            if(value === true) {
                this.unsubscribe = false;
            }
        },
        unsubscribe: function(value) {
            if(value === true) {
                this.subscribe_weekly = false;
                this.subscribe_monthly = false;
            }
        },
        scheduled_call_date: function(value) {
            this.scheduled_call_date = new Date(this.scheduled_call_date).toDateString();
        },
        fitness_goal: function(value, old) {
            if(this.loading) {
                // we dont want to save when this was updated by a load...
                return;
            }

            this.saveLead({fitness_goal: value}, 'Fitness Goal');
        },

        displayAddCallModal: function(value) {
            this.componentKey++;
        },
        displayAddAppointmentModal: function(value) {
            this.appointmentKey++;
        }
    },

    methods: {
        loadProfile() {
            this.loading = true;
            axios.get('/api/admin/leads/' + this.$route.params.id)
            .then(response => {
                this.$router.push('/admin/members/' + response.data.lead.user_id);

                console.log(response);
                this.lead = response.data.lead;
                this.image_url = response.data.lead.image_url;

                this.full_name = response.data.lead.full_name;
                this.first_name = response.data.lead.first_name;
                this.last_name = response.data.lead.last_name;
                this.email = response.data.lead.email;
                this.phone_number = response.data.lead.phone_number;
                this.date_of_birth = new Date(response.data.lead.date_of_birth);
                this.gender = response.data.lead.gender;
                this.source = response.data.lead.source;
                this.fitness_goal = response.data.lead.fitness_goal;
                this.gym_locations = response.data.lead.gym_locations;
                this.heard_from = response.data.lead.heard_from;
                this.fitness_goal = response.data.lead.fitness_goal;
                this.fitness_history = response.data.lead.fitness_history;
                this.previous_gyms = response.data.lead.previous_gyms;
                this.last_exercise = response.data.lead.last_exercise;
                this.upcoming_events = response.data.lead.upcoming_events;
                this.family_situation = response.data.lead.family_situation;
                this.sleep_pattern = response.data.lead.sleep_pattern;
                this.fitness_activities = response.data.lead.fitness_activities;

                this.status = response.data.lead.status;
                this.temperature = response.data.lead.temperature;
                this.profile_completion = response.data.lead.profile_completion;

                this.calls_made = response.data.lead.calls_made;

                // this.activities = response.data.lead.activities;

                this.loadActivities();
                this.loadFitnessGoals();
                this.fetchLeadNotes();
                this.fetchLeadEmails();
                this.fetchLeadCalls();
                this.fetchLeadAppointments();
                console.log(this.activities.length);
            })
            .catch(error => {
                console.error(error);
            })
            .finally(() => {
                this.loading = false;
            });
        },

        loadFitnessGoals() {
            axios.get('/api/admin/leads/goals')
            .then(response => {
                this.goals = response.data.goals;
            })
            .catch(error => {
                console.error(error);
            });
        },

        loadActivities(limit = 10) {
            axios.post('/api/admin/leads/' + this.$route.params.id + '/activities', {
                limit
            })
            .then(response => {
                console.log(response);
                this.activities = response.data.activities;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        saveLead(data, field) {
            for(let key in data) {
                if(key === 'fitness_goal') {
                    // we dont need to check this field
                    break;
                }

                if(this[key] === data[key]) {
                    console.log('its the same!');
                    return;
                } else {
                    // set the value on the data object
                    this[key] = data[key];
                }
            }

            if(data.date_of_birth) {
                let year = data.date_of_birth.getFullYear();
                let month = data.date_of_birth.getMonth() + 1;
                let day = data.date_of_birth.getDate();

                if(month < 10) {
                    month = `0${month}`;
                }

                if(day < 10) {
                    day = `0${day}`;
                }

                data.date_of_birth = `${year}-${month}-${day}`;
            }

            axios.post('/api/admin/leads/' + this.leadID + '/save', data)
            .then(response => {
                this.hideModals();
                this.loadProfile();
                this.showSuccessModal(`${field} was successfully updated`);
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        blur(event) {
            $(event.target).blur();
        },

        storeNotes() {
            axios.post('/api/leads/' + this.leadID + '/notes', {
                note: this.new_note
            })
            .then(response => {
                this.new_note = null;
                this.hideModals();

                this.loadProfile();
                this.fetchLeadNotes();

                this.showSuccessModal('A note has been added to this lead');
            })
            .catch(error => {
                console.error(error);
            });
        },

        switchTab(tab) {
            if(tab === 'all') {
                this.loadProfile();
            }

            if(tab === 'notes') {
                this.fetchLeadNotes();
            }

            if(tab === 'emails') {
                this.fetchLeadEmails();
            }

            if(tab === 'calls') {
                this.fetchLeadCalls();
            }

            if(tab === 'appointments') {
                this.fetchLeadAppointments();
            }

            this.tab = tab;
        },

        fetchGyms() {
            axios.post('/api/gyms')
            .then(response => {
                this.gyms = response.data;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        fetchLeadNotes() {
            axios.get('/api/admin/leads/' + this.leadID + '/notes')
            .then(response => {
                this.notes = response.data.notes;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        fetchLeadEmails() {
            axios.get('/api/admin/leads/' + this.leadID + '/emails')
            .then(response => {
                this.emails = response.data.emails;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            });
        },

        fetchLeadCalls() {
            axios.get('/api/admin/leads/' + this.leadID + '/calls')
            .then(response => {
                this.calls = response.data.calls;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        fetchLeadAppointments() {
            axios.get('/api/admin/leads/' + this.leadID + '/appointments')
            .then(response => {
                this.appointments = response.data.appointments;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        showAddNoteModal() {
            this.displayAddNoteModal = true;
        },

        showAddCallModal() {
            // this.fetchGyms();
            this.displayAddCallModal = true;
        },

        showAddAppointmentModal() {
            this.displayAddAppointmentModal = true;
        },

        showUpdateAppointmentModal(appointment) {
            this.appointment_id = appointment.id;
            this.displayUpdateAppointmentModal = true;
        },

        selectAppointmentShow(event, value) {
            this.appointment_show = value;

            $('.button.button__select.show').removeClass('active');
            $(event.target).addClass('active');
        },

        selectAppointmentOutcome(event, value) {
            this.appointment_outcome = value;

            $('.button.button__select.outcome').removeClass('active');
            $(event.target).addClass('active');
        },

        storeAppointmentOutcome() {
            axios.post('/api/leads/update-appointment/' + this.appointment_id, {
                outcome: this.appointment_outcome,
                reason: this.appointment_outcome_reason,
                show: this.appointment_show,
                note: this.update_appointment_note,
                scheduled_call_date: this.scheduled_call_date,
                scheduled_call_time: this.scheduled_call_time
            })
            .then(response => {
                this.hideModals();
                this.fetchLeadAppointments();

                this.showSuccessModal('Appointment outcome updated');
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        showNoteModal(item) {
            this.note_id = item.note_id;
            this.displayEditNotesModal = true;
        },

        showSendEmailModal() {
            this.displaySendEmailModal = true;
        },

        selectCallOutcome(event, value) {
            this.call_outcome = value;
        },

        storeCall() {
            axios.post('/api/leads/' + this.leadID + '/log-call', {
                subscribe_weekly: this.subscribe_weekly,
                subscribe_monthly: this.subscribe_monthly,
                unsubscribe: this.unsubscribe,
                outcome: this.call_outcome,
                scheduled_call_date: this.scheduled_call_date,
                scheduled_call_time: this.scheduled_call_time,
                gym_id: this.selected_gym,
                appointment_duration: this.appointment_duration,
                note: this.new_note
            })
            .then(response => {
                this.hideModals();
                this.fetchLeadCalls();
                this.loadProfile();

                switch(this.call_outcome) {
                    case 'Appointment':
                        this.showSuccessModal('An appointment has been scheduled')
                        break;
                    case 'Still thinking':
                        this.showSuccessModal('A callback has been scheduled');
                        break;
                    default:
                        this.showSuccessModal('Call activity saved');
                        break;
                }


            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            })
        },

        sendEmail() {
            console.log(this.mail);

            axios.post('/api/admin/leads/' + this.leadID + '/email', {
                from_address: this.authUser.email,
                subject: this.mail.subject,
                message: this.mail.message
            })
            .then(response => {
                this.hideModals();
                this.fetchLeadEmails();
                this.showSuccessModal('Email sent');
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            })
        },

        showSignUpModal() {
            this.displaySignUpModal = true;
        },

        storeSignup() {
            axios.post('/api/leads/' + this.leadID + '/signup', {
                keyfob_id: this.signup_keyfob_id,
                contract: this.contract,
                date_of_birth: this.signup_date_of_birth,
            })
            .then(response => {
                this.hideModals();
                this.showSuccessModal('sign up');

                this.uploadContractFile(response.data.signup.id);
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        uploadContractFile(id) {
            let formData = new FormData();

            /**
             * Iteate over any file sent over appending the files to the form data.
             */
            for(var i = 0; i < this.contract.length; i++ ) {
                let file = this.contract[i];
                formData.append('files[' + i + ']', file);
            }

            axios.post('/api/leads/' + id + '/upload-contract', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                onUploadProgress: function( progressEvent ) {
                    this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total ) * 100 ));
                }.bind(this)
            }).then(response => {
                this.loadProfile();
                this.showSuccessModal('The contract has been uploaded');
            })
            .catch(function(error){
                console.log('FAILURE!!');
                console.log(error);
            });
        },

        showContractUploadPopup(event) {
            event.preventDefault();
            this.$refs.contract.click();
        },

        handleContractUpload() {
            this.contract = this.$refs.contract.files;
        },

        hideModals() {
            this.displayAddNoteModal = false;
            this.displayAddCallModal = false;
            this.displayEditNotesModal = false;
            this.displayAddAppointmentModal = false;
            this.displaySendEmailModal = false;
            this.displaySignUpModal = false;
            this.displayUpdateAppointmentModal = false;
        },

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
        /*
         * Open the reassign lead modal.
         * @param {none}
         */
        openReassignLeadModal() {
            // this.fetchAssignableAgents();
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
            console.log('Reassigning lead to ', this.reassignTo.id);

            axios.patch('/api/leads/' + this.leadID + '/reassign', {agent_id: this.reassignTo.id}).then(response => {
                console.log(response);
                this.displayReassignLeadModal = false;
                this.reassignTo = {};
                this.displayLeadReassignedModal = true;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            });
        }
    }
}

</script>
