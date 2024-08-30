import Vue from 'vue'
import VueRouter from 'vue-router';
import Multiselect from 'vue-multiselect';
import axios from 'axios';
import Chart from 'chart.js'

Chart.defaults.global.legend.labels.usePointStyle = true;

const token = localStorage.getItem('fc-admin-usertoken')

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

    next('/admin/login')
}

Vue.component('App', require('./App.vue').default);
Vue.component('multiselect', Multiselect);

import Login from './auth/Login.vue';
import Invitation from './auth/Invitation.vue';

import PermissionDenied from './PermissionDenied.vue';

import MyDashboard from './dashboards/MyDashboard.vue';
import RevenueDashboard from './dashboards/Revenue.vue';
import CreditPackRevenueDashboard from './dashboards/CreditRevenue.vue';
import SessionDashboard from './dashboards/Sessions.vue';
import UserDashboard from './dashboards/Users.vue';

import DoorAccessLive from './door-access/Live.vue';
import DoorAccessLog from './door-access/AccessLog.vue';

import SearchResults from './search/Results.vue';

import PenetrationDashboard from './dashboards/penetration/Main.vue';

import PenetrationOnDemand from './dashboards/penetration/OnDemand.vue';
import PenetrationClassCategoryView from './dashboards/penetration/PenetrationClassCategoryView.vue';
import PenetrationClassCategoryMemberView from './dashboards/penetration/PenetrationClassCategoryMembers.vue';
import PenetrationClassSingle from './dashboards/penetration/PenetrationClassSingle.vue';
import PenetrationClassSingleMembers from './dashboards/penetration/PenetrationClassSingleMembers.vue';

import PenetrationLive from './dashboards/penetration/Live.vue';
import PenetrationLiveCategoryView from './dashboards/penetration/LiveCategoryView.vue';
import PenetrationLiveCategoryMembers from './dashboards/penetration/LiveCategoryMembers.vue';

import PenetrationVirtual from './dashboards/penetration/PenetrationVirtual.vue';

import LiveClasses from './live-classes/Main.vue';
import LiveClassCategories from './live-classes/Categories.vue';
import LiveClassSingleCategory from './live-classes/SingleCategory.vue';
import LiveClassSingle from './live-classes/SingleClass.vue';

import OnDemandLibrary from './on-demand/Library.vue';
import OnDemandSingle from './on-demand/Single.vue';
import OnDemandCategories from './on-demand/Categories.vue';
import OnDemandSingleCategory from './on-demand/SingleCategory.vue';
import OnDemandOrder from './on-demand/Order.vue';
import OnDemandOrderCategories from './on-demand/OrderCategories.vue';

import StudioBookings from './studio/Bookings.vue';
import StudioSelect from './studio/StudioSelect.vue';
import StudioUpcoming from './studio/StudioUpcoming.vue';
import StudioMachines from './studio/StudioMachines.vue';
import StudioMachineSingle from './studio/StudioMachineSingle.vue';
import ViewReservation from './studio/ViewReservation.vue';
import CreateReservation from './studio/CreateReservation.vue';

import GymList from './gyms/Main.vue';
import GymSingle from './gyms/Single.vue';

import MemberList from './members/Main.vue';
import MemberSingle from './members/Single.vue';
import MemberStudioReservations from './members/StudioReservations.vue';
import MemberMembershipHistory from './members/MembershipHistory.vue';
import MemberMembershipPurchaseCallback from './members/MembershipPurchaseCallback.vue';
import MemberCreditPackHistory from './members/CreditPackPurchases.vue';
import MemberCreditPackPurchaseCallback from './members/CreditPackPurchaseCallback.vue';

import TrainerList from './trainers/Main.vue';
import TrainerSingle from './trainers/Single.vue';

import AdminList from './admins/Main.vue';
import AdminSingle from './admins/Single.vue';

import OrdersMain from './orders/Main.vue';
import OrderSingle from './orders/Single.vue';

import MembershipsMain from './memberships/Main.vue';
import MembershipSingle from './memberships/Single.vue';

import ReportsMain from './reports/Main.vue';

import ProductIndex from './products/Index.vue';

Vue.component('workout-builder-item', require('../components/WorkoutBuilderItem.vue').default);
import SessionsMain from './sessions/Main.vue';
import SessionSingle from './sessions/Single.vue';

import LeadDashboard from './leads/MyDashboard.vue';
import LeadPlanner from './leads/DayPlanner.vue';
import LeadProfile from './leads/LeadProfile.vue';
import LeadManage from './leads/ManageLeads.vue';
import AgentManage from './leads/ManageAgents.vue';
import AgentProfile from './leads/AgentProfile.vue';

export const routes = [
    { path: '/admin/login', component: Login, name: 'Login', beforeEnter: ifNotAuthenticated },
    { path: '/admin/setup/:token', component: Invitation, name: 'Invitation', beforeEnter: ifNotAuthenticated },

    { path: '/admin/permission-denied', component: PermissionDenied, name: 'PermissionDenied', beforeEnter: ifAuthenticated },

    { path: '/admin/', component: MyDashboard, name: 'MyDashboard', beforeEnter: ifAuthenticated },
    { path: '/admin/search', component: SearchResults, name: 'SearchResults', beforeEnter: ifAuthenticated },

    { path: '/admin/dashboard/door-access', component: DoorAccessLive, name: 'DoorAccessLive', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/door-access/log', component: DoorAccessLog, name: 'DoorAccessLog', beforeEnter: ifAuthenticated },

    { path: '/admin/dashboard/penetration', component: PenetrationDashboard, name: 'PenetrationDashboard', beforeEnter: ifAuthenticated },

    { path: '/admin/dashboard/penetration/on-demand', component: PenetrationOnDemand, name: 'PenetrationOnDemand', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/penetration/class/:id', component: PenetrationClassCategoryView, name: 'PenetrationClassCategoryView', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/penetration/class/:id/members', component: PenetrationClassCategoryMemberView, name: 'PenetrationClassCategoryMemberView', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/penetration/class/:category_id/:class_id', component: PenetrationClassSingle, name: 'PenetrationClassSingle', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/penetration/class/:category_id/:class_id/members', component: PenetrationClassSingleMembers, name: 'PenetrationClassSingleMembers', beforeEnter: ifAuthenticated },

    { path: '/admin/dashboard/penetration/live', component: PenetrationLive, name: 'PenetrationLive', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/penetration/live/:category_id', component: PenetrationLiveCategoryView, name: 'PenetrationLiveCategoryView', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/penetration/live/:category_id/members', component: PenetrationLiveCategoryMembers, name: 'PenetrationLiveCategoryMembers', beforeEnter: ifAuthenticated },

    { path: '/admin/dashboard/penetration/virtual/:duration', component: PenetrationVirtual, name: 'PenetrationVirtual', beforeEnter: ifAuthenticated },

    { path: '/admin/dashboard/revenue/memberships', component: RevenueDashboard, name: 'RevenueDashboard', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/revenue/credit-packs', component: CreditPackRevenueDashboard, name: 'CreditPackRevenueDashboard', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/sessions', component: SessionDashboard, name: 'SessionDashboard', beforeEnter: ifAuthenticated },
    { path: '/admin/dashboard/users', component: UserDashboard, name: 'UserDashboard', beforeEnter: ifAuthenticated },

    { path: '/admin/gyms', component: GymList, name: 'GymList', beforeEnter: ifAuthenticated },
    { path: '/admin/gyms/:id', component: GymSingle, name: 'GymSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/live-classes', component: LiveClasses, name: 'LiveClasses', beforeEnter: ifAuthenticated },
    { path: '/admin/live-classes/categories', component: LiveClassCategories, name: 'LiveClassCategories', beforeEnter: ifAuthenticated },
    { path: '/admin/live-classes/categories/:id', component: LiveClassSingleCategory, name: 'LiveClassSingleCategory', beforeEnter: ifAuthenticated },
    { path: '/admin/live-classes/:id', component: LiveClassSingle, name: 'LiveClassSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/on-demand/library', component: OnDemandLibrary, name: 'OnDemandLibrary', beforeEnter: ifAuthenticated },
    { path: '/admin/on-demand/library/:id', component: OnDemandSingle, name: 'OnDemandSingle', beforeEnter: ifAuthenticated },
    { path: '/admin/on-demand/categories', component: OnDemandCategories, name: 'OnDemandCategories', beforeEnter: ifAuthenticated },
    { path: '/admin/on-demand/categories/:id', component: OnDemandSingleCategory, name: 'OnDemandSingleCategory', beforeEnter: ifAuthenticated },
    { path: '/admin/on-demand/categories/:id/order', component: OnDemandOrder, name: 'OnDemandOrder', beforeEnter: ifAuthenticated },
    { path: '/admin/on-demand/category/list/order', component: OnDemandOrderCategories, name: 'OnDemandOrderCategories', beforeEnter: ifAuthenticated },

    { path: '/admin/studio/bookings', component: StudioBookings, name: 'StudioBookings', beforeEnter: ifAuthenticated },
    { path: '/admin/studio', component: StudioSelect, name: 'StudioSelect', beforeEnter: ifAuthenticated },
    { path: '/admin/studio/:studio_id', component: StudioUpcoming, name: 'StudioUpcoming', beforeEnter: ifAuthenticated },
    { path: '/admin/studio/:studio_id/machines', component: StudioMachines, name: 'StudioMachines', beforeEnter: ifAuthenticated },
    { path: '/admin/studio/:studio_id/machines/:machine_id', component: StudioMachineSingle, name: 'StudioMachineSingle', beforeEnter: ifAuthenticated },
    { path: '/admin/studio/:studio_id/machines/:machine_id/booking', component: CreateReservation, name: 'CreateReservation', beforeEnter: ifAuthenticated },
    { path: '/admin/studio/:studio_id/machines/:machine_id/booking/:booking_id', component: ViewReservation, name: 'ViewReservation', beforeEnter: ifAuthenticated },

    // { path: '/admin/exercise/library', component: ExerciseLibrary, name: 'ExerciseLibrary', beforeEnter: ifAuthenticated },
    // { path: '/admin/exercise/library/:id', component: ExerciseSingle, name: 'ExerciseSingle', beforeEnter: ifAuthenticated },
    // { path: '/admin/exercise/categories', component: ExerciseCategories, name: 'ExerciseCategories', beforeEnter: ifAuthenticated },
    // { path: '/admin/exercise/categories/:id', component: ExerciseSingleCategory, name: 'ExerciseSingleCategory', beforeEnter: ifAuthenticated },

    { path: '/admin/sessions', component: SessionsMain, name: 'SessionsMain', beforeEnter: ifAuthenticated },
    { path: '/admin/sessions/:id', component: SessionSingle, name: 'SessionSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/orders', component: OrdersMain, name: 'OrdersMain', beforeEnter: ifAuthenticated },
    { path: '/admin/orders/:id', component: OrderSingle, name: 'OrderSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/memberships', component: MembershipsMain, name: 'MembershipsMain', beforeEnter: ifAuthenticated },
    { path: '/admin/memberships/:id', component: MembershipSingle, name: 'MembershipSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/members', component: MemberList, name: 'MemberList', beforeEnter: ifAuthenticated },
    { path: '/admin/members/:id', component: MemberSingle, name: 'MemberSingle', beforeEnter: ifAuthenticated },
    { path: '/admin/members/:id/memberships', component: MemberMembershipHistory, name: 'MemberMembershipHistory', beforeEnter: ifAuthenticated },
    { path: '/admin/members/:id/membership/callback/success', component: MemberMembershipPurchaseCallback, name: 'MemberMembershipPurchaseCallback', beforeEnter: ifAuthenticated },
    { path: '/admin/members/:id/credit-packs', component: MemberCreditPackHistory, name: 'MemberCreditPackHistory', beforeEnter: ifAuthenticated },
    { path: '/admin/members/:id/credit-packs/callback/success', component: MemberCreditPackPurchaseCallback, name: 'MemberCreditPackPurchaseCallback', beforeEnter: ifAuthenticated },
    { path: '/admin/members/:id/studio-reservations', component: MemberStudioReservations, name: 'MemberStudioReservations', beforeEnter: ifAuthenticated },

    { path: '/admin/trainers', component: TrainerList, name: 'TrainerList', beforeEnter: ifAuthenticated },
    { path: '/admin/trainers/:id', component: TrainerSingle, name: 'TrainerSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/admins', component: AdminList, name: 'AdminList', beforeEnter: ifAuthenticated },
    { path: '/admin/admins/:id', component: AdminSingle, name: 'AdminSingle', beforeEnter: ifAuthenticated },

    { path: '/admin/leads', component: LeadDashboard, name: 'LeadDashboard', beforeEnter: ifAuthenticated },
    { path: '/admin/leads/planner', component: LeadPlanner, name: 'LeadPlanner', beforeEnter: ifAuthenticated },
    { path: '/admin/leads/manage', component: LeadManage, name: 'ManageLeads', beforeEnter: ifAuthenticated },
    { path: '/admin/leads/manage/agents', component: AgentManage, name: 'ManageAgents', beforeEnter: ifAuthenticated },
    { path: '/admin/leads/manage/agents/:id', component: AgentProfile, name: 'AgentProfile', beforeEnter: ifAuthenticated },
    { path: '/admin/leads/:id', component: LeadProfile, name: 'LeadProfile', beforeEnter: ifAuthenticated },

    { path: '/admin/reports', component: ReportsMain, name: 'ReportsMain', beforeEnter: ifAuthenticated },

    { path: '/admin/products', component: ProductIndex, name: 'ProductIndex', beforeEnter: ifAuthenticated }
];

// filters
Vue.filter('striphtml', function (value) {
    let div = document.createElement("div");
    div.innerHTML = value;
    let text = div.textContent || div.innerText || "";
    return text;
});

Vue.component('pagination', require('./../components/Pagination.vue').default);

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes
});

new Vue({
    el: '#app',
    components: {},
    router
});
