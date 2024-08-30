<template>
        <div class="workouts">
            <div class="navigation">
                <button @click="$router.go(-1)" class="navigation__back"><img src="../../../../public/images/icons/backblue.png" style="height: 15px" alt=""> &nbsp; Back</button>
            </div>

            <div class="workouts__hero" :style="'background-image: linear-gradient(270deg, #FFFFFF00 30%, #00000071 80%, #000000 100%), url(' + category.image + ')'">
                <div class="workouts__container">
                    <div class="workouts__hero__title">{{category.name}}</div>
                    <div class="workouts__hero__text--small">{{category.description}}</div>
                </div>
            </div>

            <div class="workouts__container">
                <h1 class="workouts__container__items__title">Browse</h1>
                <loading-spinner :loading="loading" loadingText="Classes" />
                <div class="workouts__container__items" v-if="category.videos.length > 0">
                    <div v-for="item in category.videos" :key="item.id" class="workouts__container__items__item-col">
                        <div @click="click(item)" class="card-link" :style="item.image ? `background-image: linear-gradient(180deg, #FFFFFF00 20%, #00000071 60%, #000000 100%), url(${item.image})` : ''">
                            <div v-if="item.price_human" class="card-link__tag-container">
                                <label :class="{
                                    'card-link__tag': true,
                                    'card-link__tag--solid': item.price_human === 'PURCHASED',
                                    'card-link__tag--alt': (item.price_human !== 'PURCHASED' && item.price_human !== 'FREE')
                                }">
                                    {{ item.price_human }}
                                </label>
                            </div>
                            <div class="card-link__text-container">
                                <div class="card-link__title">
                                    {{ item.name }}
                                </div>
                                <div class="workouts__container__items__item__text--small">{{ item.excerpt }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="!loading">
                    <p>Sorry we haven't added any classes to this category yet, check back soon and I'm sure we'll have new ones to show you!</p>
                </div>
            </div>

            <stripe-checkout
                ref="checkoutRef"
                mode="payment"
                :pk="publishableKey"
                :line-items="lineItems"
                :success-url="successURL"
                :cancel-url="cancelURL"
                @loading="v => loading = v"
            />

            <div v-if="displayPurchaseModal" :class="{ modal: true, 'modal--active': displayPurchaseModal }">
                <div class="modal__box modal__box--small modal__body">
                    <h2 class="modal__title">{{ this.selectedClass.name }}</h2>

                    <p>You do not currently own this class, you can purchase this class for a 1-off payment of {{ this.selectedClass.price_human }}</p>

                    <div class="modal__buttons">
                        <button class="button button--outline" @click="displayPurchaseModal = false">Cancel</button>
                        <button class="button button--with-icon button--green" @click="submit()" v-if="!purchasing">
                            Buy Now {{ this.selectedClass.price_human }}
                        </button>
                        <button class="button button--with-icon button--green" v-else>
                            <i class="fas fa-spinner fa-spin"></i>One Moment
                        </button>
                    </div>

                    <div class="modal__divider"></div>

                    <h2 class="modal__title">Go Premium</h2>

                    <p>Premium is a great way to get all your fitness content for less, subscribe for £55.99 per month and get unlimited access to all our fitness content.</p>

                    <router-link class="button button--top" to="/myaccount/subscription">
                        View Plans
                    </router-link>
                </div>
            </div>

            <auth-modal v-if="displayAuthModal" v-on:close="displayAuthModal = false" />
        </div>
</template>

<script>
    import { StripeCheckout } from '@vue-stripe/vue-stripe';
    import axios from 'axios';
    import CardLink from '../layout/CardLink.vue'
    import AuthModal from './../../components/AuthModal.vue';
    import LoadingSpinner from '../layout/LoadingSpinner.vue';

    export default {
        components: { CardLink, StripeCheckout, AuthModal, LoadingSpinner },

        props: {
            authUser: Object,
        },
        data () {
            this.publishableKey = 'pk_test_7cnzJxhUjHPpUnNieEjE0GLX00tYinkqfY';

            return {
                categorySlug: '',
                category: {
                    videos: []
                },
                loading: true,

                displayAuthModal: false,
                displayPurchaseModal: false,
                lineItems: [
                    {
                        price: 'price_1IBivXKhJPLSqczXgcZXOwrm', // The id of the one-time price you created in your Stripe dashboard
                        quantity: 1,
                    },
                ],
                successURL: null,
                cancelURL: null,

                selectedClass: null,
                purchasing: false
            };
        },

        mounted() {
        },

        methods: {
            getCategory(){
                axios.get('/api/ondemand/categories/'+this.$route.params.slug).then(response => {
                    this.loading = false;
                    this.category = response.data;
                })
            },

            click(item) {
                if (this.authUser.id === undefined) {
                    this.displayAuthModal = true;
                    return;
                }

                this.selectedClass = item;

                if (item.price_human !== 'PURCHASED' && item.price_human !== 'FREE' && item.price_human !== 'PREMIUM') {
                    console.log('must purchase')
                    this.displayPurchaseModal = true;
                } else {
                    this.$router.push('/on-demand/video/' + item.id);
                }
            },

            // You will be redirected to Stripe's secure checkout page
            submit () {
                this.purchasing = true;
                this.successURL = window.location.origin + '/on-demand/purchase/' + this.selectedClass.id + '/success?stripe_order_id={CHECKOUT_SESSION_ID}';
                this.cancelURL = window.location.origin + '/on-demand/purchase/' + this.selectedClass.id + '/cancel';
                this.$refs.checkoutRef.redirectToCheckout();
            },
        },

        beforeMount(){
            this.getCategory();
        }
    };
</script>
