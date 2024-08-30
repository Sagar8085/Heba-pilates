<template>
    <div class="auth__wrapper">
        <form class="account wrapper" @submit.prevent="update">
            <h1>Change Name</h1>

            <div class="account-info account-info--col">
                <label class="account-info__label">Current Name</label>
                <span class="account-info__content">{{ authUser.name }}</span>
            </div>

            <div class="auth__group">
                <form-input v-model="firstName" label="New First Name" />
                <form-input v-model="lastName" label="New Last Name" />
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

                firstName: '',
                lastName: ''
            };
        },

        computed: {
            saveDisabled () {
                return this.firstName.length == 0 || this.lastName.length == 0;
            }
        },

        methods: {
            update() {
                if (this.saveDisabled) return;

                axios.patch('/api/account/name', {
                    first_name: this.firstName,
                    last_name: this.lastName
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
