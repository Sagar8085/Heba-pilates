export default {
    data() {
        return {
            payment: null,
        }
    },
    mounted() {
        this.payment = Stripe(process.env.MIX_STRIPE_PUBLISHABLE_KEY);
    },
    methods: {
        getPaymentHandler() {
            return this.payment;
        }
    }
}
