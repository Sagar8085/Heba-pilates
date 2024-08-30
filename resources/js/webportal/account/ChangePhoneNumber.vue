<template>
    <div class="auth__wrapper">
        <form class="account wrapper" @submit.prevent="update">
            <h1>Change Phone Number</h1>

            <div class="account-info account-info--col">
                <label class="account-info__label">Current Phone Number</label>
                <span class="account-info__content">{{ authUser.phone_number }}</span>
            </div>

            <div class="auth__group">
                <form-input v-model="phoneNumber" label="New Phone Number" />
            </div>

            <div class="account-info account-info--inline">
                <router-link to="/myaccount" class="button button--outline">Cancel</router-link>
                <button class="button" :disabled="saveDisabled" @click.prevent="update">Save</button>
            </div>

        </form>
    </div>
</template>

<script>
    import FormInput from '../../components/FormInput.vue'

    export default {
        props: {
            authUser: Object,
        },
        components: {
            FormInput
        },

        data () {
            return {
                errors: [],
                phoneNumber: '',
            };
        },

        computed: {
            saveDisabled () {
                return this.phoneNumber.length == 0;
            }
        },

        methods: {
            update() {
                if (this.saveDisabled) return;

                axios.patch('/api/account/phone', {
                    phone_number: this.phoneNumber,
                }).then(response => {
                    this.$router.push('/myaccount')
                }).catch(error => {
                    console.log('ERROR');
                    this.errors = error.response.data.errors;
                });
            }
        }
    };
</script>
