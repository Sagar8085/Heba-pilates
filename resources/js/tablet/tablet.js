import Vue from 'vue'
import VueRouter from 'vue-router';
import axios from 'axios';
import VueSelectImage from 'vue-select-image';
import JQuery from 'jquery';
import VueVideoPlayer from 'vue-video-player'
import 'video.js/dist/video-js.css'
import 'vue-video-player/src/custom-theme.css'
// import 'videojs-flash'
import 'videojs-contrib-hls/dist/videojs-contrib-hls'
// import 'videojs-contrib-hls.js/src/videojs.hlsjs'

window.$ = JQuery;
window.axios = axios;

Vue.use(VueRouter);
Vue.use(VueSelectImage);
Vue.use(VueVideoPlayer);

const token = localStorage.getItem('tablet-token') || sessionStorage.getItem('tablet-token');

if (token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
}

const ifNotAuthenticated = (to, from, next) => {
  if (!token) {
    next()
    return
  }
  next('/tablet/login')
}

const ifAuthenticated = (to, from, next) => {
    if (token) {
        next()
        return
    }

    next('/tablet/login')
}

Vue.component('App', require('./App.vue').default);

import Dashboard from './Dashboard.vue';
import Login from './Login.vue';
import OnDemand from './on-demand/Main.vue';
import OnDemandCategory from './on-demand/Category.vue';
import OnDemandSingle from './on-demand/Single.vue';

import LiveClasses from './live-classes/Main.vue';
import LiveClassSingle from './live-classes/Single.vue';
import LiveClassCategory from './live-classes/Category.vue';

Vue.component('workout-builder-item', require('../components/WorkoutBuilderItem.vue').default);
Vue.component('active-excercise', require('../components/ActiveExcercise.vue').default);
Vue.component('view-booking-card', require('../components/ViewBookingCard.vue').default);


export const routes = [

    // beforeEnter: ifAuthenticated check to be added back in once backend APIs are built. In the meantime, it's been left commented out next to it's corresponding path.

    { path: '/tablet', component: () => token ? import('./Dashboard.vue') : import('./Login.vue'), name: 'Home' },

    { path: '/tablet/live-classes', component: LiveClasses, name: 'LiveClasses' },
    { path: '/tablet/live-classes/category/:slug', component: LiveClassCategory, name: 'LiveClassCategory' },
    { path: '/tablet/live-classes/:id', component: LiveClassSingle, name: 'LiveClassSingle' },

    { path: '/tablet/on-demand', component: Dashboard, name: 'Dashboard' },
    { path: '/tablet/on-demand/video/:video_id', component: OnDemandSingle, name: 'OnDemandSingle' },

];

const router = new VueRouter({
    mode: 'history',
    routes
});

new Vue({
    el: '#app',
    components: {},
    router
});
