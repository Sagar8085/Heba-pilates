<template>
    <div>
        <!-- <div class="home wrapper">
            <h1 class="home__title"><span>Welcome Back, {{this.authUser.first_name}}</span></h1>
        </div> -->

        <section class="next-journey wrapper">
            <header class="video-list__header" v-if="suggestedClass.id != null && authUser.role_id === 1 || authUser.role_id === 2">
                <h3 class="video-list__header__title">Next class on your Heba Journey</h3>
            </header>

            <div class="next-journey__box" v-if="suggestedClass.id != null && authUser.role_id === 1 || authUser.role_id === 2">
                <div class="next-journey__content">
                    <img :src="suggestedClass.image">
                    <div>
                        <h3>{{ suggestedClass.name }}</h3>
                        <p>Duration: {{ suggestedClass.duration }} Mins</p>
                        <router-link class="button" :to="'/on-demand/video/' + suggestedClass.id">Watch Now</router-link>
                    </div>
                </div>
            </div>
            <div class="next-journey__box" v-else>
                <div class="next-journey__content">
                    <img src="https://s3.eu-west-2.amazonaws.com/prod.hebepilates/on-demand/images/7UBbmsP26QZiPeawKuhFpWrMr7ctoGOyaf6UHCVV.png">
                    <div>
                        <h3>Heba At Home</h3>
                        <p>Watch pilates classes from the comfort of your own home!</p>
                        <button class="button" style="cursor: default;">Coming Soon</button>
                    </div>
                </div>
            </div>

            <header class="video-list__header" style="margin-top: 2.5rem;">
                <h3 class="video-list__header__title">Reserve some studio time</h3>
            </header>

            <div class="row row--mobile">
                <div class="four columns" v-for="studio in studios" :key="studio.id">
                    <div v-if="studios.length > 0" class="card-list">
                        <div class="card card--padded card--with-bg card--bordered">
                            <h3 class="card__title">{{ studio.name }}</h3>

                            <p class="card__description" v-if="studio.phone_number">Tel: {{ studio.phone_number }}</p>
                            <p class="card__description">Email: {{ studio.email }}</p>
                            <p class="card__description">Address: {{ studio.address }}</p>

                            <div class="card__footer">
                                <router-link class="button button--full" :to="'/studio/' + studio.id">Book Studio Time</router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="this.phone_number === null">
            <Modal v-model="popUpModalPhone" title="Please add a Phone Number" hideCancel hideClose>
                We need a contact number for your account.
                 <router-link style="margin-top: 2.5rem;" class="onboard__form__submit button button--full button--larger button--outline" to="/myaccount/phonenumber">Add Phone Number</router-link>
            </Modal>
        </div>

        <div v-if="introPackAvailable">
            <Modal v-model="introPackAvailable" title="Activate your account now" hideCancel @close="popUpModal = false">
                To get started and make your first booking, simply choose one of our subscriptions or credit packs
                <br><br>
                Don’t forget as a new Heba Guest you can also take advantage of our one time only 3 sessions for £36 Intro Pack offer
                <router-link style="margin-top: 2.5rem;" class="onboard__form__submit button button--full button--larger button--outline" to="/membership">See all memberships and credit packs</router-link>
            </Modal>
        </div>

    </div>
</template>

<script>
    import axios from 'axios';
    import VideoList from './layout/VideoList.vue'
    import Modal from '../components/Modal.vue';

    export default {
        components: { VideoList, Modal },

        props: {
            authUser: Object
        },

        data () {
            return {
                user: {
                    subscription: {}
                },
                sessions: [],
                trainers: [],
                claiming: false,
                trialClaimed: false,
                trialPackage: {},
                selectedTrainer: {},
                pending_subscription: {},
                suggestedClass: [],
                myBookings: [],
                studios: [],
                phone_number: null,
                popUpModal: true,
                popUpModalPhone: true,
                introPackAvailable: false
            };
        },

        mounted() {
            this.loadIsIntroPackAvailable();
            this.loadSuggestedClasses();
            this.loadMyBookings();
            this.loadStudios();
            this.checkPhone();
            console.log(this.phone_number);
        },

        methods: {
            loadSuggestedClasses() {
                axios.get('/api/ondemand/suggested').then(response => {
                    this.suggestedClass = response.data;
                })
            },
            loadMyBookings() {
                axios.get('/api/live/bookings').then(response => {
                    this.myBookings = response.data;
                    this.loading = false;
                }).catch(error => {
                    console.log('ERROR', error);
                    this.loading = false;
                });
            },
            loadStudios () {
                axios.get('/api/gyms').then(response => {
                    this.studios = response.data;
                });
            },
            checkPhone (){
                axios.get('/api/account/phone/check').then(response => {
                    this.phone_number = response.data.phone_number;
                });
            },
            loadIsIntroPackAvailable() {
                axios.get('/api/user/intro-pack/available').then(response => {
                    this.introPackAvailable = response.data.available
                })
            }

        }
    };
</script>
