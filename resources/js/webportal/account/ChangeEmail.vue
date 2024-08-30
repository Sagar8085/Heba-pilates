<template>
    <div class="auth__wrapper">
        <form class="account wrapper" @submit.prevent="updateEmail">
            <h1>Change Email</h1>

            <div class="account-info account-info--col">
                <label class="account-info__label">Current Email</label>
                <span class="account-info__content">{{ authUser.email }}</span>
            </div>

            <div class="auth__group">
                <form-input v-model="newEmail" label="New Email" />
                <form-input v-model="password" label="Current Password" type="password">
                    <template v-slot:label>
                        <div class="auth__label">Current Password</div>
                    </template>
                </form-input>
            </div>

            <p v-if="saveError">Your password is incorrect.</p>

            <div class="account-info account-info--inline">
                <router-link to="/myaccount" class="button button--outline">Cancel</router-link>
                <button class="button" :disabled="saveDisabled" @click="updateEmail">Save</button>
            </div>

        </form>
    </div>
</template>

<script>
    import FormInput from '../../components/FormInput.vue';

    export default {
        props: {
            authUser: Object,
        },

        components: {
            FormInput
        },

        data () {
            return {
                newEmail: '',
                password: '',
                saveError: false
            };
        },

        computed: {
            saveDisabled () {
                return this.newEmail.length == 0 || this.password.length == 0;
            }
        },

        methods: {
            updateEmail(){
                if (this.saveDisabled) return;

                axios.patch('/api/account/email', {
                    email: this.newEmail,
                    password: this.password
                }).then(response => {

                    if (response.data.status !== 'success') {
                        this.saveError = true;
                    } else {
                        this.saveError = false;
                        this.$router.push('/myaccount')
                    }
                }).catch(error => {
                    console.log('ERROR');
                    this.errors = error.response.data.errors;
                });
            }
        }
    };
</script>
