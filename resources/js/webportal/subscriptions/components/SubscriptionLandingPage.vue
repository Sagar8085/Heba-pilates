<template>
    <div class="subscription-lp__wrapper">
        <div v-if="isPromo" class="subscription-lp__promo subscription-lp__title">
            Limited time only promotional rate! <br/>Limited spaces available!
        </div>
        <div :class="{ 'subscription-lp__container': true, 'subscription-lp__container--promo': isPromo }">
            <div class="subscription-lp__logo">
                <img src="/images/logos/heba-logo.png" alt="Heba Pilates" title="Heba Pilates" width="75" height="20"/>
            </div>

            <div class="subscription-lp__info">
                <p class="subscription-lp__subtitle">You have chosen:new</p>
                <h2 v-html="title" class="subscription-lp__title"></h2>
                <div class="subscription-lp__grid">
                    <slot name="info"></slot>
                </div>
            </div>

            <div class="subscription-lp__user">
                <h2 class="subscription-lp__title">Create your Heba profile</h2>

                <p class="subscription-lp__title subscription-lp__title--medium">Contact Details</p>

                <div class="subscription-lp__form">
                    <form-input
                        v-model="details.first_name"
                        label="First Name"
                        :error="getError('first_name')"
                        required
                    />
                    <form-input
                        v-model="details.last_name"
                        label="Last Name"
                        :error="getError('last_name')"
                        required
                    />
                    <form-input
                        v-model="details.email"
                        label="Email"
                        type="email"
                        :error="getError('email')"
                        required
                    />
                    <form-input
                        v-model="details.phone_number"
                        label="Phone Number"
                        type="number"
                        :error="getError('phone_number')"
                        allowStartingZero
                        required
                    />

                    <form-input
                        v-model="details.gender"
                        label="Select your gender"
                        type="select"
                        staticLabel
                        placeholder="Select..."
                        :error="getError('gender')"
                    >
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Prefer not to say</option>
                    </form-input>

                    <form-input
                        label="Please choose your preferred home studio location"
                        v-model="details.home_studio_id"
                        type="select"
                        placeholder="Select Studio..."
                        required
                        :error="getError('home_studio_id')"
                    >
                        <option
                            v-for="studio in availableStudios"
                            :value="studio.id"
                            :key="studio.id"
                        >
                            {{ studio.name }}
                        </option>
                    </form-input>

                    <form-input
                        label="How did you hear about us?"
                        v-model="details.marketing_hear_about_us"
                        type="select"
                        placeholder="Select..."
                        :error="getError('marketing_hear_about_us')"
                    >
                        <option value="referral">Referral</option>
                        <option value="google">Google</option>
                        <option value="social-media">Social media</option>
                        <option value="website">Website</option>
                        <option value="leaflet-flyer">Leaflet / Flyer</option>
                        <option value="local-event">Local Event</option>
                        <option value="local-signs">Local Signs</option>
                        <option value="radio">Radio</option>
                        <option value="tv">TV</option>
                        <option value="returning-guest">Returning Guest</option>
                    </form-input>
                    <button class="button" @click="next">Next: Payment</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from '../../../components/FormInput.vue'
import axios from 'axios';
import Payment from "../../../mixins/payment";

export default {
    components: {FormInput},

    mixins: [Payment],

    props: {
        title: String,
        isPromo: {
            type: Boolean,
            default: false
        },
        isLondon: {
            type: Boolean,
            default: false
        },
        id: {
            default: 0
        },
        type: {
            type: String,
            default: 'payment'
        },
        forAllLocations: {
            type: Boolean,
            default: false,
        }
    },

    data() {
        return {
            details: {
                first_name: '',
                last_name: '',
                email: '',
                phone_number: '',
                gender: '',
                home_studio_id: '',
                marketing_hear_about_us: ''
            },
            errors: [],

            studios: []
        }
    },

    computed: {
        availableStudios() {
            const londonStudioIds = [5, 6];

            const excludedIds = [4]; // Exclude Spain

            if (this.forAllLocations) {
                return this.studios.filter(studio => !excludedIds.includes(studio.id));
            }

            return this.isLondon
                ? this.studios.filter(studio => londonStudioIds.includes(studio.id))
                : this.studios.filter(studio => !londonStudioIds.includes(studio.id) && !excludedIds.includes(studio.id))
        },
        formData() {
            return {
                first_name: this.details.first_name,
                last_name: this.details.last_name,
                email: this.details.email,
                phone_number: this.details.phone_number,
                gender: this.details.gender,
                home_studio_id: this.details.home_studio_id,
                marketing_hear_about_us: this.details.marketing_hear_about_us,
            };
        },
    },
    methods: {
        getError(key) {
            return lodash.get(this.errors, key + '.0');
        },
        getStudios() {
            axios.get('/api/gyms').then((response) => this.studios = response.data)
        },

        next() {
            axios.post('/api/payment-confirmation', this.formData)
                .then(response => {
                    localStorage.setItem('fc-usertoken', response.data.user.api_token);
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.user.api_token;

                    let destUrl = `/api/account/credit-packs/${this.id}/checkout`;

                    if (this.type === 'subscription') {
                        destUrl = `/api/account/subscription/stripe/bacs-checkout?tier=${this.id}`;
                    }

                    axios.post(destUrl, {
                        gymId: this.details.home_studio_id,
                        mode: this.type,
                    }).then(response => {
                        this.getPaymentHandler()
                            .redirectToCheckout({
                                sessionId: response.data.id
                            });
                    });

                }).catch(({response}) => this.errors = response.data.errors);
        },
    },

    mounted() {
        this.getStudios()
    }
}
</script>
