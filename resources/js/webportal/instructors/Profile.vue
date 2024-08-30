<template>
    <div class="page-content">
        <loading-spinner class="wrapper" :loading="loadingTrainer" loadingText="Trainer" :noData="!profile.id" noDataText="Trainer not found." />
        <div v-if="profile.id" class="profile">
            <section class="wrapper profile__intro">
                <div class="row">
                    <div class="columns six-md">
                        <ProfilePicture :image="profile.avatar" :firstName="profile.first_name" :lastName="profile.last_name" size="xlarge" />
                    </div>
                    <div class="columns six-md">
                        <label class="page-subtitle">Average Rating</label>
                        <star-rating :rating="averageRating" />
                        <h1 class="profile__title">{{ profile.name }}</h1>
                        <p class="profile__subtitle">Heba Class Instructor</p>
                    </div>
                </div>
            </section>

            <div class="wrapper--half">
                <section class="profile__section">
                    <!-- <video class="profile__video" controls preload="auto">
                        <source :src="profile.video" type="video/mp4">
                    </video> -->
                    <h3 class="profile__title">About Me</h3>
                    <p class="profile__section__content">I specialise in clinic pilates classes and nutrition. I have a masters degree in fitness to give you the best information when it comes to your fitness goals.</p>
                </section>
            </div>

            <!-- <div v-if="popularClasses.length > 0" class="wrapper">
                <section class="profile__section">
                    <h3 class="profile__title">Popular Classes</h3>
                    <VideoList :videos="popularClasses" />
                </section>
            </div> -->

            <!-- <div v-if="reviews.length > 0" class="wrapper--half">
                <section class="profile__section">
                    <h3 class="profile__title">Reviews</h3>

                    <ul>
                        <li v-for="review in reviews" :key="review.id" class="profile__review">
                            <star-rating :rating="review.rating" />
                            <h4 class="profile__review__title">{{ review.title }}</h4>
                            <p class="profile__review__description">{{ review.description }}</p>
                        </li>
                    </ul>
                </section>
            </div> -->
        </div>
    </div>
</template>

<script>
import BackNavigation from '../layout/BackNavigation.vue';
import Modal from '../../components/Modal.vue';
import LoadingSpinner from '../layout/LoadingSpinner.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';
import VideoList from '../layout/VideoList.vue';
import StarRating from '../layout/StarRating.vue'

export default {
    components: {
        BackNavigation,
        Modal,
        LoadingSpinner,
        ProfilePicture,
        StarRating,
        VideoList
    },

    props: {
        authUser: Object
    },

    data() {
        return {
            averageRating: 4,
            popularClasses: [],
            reviews: [
                {
                    id: 1,
                    rating: 5,
                    title: 'Great class!',
                    description: 'Classes are very upbeat and energetic, pushes me to my limit and helps me keep in shape'
                },
                {
                    id: 2,
                    rating: 4,
                    title: 'Good',
                    description: 'Very knowledgeable when it comes to fitness techniques I have learnt alot'
                }
            ],
            // showAvailabiltyModal: false,
            // selectedPackage: null,
            // showPackageInfoModal: false,
            // showBookingModal: false,
            // displayAuthModal: false,
            profile: {
                trainer: {}
            },
            // packages: [],
            // activePackages: [],
            loadingTrainer: true,
            // loadingAvailability: true,

            // slots: [],
            // selectedSlot: {},
            // selectedSlots: [],
            // currentStep: 1,
            // steps: {
            //     profile: 1,
            //     checkout: 2,
            //     'booking-confirmed': 3
            // },
            // personalBookedSlots: [],
            // externalBookedSlots: [],
            // availableSlots: []
        }
    },
    computed: {
        calendarSlots () {
            return {
                availableSlots: this.availableSlots,
                externalBookedSlots: this.externalBookedSlots,
                personalBookedSlots: this.personalBookedSlots
            }
        }
    },

    mounted() {
        this.loadProfile();
    },

    methods: {
        loadAvailabilityCalendar(data) {
            axios.post('/api/virtual/coaches/' + this.profile.id + '/availability-calendar', {
                week_start: data.weekStart,
                week_end: data.weekEnd
            }).then(response => {

                var all_availability = [];

                response.data.all_availability.forEach(event => {
                    all_availability.push({
                        id: event.id,
                        title: "Availability",
                        start: event.start,
                        end: event.end,
                        backgroundColor: event.type !== 'free-vpt' ? '#2196F3' : '#E91E63',
                        borderColor: 'rgba(0,0,0,0.25)'
                    });
                });

                this.availableSlots = all_availability;

                var my_sessions = [];

                response.data.my_sessions.forEach(event => {
                    my_sessions.push({
                        id: event.id,
                        title: "My Session",
                        start: event.date + ' ' + event.start,
                        end: event.date + ' ' + event.end,
                        backgroundColor: event.type !== 'free-vpt' ? '#2196F3' : '#E91E63',
                        borderColor: 'rgba(0,0,0,0.25)'
                    });
                });

                this.personalBookedSlots = my_sessions;

            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loadingAvailability = false);
        },

        goToBook (selectedPackage = null) {
            this.showAvailabiltyModal = false;
            this.showPackageInfoModal = false;
            this.showBookingModal = true;
            this.$refs.bookingModal.setSelectedPackage(selectedPackage);
        },
        openPackageInfoModal (item) {
            // Is this user logged in?
            if (this.authUser.id === undefined) {
                this.displayAuthModal = true;
            } else {
                this.selectedPackage = item;
                this.showPackageInfoModal = true;
            }
        },
        closePackageInfoModal () {
            this.selectedPackage = null
        },
        stepBack () {
            this.currentStep == 1 ?
                this.$router.go(-1) :
                this.currentStep -= 1;

            this.selectedPackage = null;
            this.selectedSlots = [];
        },
        setStep (step) {
            this.currentStep = this.steps[step];
        },
        goToCheckout (slots) {
            this.selectedSlots = slots;
            this.showBookingModal = false;
            window.scrollTo({top: 0});
            this.setStep('checkout');
        },
        bookActivePackageSlots (slots) {
            this.selectedSlots = slots;
            this.showBookingModal = false;
            // call API
            window.scrollTo({top: 0});
            this.bookingSuccessful();
        },
        bookingSuccessful () {
            this.setStep('booking-confirmed');
        },

        /*
         * Select a slot for booking.
         * @param {object}
         */
        selectSlot(slot) {
            this.selectedSlot = slot;
            this.setStep('confirmation');
        },

        /*
         * Fetch a list of all virtual coaches.
         * @param {none}
         */
        loadProfile() {
            axios.get('/api/virtual/coaches/' + this.$route.params.id).then(response => {
                this.loadingTrainer = false;
                this.profile = response.data.profile;
                this.packages = response.data.profile.packages;
                this.activePackages = response.data.my_packages;
            }).catch(error => {
                this.loadingTrainer = false;
                console.log('ERROR');
            });
        },

        /*
         * Book in session for specified timeslot.
         * @param {none}
         */
        confirmBooking() {
            axios.post('/api/virtual/session/book', {
                slot: this.selectedSlot,
                trainer_id: this.profile.id
            }).then(response => {
                this.slots = response.data;
                this.setStep('booking-confirmed');
            }).catch(error => {
                console.log('ERROR');
            });
        }
    }

}
</script>
