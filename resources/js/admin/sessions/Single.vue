<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">remember_me</i>
                        </div>
                        Sessions
                    </h1>

                    <h2 class="page-header__sub">Session #{{ session.id }}</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="row">
                <div class="columns eight-xl six-xxl">
                    <booking-info-card 
                        class="card"
                        :datetime="session.time_human" 
                        :status="session.status_human"
                        :duration="session.length_human"
                        :rating="session.rating"
                        :sessionId="session.id"
                        :packageInfo="session.package"
                        :trainerInfo="session.trainer"
                        :memberInfo="session.member">
                        <section class="booking-card__section">
                            <h2 class="booking-card__section__title booking-card__section__title--no-margin">
                                {{ session.name ? session.name : `${session.length_human} minute Virtual Coaching` }}
                            </h2>
                        </section>
                        <section class="booking-card__section">
                            <h2 class="booking-card__section__title booking-card__section__title--no-margin">Package Name</h2>
                        </section>
                        <section v-if="session.status !== 'completed'" class="booking-card__section">
                            <div class="booking-card__section__buttons booking-card__section__buttons--no-margin">
                                <template v-if="session.status === 'in-progress'">
                                    <button class="button">
                                        Join Video Call
                                    </button>
                                    <button class="button button--grey">
                                        Mark as No Show
                                    </button>
                                </template>

                                <template v-if="session.status === 'upcoming'">
                                    <button class="button button--red" @click="displayCancelConfirmModal = true">
                                        Cancel Session
                                    </button>
                                </template>
                            </div>
                        </section>
                    </booking-info-card>
                </div>
            </div>
        </section>

        <div v-if="displayCancelConfirmModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Cancellation</h2>

                <p>Are you sure you wish to cancel this session? The member will be notified and their credit refunded back to their account.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="displayCancelConfirmModal = false">Cancel</button>
                    <button class="button button--with-icon button--red" @click="cancelSession()">
                        Yes, Cancel
                    </button>
                </div>
            </div>
        </div>

        <div v-if="displayCancelledModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Session Cancelled</h2>

                <p>{{ session.member.name }} has been sent an email confirming this cancellation and they will be credited a session back to their account.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--green" @click="displayCancelledModal = false">Okay</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import BookingInfoCard from '../../components/BookingInfoCard.vue'

export default {
    components: { BookingInfoCard },
    data() {
        return {
            loading: true,
            session: {
                member: {},
                trainer: {},
                package: {}
            },

            displayCancelledModal: false,
            displayCancelConfirmModal: false,
        }
    },

    mounted() {
        this.loadSession();
    },

    methods: {
        /*
         * Fetch the single session on page load.
         * @param {none}
         */
        loadSession() {
            axios.get('/api/admin/sessions/' + this.$route.params.id).then(response => {
                this.session = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        /*
         * Cancel a session.
         * @param {none}
         */
        cancelSession() {
            axios.patch('/api/admin/sessions/' + this.$route.params.id + '/cancel').then(response => {
                this.displayCancelConfirmModal = false;
                this.displayCancelledModal = true;
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
