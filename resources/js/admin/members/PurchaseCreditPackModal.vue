<template>
    <modal v-model="shouldShow" :hideClose="hideClose">
        <template>
            <div class="form-element">
                <span class="form-element__label">
                    Which Credit Pack would you like to purchase?
                </span>

                <div class="form-element__control">
                    <select v-model="pack">
                        <option :value="null" selected disabled>
                            -- Select --
                        </option>
                        <option value="1">Intro Pack - £{{ getPriceInPounds(getPackById(1)) }}</option>
                        <option value="2">Single Session - £{{ getPriceInPounds(getPackById(2)) }}</option>
                        <option value="13">10 Sessions - £{{ getPriceInPounds(getPackById(13)) }}</option>
                        <option value="12">30 Sessions - £{{ getPriceInPounds(getPackById(12)) }}</option>
                        <option value="19">4 Sessions Promo - £{{ getPriceInPounds(getPackById(19)) }}</option>
                        <option value="20">Autumn Incentive - £{{ getPriceInPounds(getPackById(20)) }}</option>
                        <option value="5">1 Visit for £1 - £1.00</option>
                        <option value="11">Unlimited Promo - £49</option>
                        <option value="15">No Show - £5</option>
                        <option value="16">Gifted credit - FREE</option>
                        <option value="17">Staff Unlimited - FREE</option>
                    </select>
                </div>
            </div>

            <div class="form-element">
                <span class="form-element__label">
                    Would you like to pay for this credit pack, or add it to the
                    members profile free of charge?
                </span>

                <div class="form-element__control">
                    <select v-model="type">
                        <option :value="null" disabled selected>
                            -- Select a Payment Option --
                        </option>
                        <option
                            v-for="type in typeOptions"
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
                v-if="pack && type"
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
        availablePacks: {
            type: Array,
            required: true,
        }
    },

    data() {
        return {
            error: null,
            pack: null,
            type: null,
            availableTypes: [
                {
                    id: "card",
                    name: "Pay via Credit/Debit Card",
                    disabled: false,
                    checkout: true,
                },
                {
                    id: "free",
                    name: "Add for Free (If the user has not, or will not make a payment)",
                    disabled: false,
                    checkout: false,
                },
                {
                    id: "prepaid",
                    name: "Already Paid (If a user has already made a payment)",
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

        noCharge() {
            return this.pack == 16 || this.pack == 17;
        },

        typeOptions() {
            return this.availableTypes.filter((type) =>
                this.noCharge ? !type.checkout : true
            );
        },
    },

    methods: {
        continuePurchase() {
            this.error = null;

            if (this.type.checkout === true) {
                this.checkoutPack();
            } else {
                this.addPack();
            }
        },

        checkoutPack() {
            axios
                .post(
                    `/api/admin/members/${this.member}/credit-packs/checkout`,
                    {
                        creditPackId: this.pack,
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

        addPack() {
            axios
                .post(`/api/admin/members/${this.member}/credit-packs`, {
                    creditPackId: this.pack,
                    type: this.type.id,
                })
                .then((response) => {
                    this.$emit("close");
                    this.$emit("change", false);
                    this.$emit("added", response.data);
                })
                .catch((error) => {
                    this.error =
                        error.response.data.message || "Unable to add pack";
                });
        },

        getPackById(id) {
            return this.availablePacks.filter(pack => pack.id == id)[0];
        },

        getPriceInPounds(creditPack) {
            return creditPack
                ? creditPack.credit_pack_prices[0].price_in_pence / 100
                : 'Not set';
        },
    },

    watch: {
        noCharge() {
            if (this.noCharge && this.type?.checkout === true) {
                this.type = null;
            }
        },
    },
};
</script>
