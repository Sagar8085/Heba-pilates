<template>
    <modal v-model="shouldShow" :hideClose="hideClose">
        <template>
            <div class="form-element">
                <span class="form-element__label">
                    Which membership would you like to purchase?
                </span>

                <div class="form-element__control">
                    <select v-model="tier">
                        <option :value="null" selected disabled>
                            -- Select --
                        </option>
                        <option value="once-weekly-subscription">
                            Once Weekly (£{{ getPriceInPounds(getTierBySlug('once-weekly-subscription')) }})
                        </option>
                        <option value="twice-weekly-subscription">
                            Twice Weekly (£{{ getPriceInPounds(getTierBySlug('twice-weekly-subscription')) }})
                        </option>
                        <option value="ambassador-monthly">
                            Heba Ambassador (£{{ getPriceInPounds(getTierBySlug('ambassador-monthly')) }})
                        </option>
                        <option value="unlimited-membership-subscription-2">
                            Unlimited (£{{ getPriceInPounds(getTierBySlug('unlimited-membership-subscription-2')) }})
                        </option>
                        <option value="one-month-unlimited">
                            One Month - Unlimited (£{{ getPriceInPounds(getTierBySlug('one-month-unlimited')) }})
                        </option>
                        <option value="vip-unlimited-2">
                            Unlimited Annual (£{{ getPriceInPounds(getTierBySlug('vip-unlimited-2')) }})
                        </option>
                        <option value="online" disabled>
                            Online Only - COMING SOON
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    Would you like to pay for this membership, or add it to the
                    members profile free of charge?
                </span>

                <div class="form-element__control">
                    <select v-model="type">
                        <option :value="null" disabled selected>
                            -- Select a Payment Option --
                        </option>
                        <option
                            v-for="type in availableTypes"
                            :key="type.id"
                            :value="type"
                            :disabled="type.disabled"
                        >
                            {{ type.name }}
                        </option>
                    </select>
                </div>
            </div>

            <span
                v-if="error"
                class="form-element__error form-element__error--centered"
            >
                {{ error }}
            </span>

            <button
                slot="buttons"
                class="button"
                v-if="tier && type"
                @click="continuePurchase"
            >
                Continue
            </button>
        </template>
    </modal>
</template>

<script>
import axios from "axios";
import Modal from "../../components/Modal.vue";
import Payment from "../../mixins/payment";

export default {
    components: {
        Modal,
    },

    mixins: [Payment],

    model: {
        prop: "show",
        event: "change",
    },

    props: {
        member: {
            type: String | Number,
            required: true,
        },
        show: {
            type: Boolean,
            default: false,
        },
        hideClose: {
            type: Boolean,
            default: true,
        },
        availableMemberships: {
            type: Array,
            required: true,
        },
    },

    data() {
        return {
            error: null,
            tier: null,
            type: null,
            availableTiers: [],
            availableTypes: [
                {
                    id: "bacs",
                    name: "Take payment now via DD or Card",
                    disabled: false,
                    checkout: true,
                },
                {
                    id: "free",
                    name: "Add for Free",
                    disabled: false,
                    checkout: false,
                },
                {
                    id: "already-paid",
                    name: "Payment Already Taken",
                    disabled: false,
                    checkout: false,
                },
            ],
        };
    },

    computed: {
        shouldShow: {
            get: function () {
                return this.show;
            },
            set: function (value) {
                this.$emit("change", value);
            },
        },
    },

    mounted() {
        // this.loadTiers();
    },

    methods: {
        formatPrice(price) {
            return Intl.NumberFormat("en-GB", {
                style: "currency",
                currency: "GBP",
                maximumFractionDigits: 0,
            }).format(price / 100);
        },

        loadTiers() {
            axios.get("/api/admin/membership-tiers").then((response) => {
                this.availableTiers = response.data.map((tier) => {
                    return {
                        id: tier.slug,
                        name: `${tier.name} (${this.formatPrice(tier.price)})`,
                        disabled: false,
                    };
                });
            });
        },

        continuePurchase() {
            this.error = null;

            if (this.type.checkout === true) {
                this.checkoutMembership();
            } else {
                this.createMembership();
            }
        },

        checkoutMembership() {
            axios
                .post(
                    `/api/admin/members/${this.member}/memberships/checkout`,
                    {
                        tier: this.tier,
                        type: this.type.id,
                    }
                )
                .then((response) => {
                    this.getPaymentHandler()
                        .redirectToCheckout({
                            sessionId: response.data.id,
                        });
                })
                .catch(() => this.error = 'Sorry, there was an error processing your request.');
        },

        createMembership() {
            axios
                .post(`/api/admin/members/${this.member}/memberships`, {
                    tier: this.tier,
                    type: this.type.id,
                })
                .then((response) => {
                    this.$emit("close");
                    this.$emit("change", false);
                    this.$emit("created", response.data);
                })
                .catch((error) => {
                    this.error =
                        error.response.data.message ||
                        "Unable to add membership";
                });
        },

        getTierBySlug(slug) {
            const tier = this.availableMemberships.filter(tier => tier.slug === slug)[0];

            if (tier === undefined) {
                return {
                    subscription_tier_prices: [
                        {
                            price_in_pence: 99999,
                        },
                    ],
                };
            }

            return tier;
        },

        getPriceInPounds(subscriptionTier) {
            return subscriptionTier.subscription_tier_prices[0].price_in_pence / 100;
        },
    },
};
</script>
