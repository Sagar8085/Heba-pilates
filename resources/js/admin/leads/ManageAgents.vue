<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">assignment_ind</i>
                        </div>
                        Lead Management
                    </h1>

                    <h2 class="page-header__sub">Manage Sales Agents</h2>
                </div>
                <div class="page-header__col">
                    <router-link :to="'/admin/admins'" class="button">
                        Create Agent
                        <i class="material-icons">add_circle</i>
                    </router-link>
                </div>
            </div>
        </section>

        <div class="page-content lead-management">
            <div class="wrapper">
                <section class="info info--bottom">
                    <p><i class="fas fa-info-circle"></i> If you would like to convert an existing admin user to a sales agent, choose your admin from the <router-link to="/admin/admins">admins</router-link> section and click Edit, then change Is Sales Agent to <strong>Yes</strong></p>
                </section>

                <div class="list-wrap" v-if="this.agents.length > 0">
                    <table class="list list--no-top lead-management__dashboard__list">
                        <thead>
                            <th>Name</th>
                            <th>Job Title</th>
                            <th>Email</th>
                            <th>Calls</th>
                            <th>Appointments</th>
                            <th>Sign Ups</th>
                            <th>Last Activity</th>
                            <th>First Activity</th>
                        </thead>
                        <tbody>
                            <tr v-for="(agent, index) in this.agents" :key="index">
                                <td class="fixed">
                                    <router-link :to="'/admin/leads/manage/agents/' + agent.id">
                                        <!-- <img src="/images/taylor.jpg"> -->
                                        {{agent.name}}
                                    </router-link>
                                </td>
                                <td>Sales Agent</td>
                                <td class="fixed">{{agent.email}}</td>
                                <td>{{agent.calls_made}}</td>
                                <td>{{agent.appointments_made}}</td>
                                <td>{{agent.signups_made}}</td>
                                <td>{{agent.created_at}}</td>
                                <td>{{agent.updated_at}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <section class="forbidden" v-else>
                    <img src="/images/member-portal/empty.svg">
                    <h2>No Sales Agents</h2>
                    <p>Why not create your first one? Or you can upgrade one of your current admin users to a sales agent</p>
                    <br><br>
                    <router-link to="/admin/admins" class="button">Create or Upgrade Existing Admin <i class="fas fa-edit"></i></router-link>
                </section>
            </div>

            <div class="modal-container" v-if="displayCreateModal">
                <div class="sidebar-modal">
                    <div class="sidebar-modal__header">
                        <label class="sidebar-modal__header__title">Create New Sales Agent</label>
                    </div>
                    <div class="sidebar-modal__body">

                        <div class="row">
                            <div class="six columns">
                                <div class="form-element">
                                    <label class="form-element__label">
                                        First Name
                                        <span v-if="this.errors['first_name']">{{ this.errors['first_name'][0] }}</span>
                                    </label>
                                    <div class="form-element__control">
                                        <input type="text" v-model="agent.first_name" />
                                    </div>
                                </div>
                            </div>
                            <div class="six columns">
                                <div class="form-element">
                                    <label class="form-element__label">
                                        Last Name
                                        <span v-if="this.errors['last_name']">{{ this.errors['last_name'][0] }}</span>
                                    </label>
                                    <div class="form-element__control">
                                        <input type="text" v-model="agent.last_name" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-element">
                            <label class="form-element__label">
                                Email
                                <span v-if="this.errors['email']">{{ this.errors['email'][0] }}</span>
                            </label>
                            <div class="form-element__control">
                                <input type="text" v-model="agent.email" />
                            </div>
                        </div>

                        <div class="form-element">
                            <label class="form-element__label">
                                Phone
                                <span v-if="this.errors['phone_number']">{{ this.errors['phone_number'][0] }}</span>
                            </label>
                            <div class="form-element__control">
                                <input type="tel" v-model="agent.phone_number" />
                            </div>
                        </div>

                        <div class="form-element">
                            <label class="form-element__label">
                                Date of Birth
                                <span v-if="this.errors['dob_year']">{{ this.errors['dob_year'][0] }}</span>
                            </label>
                            <div class="form-element__control">
                                <div class="row">
                                    <div class="four columns">
                                        <div class="form-element">
                                            <div class="form-element__control">
                                                <input type="number" required v-model="agent.dob_day" placeholder="25" maxlength="2">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="four columns">
                                        <div class="form-element">
                                            <div class="form-element__control">
                                                <select v-model="agent.dob_month">
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
                                                <select v-model="agent.dob_year">
                                                    <option value="">-- Year</option>
                                                    <option :value="year" v-for="year in years">{{year}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-element">
                            <label class="form-element__label">
                                Gender
                                <span v-if="this.errors['gender']">{{ this.errors['gender'][0] }}</span>
                            </label>
                            <div class="form-element__control">
                                <select v-model="agent.gender">
                                    <option value="">-- Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-element">
                            <label class="form-element__label">
                                Home Gym
                                <span v-if="this.errors['gym_id']">{{ this.errors['gym_id'][0] }}</span>
                            </label>
                            <div class="form-element__control">
                                <select v-model="agent.gym_id">
                                    <option value="">-- Gym</option>
                                    <option :value="gym.id" v-for="gym in gyms">{{gym.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar-modal__footer">
                        <button class="button" @click="save()">Create Sales Agent</button>
                        <button class="button button--white" @click="displayCreateModal = false">Cancel</button>
                    </div>
                </div>
            </div>

            <div :class="displayCreateSuccessModal ? 'modal modal--active' : 'modal'">
                <div class="modal__box">
                    <div class="modal__header">
                        <h3 class="modal__title">Agent Created!</h3>
                    </div>

                    <div class="modal__body">
                        <p class="modal__text">An invitation has been sent to <strong>{{this.createdAgent.email}}</strong> to create their account.</p>
                    </div>


                    <div class="modal__footer">
                        <button type="button" class="button" @click="displayCreateSuccessModal = false">Okay!</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axios from 'axios';

    export default {
        props: {
            authUser: Object
        },

        data() {
            return {
                displayCreateModal: false,
                displayCreateSuccessModal: false,

                agent: {
                    dob_month: '',
                    dob_year: '',
                    gender: '',
                    gym_id: ''
                },

                createdAgent: {

                },

                errors: [],
                agents: [],
                gyms: [],
                years: []
            }
        },

        mounted() {
            this.loadAgents();
            this.loadGyms();

            const year = new Date().getFullYear();
            this.years = Array.from({length: year - 1900}, (value, index) => 1901 + index);
        },

        methods: {
            loadAgents() {
                console.log('Loading Agents..');

                axios.get('/api/admin/leads/manage/agents')
                    .then(response => {
                        this.agents = response.data;
                    })
                    .catch(error => {
                        console.log('ERROR');
                        console.log(error);
                    })
                    .finally(() => this.loading = false);
            },

            loadGyms() {
                console.log('Loading Gyms..');

                axios.get('/api/gyms')
                    .then(response => {
                        this.gyms = response.data;
                    })
                    .catch(error => {
                        console.log('ERROR');
                        console.log(error);
                    });
            },

            save() {
                console.log('Creating New Sales Agent...');

                axios.post('/api/leads/manage/agents', {
                    first_name: this.agent.first_name,
                    last_name: this.agent.last_name,
                    email: this.agent.email,
                    phone_number: this.agent.phone_number,
                    gender: this.agent.gender,
                    dob_day: this.agent.dob_day,
                    dob_month: this.agent.dob_month,
                    dob_year: this.agent.dob_year,
                    gym_id: this.agent.gym_id
                })
                .then(response => {
                    this.displayCreateModal = false;
                    this.displayCreateSuccessModal = true;
                    this.createdAgent = response.data.agent;
                    this.admin = {
                        dob_month: '',
                        dob_year: '',
                        gender: '',
                        gym_id: ''
                    };
                    this.loadAgents();
                })
                .catch(error => {
                    console.log('ERROR');
                    console.log(error);
                    this.errors = error.response.data.errors;
                })
                .finally(() => this.loading = false);
            }
        }
    }
</script>
