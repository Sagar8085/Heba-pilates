<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--trainer">
                            <img src="/icons/standard/user.svg">
                        </div>
                        Trainers
                    </h1>
                    <h2 class="page-header__sub">Trainer #{{this.trainer.id}} - {{this.trainer.name}}</h2>
                </div>

                <div class="page-header__col">
                    <button @click="displayEditModal = true" class="button">Edit <img src="/icons/utility/edit.svg"></button>
                </div>
            </div>
        </section>

        <div class="page-content">
            <section class="tab tab--bottom">
                <ul>
                    <li :class="this.tab === 'activity' ? 'active' : ''"><a @click="tab = 'activity'">Performance · November</a></li>
                    <li :class="this.tab === 'availability' ? 'active' : ''"><a @click="tab = 'availability'">Availability Calendar</a></li>
                    <li :class="this.tab === 'bookings' ? 'active' : ''"><a href="#">Bookings</a></li>
                    <li :class="this.tab === 'clients' ? 'active' : ''"><a href="#">Clients</a></li>
                    <li :class="this.tab === 'activity-log' ? 'active' : ''"><a href="#">Activity Log</a></li>
                </ul>
            </section>

            <availability-calendar :user="this.trainer" v-if="!loading && tab === 'availability'" />

            <div class="dashboard-widget dashboard-widget--grey dashboard-widget--four">
                <div class="dashboard-widget__col">
                    <div class="dashboard-widget__row">
                        <div class="dashboard-widget__row-title">Revenue</div>
                        <div class="dashboard-widget__row-value">£424.99 <small>· £320</small></div>
                        <div class="dashboard-widget__row-percentage dashboard-widget__row-percentage--green">
                            <i class="fas fa-chevron-up"></i>

                            <span>50%</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget__col">
                    <div class="dashboard-widget__row">
                        <div class="dashboard-widget__row-title">New Clients</div>
                        <div class="dashboard-widget__row-value">6 <small>· 1</small></div>
                        <div class="dashboard-widget__row-percentage dashboard-widget__row-percentage--green">
                            <i class="fas fa-chevron-up"></i>

                            <span>600%</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget__col">
                    <div class="dashboard-widget__row">
                        <div class="dashboard-widget__row-title">Completed Sessions</div>
                        <div class="dashboard-widget__row-value">15 <small>· 20</small></div>
                        <div class="dashboard-widget__row-percentage dashboard-widget__row-percentage--red">
                            <i class="fas fa-chevron-down"></i>

                            <span>25%</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-widget__col">
                    <div class="dashboard-widget__row">
                        <div class="dashboard-widget__row-title">Average Rating</div>
                        <div class="dashboard-widget__row-value">3.7 <small>· 2.2</small></div>
                        <div class="dashboard-widget__row-percentage dashboard-widget__row-percentage--green">
                            <i class="fas fa-chevron-up"></i>

                            <span>1.5 Stars</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <edit-trainer-modal :user="trainer"
            v-on:cancel="displayEditModal = false"
            v-on:updated="displayEditModal = false; displaySavedModal = true; loadtrainer();"
            v-if="displayEditModal"
            class="modal modal--active"
        />

        <div v-if="displaySavedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Save Successful</h2>

                <p>This trainer has been updated and updates are now live.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--green" @click="displaySavedModal = false">Okay</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import AvailabilityCalendar from './AvailabilityCalendar';
import EditTrainerModal from './EditTrainerModal';

export default {
    components: {
        AvailabilityCalendar,
        EditTrainerModal
    },

    data() {
        return {
            id: this.$route.params.id,
            tab: 'activity',
            loading: true,
            trainer: {},

            displayEditModal: false,
            displaySavedModal: false
        }
    },

    mounted() {
        this.loadtrainer();
    },

    methods: {
        /*
         * Fetch the single trainer on page load.
         * @param {none}
         */
        loadtrainer() {
            axios.get('/api/admin/trainers/' + this.$route.params.id).then(response => {
                this.trainer = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        }
    }
}
</script>
