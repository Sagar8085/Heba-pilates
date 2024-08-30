<template>
    <div class="auth__wrapper">
        <form class="wrapper account" @submit.prevent="updatePassword">
            <h1>Change Password</h1>

            <div class="auth__group">
                <form-input v-model="currentPassword" label="Current Password" type="password" autocomplete="new-password">
                    <template v-slot:label>
                        <div class="auth__label">Current Password</div>
                    </template>
                </form-input>

                <form-input v-model="newPassword" label="New Password" type="password" autocomplete="new-password" />
                <form-input v-model="newPasswordConfirmation" label="Confirm New Password" type="password" autocomplete="new-password" />
            </div>

            <p v-if="invalidPassword">Your password is incorrect.</p>
            <p v-if="passwordMismatch">Your new passwords do not match.</p>

            <div class="account-info account-info--inline">
                <router-link to="/myaccount" class="button button--outline">Cancel</router-link>
                <button class="button" :disabled="saveDisabled" @click.prevent="updatePassword">Save</button>
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
                currentPassword: '',
                newPassword: '',
                newPasswordConfirmation: '',

                invalidPassword: false,
                passwordMismatch: false
            };
        },

        computed: {
            saveDisabled () {
                return this.currentPassword.length == 0
                    || this.newPassword.length == 0
                    || this.newPasswordConfirmation.length == 0
                    || this.newPassword !== this.newPasswordConfirmation;
            }
        },

        methods: {
            updatePassword(){
                if (this.saveDisabled) return;

                axios.patch('/api/account/password', {
                    current_password: this.currentPassword,
                    new_password: this.newPassword,
                    new_password_confirmation: this.newPasswordConfirmation
                }).then(response => {
                    if (response.data.status !== 'success') {
                        this.invalidPassword = true;
                    } else {
                        this.invalidPassword = false;
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
