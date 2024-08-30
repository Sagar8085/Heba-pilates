import Vue from 'vue'
import VueRouter from 'vue-router';
import axios from 'axios';
import VueSelectImage from 'vue-select-image';
import JQuery from 'jquery';
import VueVideoPlayer from 'vue-video-player'
import 'video.js/dist/video-js.css'
import 'vue-video-player/src/custom-theme.css'
import 'videojs-contrib-hls/dist/videojs-contrib-hls'
import lodash from 'lodash';

window.$ = JQuery;
window.axios = axios;
window.lodash = lodash;

Vue.use(VueRouter);
Vue.use(VueSelectImage);
Vue.use(VueVideoPlayer);

const token = localStorage.getItem('fc-usertoken') || sessionStorage.getItem('fc-usertoken');

if (token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
}

const ifNotAuthenticated = (to, from, next) => {
    if (!token) {
        next()
        return
    }
    next('/')
}

const ifAuthenticated = (to, from, next) => {
    if (token) {
        next()
        return
    }

    next('/login')
}

const redirectToOnboarding = (to, from, next) => {
    token ? next('/') : next('/onboard');
}

const redirectToOnboardingWelcome = (to, from, next) => {
    token ? next() : next('/onboard');
}

const redirectToFirstOnboardingStep = (to, from, next) => {
    token ? next('/onboard/goals') : next();
}

Vue.component('App', require('./App.vue').default);

import Home from './Home.vue';
import LandingPage from './LandingPage.vue';

/*
 * Authentication.
 */
import SingleSignOn from './auth/SSO.vue';
import Login from './auth/Login.vue';
import Register from './auth/Register.vue';
import ChangePassword from './auth/ChangePassword.vue';
import ForgotPassword from './auth/ForgotPassword.vue';
import ResetPassword from './auth/ResetPassword.vue';
import ConfirmAccount from './auth/ConfirmAccount.vue';

import OnboardGetStarted from './auth/onboard/GetStarted.vue';
import OnboardAccountDetails from './auth/onboard/AccountDetails.vue';
import OnboardGoals from './auth/onboard/SetGoals.vue';
import OnboardFocus from './auth/onboard/SetFocus.vue';
import OnboardBodyPartFocus from './auth/onboard/SetBodyPartFocus.vue';
import OnboardFitnessLevel from './auth/onboard/FitnessLevel.vue';
import OnboardPilatesExperience from './auth/onboard/PilatesExperience.vue';
import OnboardPARQ from './auth/onboard/PARQ.vue';
import OnboardIntroPack from './auth/onboard/IntroPack.vue';
import OnBoardMembership from './auth/onboard/OnBoardMembership.vue';

import Download from './Download.vue';

/*
 * On Demand.
 */
import OnDemand from './on-demand/Main.vue';
import OnDemandCategory from './on-demand/Category.vue';
import OnDemandSingle from './on-demand/Single.vue';
import OnDemandPurchaseResponse from './on-demand/PurchaseResponse.vue';

/*
 * Live Classes.
 */
import LiveClasses from './live-classes/Main.vue';
import LiveClassSingle from './live-classes/Single.vue';
import LiveClassCategory from './live-classes/Category.vue';
import LiveClassBooking from './live-classes/Booking.vue';
import LiveClassFilterResults from './live-classes/FilterResults.vue';

/*
 * Studio Booking.
 */
import StudioMain from './studio/Main.vue';
import StudioSingle from './studio/Single.vue';

/*
 * Account.
 */

import MyAccount from './account/Main.vue';
import AccountName from './account/ChangeName.vue';
import AccountEmail from './account/ChangeEmail.vue';
import AccountAvatar from './account/ChangeAvatar.vue';
import AccountPhoneNumber from './account/ChangePhoneNumber.vue';
import AccountSubscription from './account/AccountSubscription.vue';
import AccountQR from './account/QRCode.vue';
import AccountScanQR from './account/ScanQRCode.vue';
import CancelSubscription from './account/CancelSubscription.vue';
import AccountPurchaseResponse from './account/PurchaseResponse.vue';
import MembershipsChooseStudio from './auth/ChooseStudio.vue';
import ProfileChooseStudio from '../webportal/account/ChooseStudio.vue';
import AccountMembership from './account/AccountMembership.vue';
import AccountBookings from './account/AccountBookings.vue';
import DirectDebitResponse from './account/DirectDebitResponse.vue';
import CreditPackPurchaseResponse from './account/CreditPackPurchaseResponse.vue';
import StreamingActivity from './account/StreamingActivity.vue';

/*
 * Help.
 */
import HelpMain from './help/Main.vue';
import HelpSingle from './help/Single.vue';
import HelpArticles from './help/AllArticles.vue';
import HelpSingleCategory from './help/SingleCategory.vue';
import HelpSearch from './help/Search.vue';

/*
 * Misc.
 */
import Terms from './legal/Terms.vue';
import PrivacyPolicy from './PrivacyPolicy.vue';
import CancellationPolicy from './legal/Cancellation.vue';
import Notifications from './notifications/Main.vue';
import Contact from './Contact.vue';

import SubscriptionLandingPage2for49_tier_two from './subscriptions/2for49_tier_two.vue';
import SubscriptionLandingPage2for69_tier_one from './subscriptions/2for69_tier_one.vue';
import SubscriptionLandingPage2for49_tier_one_promo from './subscriptions/2for49_tier_one_promo.vue';
import SubscriptionLandingPageUnlimited_tier_two from './subscriptions/unlimited_tier_two.vue';
import SubscriptionLandingPageUnlimited_tier_one from './subscriptions/unlimited_tier_one.vue';
import SubscriptionLandingPageTwice_weekly_tier_two from './subscriptions/twice_weekly_tier_two.vue';
import SubscriptionLandingPageTwice_weekly_tier_one from './subscriptions/twice_weekly_tier_one.vue';
import SubscriptionLandingPageOnce_weekly_tier_two from './subscriptions/once_weekly_tier_two.vue';
import SubscriptionLandingPageOnce_weekly_tier_one from './subscriptions/once_weekly_tier_one.vue';
import SubscriptionLandingPageAnnual_vip_tier_two from './subscriptions/annual_vip_tier_two.vue';
import SubscriptionLandingPageAnnual_vip_tier_one from './subscriptions/annual_vip_tier_one.vue';
import SubscriptionLandingPageThirty_sessions_tier_two from './subscriptions/thirty_sessions_tier_two.vue';
import SubscriptionLandingPageThirty_sessions_tier_one from './subscriptions/thirty_sessions_tier_one.vue';
import SubscriptionLandingPageTen_sessions_tier_two from './subscriptions/ten_sessions_tier_two.vue';
import SubscriptionLandingPageTen_sessions_tier_one from './subscriptions/ten_sessions_tier_one.vue';
import SubscriptionLandingPageSingle_session_tier_two from './subscriptions/single_session_tier_two.vue';
import SubscriptionLandingPageSingle_session_tier_one from './subscriptions/single_session_tier_one.vue';
import SubscriptionLandingPageFourSessionStarterPack from './subscriptions/4_session_starter_pack.vue';
import SubscriptionLandingPageSeasonalTierOne from './subscriptions/seasonal_tier_one.vue';
import SubscriptionLandingPageSeasonalTierTwo from './subscriptions/seasonal_tier_two.vue';

import InstructorProfile from './instructors/Profile.vue';

import SearchResults from './Search.vue';
import PageNotFound from './PageNotFound.vue';

Vue.component('workout-builder-item', require('../components/WorkoutBuilderItem.vue').default);
Vue.component('active-excercise', require('../components/ActiveExcercise.vue').default);
Vue.component('view-booking-card', require('../components/ViewBookingCard.vue').default);


export const routes = [

    // beforeEnter: ifAuthenticated check to be added back in once backend APIs are built. In the meantime, it's been left commented out next to it's corresponding path.

    {path: '/login', component: Login, name: 'Login', beforeEnter: ifNotAuthenticated},
    {path: '/signup', component: Register, name: 'Register', beforeEnter: redirectToOnboarding},
    {path: '/cancellation-policy', component: CancellationPolicy, name: 'CancellationPolicy'},
    {path: '/terms', component: Terms, name: 'Terms'},
    {path: '/privacy', component: PrivacyPolicy, name: 'PrivacyPolicy'},
    {path: '/helloworld', component: PrivacyPolicy, name: 'PrivacyPolicy'},
    {path: '/forgot', component: ForgotPassword, name: 'ForgotPassword'},
    {path: '/reset/:token', component: ResetPassword, name: 'ResetPassword'},
    {path: '/confirm/:token', component: ConfirmAccount, name: 'ConfirmAccount'},
    {path: '/', component: () => token ? import('./Home.vue') : import('./LandingPage.vue'), name: 'Home'},
    {path: '/password/change', component: ChangePassword, name: 'ChangePassword'}, // beforeEnter: ifAuthenticated
    {path: '/membership_studio/change', component: MembershipsChooseStudio, name: 'ChooseStudio'},
    {path: '/Profile_studio/change', component: ProfileChooseStudio, name: 'ChooseStudio'},
    {path: '/sso/:token', component: SingleSignOn, name: 'SingleSignOn'},
    {path: '/contact', component: Contact, name: 'Contact'},
    {path: '/search', component: SearchResults, name: 'SearchResults'},
    {path: '/download', component: Download, name: 'Download'},

    {path: '/onboard', component: OnboardGetStarted, name: 'OnboardGetStarted'},
    {
        path: '/onboard/account',
        component: OnboardAccountDetails,
        name: 'OnboardAccountDetails',
        beforeEnter: redirectToFirstOnboardingStep
    },
    {path: '/onboard/goals', component: OnboardGoals, name: 'OnboardGoals', beforeEnter: redirectToOnboardingWelcome},
    {path: '/onboard/focuses', component: OnboardFocus, name: 'OnboardFocus', beforeEnter: redirectToOnboardingWelcome},
    {
        path: '/onboard/body-part-focuses',
        component: OnboardBodyPartFocus,
        name: 'OnboardBodyPartFocus',
        beforeEnter: redirectToOnboardingWelcome
    },
    {
        path: '/onboard/fitness-level',
        component: OnboardFitnessLevel,
        name: 'OnboardFitnessLevel',
        beforeEnter: redirectToOnboardingWelcome
    },
    {
        path: '/onboard/pilates-experience',
        component: OnboardPilatesExperience,
        name: 'OnboardPilatesExperience',
        beforeEnter: redirectToOnboardingWelcome
    },
    {path: '/onboard/parq', component: OnboardPARQ, name: 'OnboardPARQ', beforeEnter: redirectToOnboardingWelcome},
    {
        path: '/onboard/intro-pack',
        component: OnboardIntroPack,
        name: 'OnboardIntroPack',
        beforeEnter: redirectToOnboardingWelcome
    },
    {
        path: '/onboard/membership',
        component: OnBoardMembership,
        name: 'OnBoardMembership',
        beforeEnter: redirectToOnboardingWelcome
    },

    {path: '/instructors/:id', component: InstructorProfile, name: 'InstructorProfile'},

    {path: '/live-classes', component: LiveClasses, name: 'LiveClasses'},
    {path: '/live-classes/category/:slug', component: LiveClassCategory, name: 'LiveClassCategory'},
    {path: '/live-classes/class/:id', component: LiveClassSingle, name: 'LiveClassSingle'},
    {path: '/live-classes/class/:id/book', component: LiveClassBooking, name: 'LiveClassBooking'},
    {path: '/live-classes/search', component: LiveClassFilterResults, name: 'LiveClassFilterResults'},

    {path: '/on-demand', component: OnDemand, name: 'OnDemand'},
    // { path: '/on-demand/category/:slug', component: OnDemandCategory, name: 'OnDemandCategory' },
    {path: '/on-demand/video/:video_id', component: OnDemandSingle, name: 'OnDemandSingle'},
    // { path: '/on-demand/purchase/:video_id/:status', component: OnDemandPurchaseResponse, name: 'OnDemandPurchaseResponse' },

    {path: '/studio', component: StudioMain, name: 'StudioMain'},
    {path: '/studio/:id', component: StudioSingle, name: 'StudioSingle'},

    {path: '/myaccount', component: MyAccount, name: 'MyAccount', beforeEnter: ifAuthenticated},
    // { path: '/myaccount/:id/bookings', component: Bookings, name: 'Bookings', beforeEnter: ifAuthenticated },
    {path: '/myaccount/name', component: AccountName, name: 'AccountName', beforeEnter: ifAuthenticated},
    {path: '/myaccount/email', component: AccountEmail, name: 'AccountEmail', beforeEnter: ifAuthenticated},
    {
        path: '/myaccount/phonenumber',
        component: AccountPhoneNumber,
        name: 'AccountPhoneNumber',
        beforeEnter: ifAuthenticated
    },
    {path: '/myaccount/avatar', component: AccountAvatar, name: 'AccountAvatar', beforeEnter: ifAuthenticated},
    {path: '/membership', component: AccountMembership, name: 'AccountMembership', beforeEnter: ifAuthenticated},
    {
        path: '/membership/:tier/bacs/:status',
        component: DirectDebitResponse,
        name: 'DirectDebitResponse',
        beforeEnter: ifAuthenticated
    },
    {
        path: '/credit-packs/:creditPackID/purchase/:status',
        component: CreditPackPurchaseResponse,
        name: 'CreditPackPurchaseResponse',
        beforeEnter: ifAuthenticated
    },
    {path: '/bookings', component: AccountBookings, name: 'AccountBookings', beforeEnter: ifAuthenticated},
    {path: '/notifications', component: Notifications, name: 'Notifications', beforeEnter: ifAuthenticated},
    {path: '/myaccount/qr', component: AccountQR, name: 'AccountQR', beforeEnter: ifAuthenticated},
    {path: '/myaccount/qr/scan', component: AccountScanQR, name: 'AccountScanQR', beforeEnter: ifAuthenticated},
    {
        path: '/streaming-activity',
        component: StreamingActivity,
        name: 'StreamingActivity',
        beforeEnter: ifAuthenticated
    },
    // { path: '/myaccount/subscription', component: AccountSubscription, name: 'AccountSubscription', beforeEnter: ifAuthenticated },
    {
        path: '/membership/cancel',
        component: CancelSubscription,
        name: 'CancelSubscription',
        beforeEnter: ifAuthenticated
    },
    // { path: '/subscription/purchase/:tier/:status', component: AccountPurchaseResponse, name: 'AccountPurchaseResponse', beforeEnter: ifAuthenticated },

    {path: '/help', component: HelpMain, name: 'HelpMain', beforeEnter: ifAuthenticated},
    {path: '/help/topics', component: HelpArticles, name: 'HelpArticles', beforeEnter: ifAuthenticated},
    {
        path: '/help/topics/:slug',
        component: HelpSingleCategory,
        name: 'HelpSingleCategory',
        beforeEnter: ifAuthenticated
    },
    {path: '/help/articles/:slug', component: HelpSingle, name: 'HelpSingle', beforeEnter: ifAuthenticated},
    {path: '/help/search', component: HelpSearch, name: 'HelpSearch', beforeEnter: ifAuthenticated},

    // Subscription landing pages

    //11
    //payment
    {
        path: '/2for49_tier_two',
        component: SubscriptionLandingPage2for49_tier_two,
        name: 'SubscriptionLandingPage2for49_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //11
    //payment
    {
        path: '/2for69_tier_one',
        component: SubscriptionLandingPage2for69_tier_one,
        name: 'SubscriptionLandingPage2for69_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //11
    //payment
    {
        path: '/2for49_tier_one_promo',
        component: SubscriptionLandingPage2for49_tier_one_promo,
        name: 'SubscriptionLandingPage2for49_tier_one_promo',
        beforeEnter: ifNotAuthenticated
    },
    //12
    //subscription
    {
        path: '/unlimited_tier_two',
        component: SubscriptionLandingPageUnlimited_tier_two,
        name: 'SubscriptionLandingPageUnlimited_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //21
    //subscription
    {
        path: '/unlimited_tier_one',
        component: SubscriptionLandingPageUnlimited_tier_one,
        name: 'SubscriptionLandingPageUnlimited_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //14
    //subscription
    {
        path: '/twice_weekly_tier_two',
        component: SubscriptionLandingPageTwice_weekly_tier_two,
        name: 'SubscriptionLandingPageTwice_weekly_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //20
    //subscription
    {
        path: '/twice_weekly_tier_one',
        component: SubscriptionLandingPageTwice_weekly_tier_one,
        name: 'SubscriptionLandingPageTwice_weekly_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //16
    //subscription
    {
        path: '/once_weekly_tier_two',
        component: SubscriptionLandingPageOnce_weekly_tier_two,
        name: 'SubscriptionLandingPageOnce_weekly_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //22
    //subscription
    {
        path: '/once_weekly_tier_one',
        component: SubscriptionLandingPageOnce_weekly_tier_one,
        name: 'SubscriptionLandingPageOnce_weekly_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //17
    //subscription
    {
        path: '/annual_vip_tier_two',
        component: SubscriptionLandingPageAnnual_vip_tier_two,
        name: 'SubscriptionLandingPageAnnual_vip_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //19
    //subscription
    {
        path: '/annual_vip_tier_one',
        component: SubscriptionLandingPageAnnual_vip_tier_one,
        name: 'SubscriptionLandingPageAnnual_vip_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //12
    //payment
    {
        path: '/thirty_sessions_tier_two',
        component: SubscriptionLandingPageThirty_sessions_tier_two,
        name: 'SubscriptionLandingPageThirty_sessions_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //12
    //payment
    {
        path: '/thirty_sessions_tier_one',
        component: SubscriptionLandingPageThirty_sessions_tier_one,
        name: 'SubscriptionLandingPageThirty_sessions_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //13
    //payment
    {
        path: '/ten_sessions_tier_two',
        component: SubscriptionLandingPageTen_sessions_tier_two,
        name: 'SubscriptionLandingPageTen_sessions_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //13
    //payment
    {
        path: '/ten_sessions_tier_one',
        component: SubscriptionLandingPageTen_sessions_tier_one,
        name: 'SubscriptionLandingPageTen_sessions_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //2
    //payment
    {
        path: '/single_session_tier_two',
        component: SubscriptionLandingPageSingle_session_tier_two,
        name: 'SubscriptionLandingPageSingle_session_tier_two',
        beforeEnter: ifNotAuthenticated
    },
    //2
    //payment
    {
        path: '/single_session_tier_one',
        component: SubscriptionLandingPageSingle_session_tier_one,
        name: 'SubscriptionLandingPageSingle_session_tier_one',
        beforeEnter: ifNotAuthenticated
    },
    //19
    //payment
    {
        path: '/four_session_starter_pack',
        component: SubscriptionLandingPageFourSessionStarterPack,
        name: 'SubscriptionLandingPageFourSessionStarterPack',
        beforeEnter: ifNotAuthenticated
    },
    //20
    //payment
    {
        path: '/seasonal_tier_one',
        component: SubscriptionLandingPageSeasonalTierOne,
        name: 'SubscriptionLandingPageSeasonalTierOne',
        beforeEnter: ifNotAuthenticated
    },
    //20
    //payment
    {
        path: '/seasonal_tier_two',
        component: SubscriptionLandingPageSeasonalTierTwo,
        name: 'SubscriptionLandingPageSeasonalTierTwo',
        beforeEnter: ifNotAuthenticated
    },

    {path: '/:404', component: PageNotFound, name: 'PageNotFound'}, // 404 catcher
    {path: '/:404/*', component: PageNotFound, name: 'PageNotFound'}, // 404 catcher

];

// filters
Vue.filter('striphtml', function (value) {
    let div = document.createElement("div");
    div.innerHTML = value;
    let text = div.textContent || div.innerText || "";
    return text;
});

const router = new VueRouter({
    mode: 'history',
    routes
});

new Vue({
    el: '#app',
    components: {},
    router
});
