<template>
    <AccountPageWrapper class="account-page--settings">
        <section class="account__section">
            <h1>Profile Settings</h1>

            <div class="account-info">
                <profile-picture size="large" :image="authUser.avatar" :firstName="authUser.first_name" :lastName="authUser.last_name" />
                <change-avatar-dropdown @avatarUpdated="updateAvatar" />
            </div>

            <div class="account-info">
                <span class="account-info__title">{{this.authUser.name}}</span>
                <router-link class="account-info__button" to="/myaccount/name">Change name</router-link>
            </div>

            <div class="account-info">
                <span class="account-info__title">{{this.authUser.email}}</span>
                <router-link class="account-info__button" to="/myaccount/email">Change email</router-link>
            </div>

            <div class="account-info">
                <span class="account-info__title">{{this.authUser.phone_number}}</span>
                <router-link class="account-info__button" to="/myaccount/phonenumber">Change Phone Number</router-link>
            </div>

            <div class="account-info">
                <span class="account-info__title">Password: <span style="color: #C4C7BE">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span></span>
                <router-link class="account-info__button" to="password/change">Change password</router-link>
            </div>

<!-- ------------------------------Studio Name--------------------------------------------------- -->
             <!-- <div class="account-info">
                <span class="account-info__title">{{this.authUser.gym.name}} <span style="color: #C4C7BE">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span></span>
                <router-link class="account-info__button" to="password/change">Change Studio</router-link>

            </div> -->

            <div class="account-info">
                <span class="account-info__title">{{this.authUser.gym.name}}</span>
                <router-link class="account-info__button" to="/Profile_studio/change">Change Studio</router-link>
                <!-- <button v-on:click="handler">check</button> -->
            </div>

<!-- --------------------------------------------------------------------------------- -->  

            <!-- <div class="account-info">
                <div class="account-info__qr-code"></div>
                <button class="account-info__button">Refresh QR Code</button>
            </div> -->
        </section>

        <section class="account__section account__section--compact">
            <h2 class="account__section__title">Notification Preferences</h2>

            <div v-for="preference in notificationCategories" :key="preference.value" class="account-info">
                <span class="account-info__title">{{ preference.label }}</span>
                <form-switch-input v-model="notificationPreferences[preference.value]" @change="updateNotificationPreferences" />
            </div>
        </section>

        <section class="account__section">
            <h2 class="account__section__title">Content Preferences</h2>

            <div class="account-info" v-if="contentPreferencesLoaded">
                <button-options v-model="contentPreferences" :options="contentCategories" :columns="4" multiple @input="updateContentPreference" />
            </div>
        </section>

        <!-- <section class="account__section">
            <h2 class="account__section__title">Language</h2>

            <div class="account-info">
                <span class="account-info__title">English</span>
                <button class="account-info__button">Change language</button>
            </div>
        </section> -->

        <section class="account__section">
            <h2 class="account__section__title">Account Security</h2>

            <div class="account-info">
                <span class="account-info__title">Delete My Account</span>
                <button class="account-info__button" @click="displayDeleteAccountModal = true">Start Process</button>
            </div>

            <hr style="opacity: .5">

            <div class="account-info">
                <span class="account-info__title">Multi Device Authentication</span>
                <button class="account-info__button" @click="displayLogoutAllDevicesModal = true">Sign out all devices</button>
            </div>

            <div class="account-info">
                <span class="account-info__title">Logout This Browser</span>
                <button class="account-info__button" @click="logout()">Logout Now</button>
            </div>
            <!-- <div class="account-info">
                <span class="account-info__title">Recent activity</span>
                <router-link class="account-info__button" to="/streaming-activity">View streaming activity</router-link>
            </div> -->
        </section>

        <Modal v-model="displayLogoutAllDevicesModal" title="Are you sure you want to logout all devices using your account?" hideCancel @close="displayLogoutAllDevicesModal = false">
            You will have to manually sign back in on each device you want to use.
            <template slot="buttons">
                <button class="button button--outline button--full" @click="displayLogoutAllDevicesModal = false">Cancel</button>
                <button class="button button--full" @click="logoutAllDevices">Confirm Logout</button>
            </template>
        </Modal>

        <Modal v-model="displayAllDevicesLoggedOutModal" title="Logout Successfull" hideCancel>
            You have now been logged out of your account on all devices, we have automatically kept you signed in here so you won't have to re-signin on your browser.
            <template slot="buttons">
                <button class="button button--outline button--full" @click="displayAllDevicesLoggedOutModal = false">Okay</button>
            </template>
        </Modal>

        <Modal v-model="displayDeleteAccountModal" title="Delete Your Account">
            Are you sure you want to delete your Heba Pilates account? You will loose:
            <ul style="margin: 1rem 0; font-size: .85rem">
                <li><strong>Account Access</strong> - You will not be able to login to your account on the web or via the app.</li>
                <li><strong>Club Access</strong> - You won't be able to access our phyiscal studios without your mobile apps QR code.</li>
            </ul>
            <template slot="buttons">
                <button class="button button--red button--full" @click="deleteAccount()">I Am Sure, Delete Account</button>
            </template>
        </Modal>
    </AccountPageWrapper>
</template>

<script>
import axios from 'axios';
import AccountPageWrapper from './AccountPageWrapper.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';
import FormSwitchInput from '../../components/FormSwitchInput.vue';
import ButtonOptions from '../../components/ButtonOptions.vue';
import ChangeAvatarDropdown from './ChangeAvatarDropdown.vue';
import Modal from '../../components/Modal.vue';

export default {
    components: { AccountPageWrapper, ProfilePicture, FormSwitchInput, ButtonOptions, ChangeAvatarDropdown, Modal },
    props: {
        authUser: Object
    },

    data () {
        return {
            displayCancelAutoRenewModal: false,
            displayAutoRenewCancelledModal: false,
            cancelling: false,
            selected: '',
            gyms: [],
            notificationCategories: [
                { label: 'Notify me about changes to my account', value: 'account' },
                { label: 'Notify me about new content you think i\'d like', value: 'new_content' },
                { label: 'Notify and remind me about my upcoming bookings and cancellations', value: 'bookings' },
                { label: 'Send me marketing emails including tips and offers', value: 'marketing' }
            ],
            notificationPreferences: {},
            contentCategories: [
                { label: 'Category Name', value: 'category1' },
                { label: 'Category Name', value: 'category2' },
                { label: 'Category Name', value: 'category3' },
                { label: 'Category Name', value: 'category4' },
                { label: 'Category Name', value: 'category5' },
                { label: 'Category Name', value: 'category6' },
            ],
            contentPreferences: ['category2', 'category5'],

            displayLogoutAllDevicesModal: false,
            displayAllDevicesLoggedOutModal: false,

            contentPreferencesLoaded: false,
            displayDeleteAccountModal: false
        };
    },

    mounted() {
        this.loadNotificationPreferences();
        this.loadContentPreferences();
    },

    methods: {
        /*
         * Log user out of all devices.
         * @param {none}
         */
        logoutAllDevices() {
            axios.post('/api/account/logout/all-devices').then(response => {
                localStorage.setItem('fc-usertoken', response.data.api_token);
                console.log('new token = ' + response.data.api_token)
                this.displayLogoutAllDevicesModal = false;
                this.displayAllDevicesLoggedOutModal = true;
                window.location.reload();
            });
        },

           handler()
        {
            console.log("authUsers>>>>>>>>>>>>>>",this.authUser)
            axios.get('/api/user').then(response=>
            {
                console.log("User Response>>>>>>",response.data)
            })
        },


        /*
         * Load content preferences.
         * @param {none}
         */
        loadContentPreferences() {
            axios.get('/api/account/content-preferences').then(response => {
                this.contentCategories = response.data.categories;
                this.contentPreferences = response.data.preferences;
                this.contentPreferencesLoaded = true;
            });
        },

        /*
         * Load notification preferences.
         * @param {none}
         */
        loadNotificationPreferences() {
            axios.get('/api/account/notification-preferences').then(response => {
                this.notificationPreferences = response.data;
            });
        },

        updateNotificationPreferences() {
            axios.post('/api/account/notification-preferences', { preferences: this.notificationPreferences }).then(response => {

            });
        },

        logout() {
            localStorage.removeItem('fc-usertoken');
            sessionStorage.removeItem('fc-usertoken');
            window.location.href = '/login';
        },

        updateContentPreference(preference) {
            axios.post('/api/account/content-preferences', { preference: preference }).then(response => {

            });
        },

        deleteAccount() {
            axios.delete('/api/account').then(response => {
                this.logout();
            });
        },

        updateAvatar(e) {
            this.authUser.avatar = e;
        }
    }
};
</script>
