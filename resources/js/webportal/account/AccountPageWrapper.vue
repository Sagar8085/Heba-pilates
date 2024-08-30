<template>
<div class="account-page">
    <div class="wrapper">
        <nav class="account-page__sidebar">
            <router-link class="account-page__sidebar__link" to="/bookings">
                <i class="material-icons">event</i>
                Bookings
            </router-link>
            <router-link class="account-page__sidebar__link" to="/myaccount">
                <i class="material-icons">account_circle</i>
                Profile Settings
            </router-link>
            <router-link class="account-page__sidebar__link" to="/myaccount/qr">
                <i class="material-icons">qr_code</i>
                <span>
                    <span class="hide-md">View My&nbsp;</span>QR Code
                </span>
            </router-link>
            <router-link class="account-page__sidebar__link" to="/myaccount/qr/scan">
                <i class="material-icons">qr_code</i>
                <span>
                    Scan QR
                </span>
            </router-link>
            <router-link class="account-page__sidebar__link" to="/membership">
                <i class="material-icons">local_atm</i>
                <span>
                    Memberships and Credit<span class="hide-md">&nbsp;Packs</span>
                </span>
            </router-link>
            <button class="account-page__sidebar__link account-page__sidebar__link--logout" @click="logout">
                <i class="material-icons">logout</i>
                Logout
            </button>
        </nav>
        <div class="account-page__content">
            <slot></slot>
        </div>
    </div>
</div>
</template>

<script>
export default {
    methods: {
        logout() {
            localStorage.removeItem('fc-usertoken');
            sessionStorage.removeItem('fc-usertoken');
            window.location.href = '/login';
        }
    }
}
</script>
