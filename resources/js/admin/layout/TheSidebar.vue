<template>
    <div :class="{ sidebar: true, 'sidebar--show': show }">
        <transition name="fade">
            <button v-if="show" class="sidebar__close button button--icon button--transparent" @click="close">
                <i class="fas fa-times" />
            </button>
        </transition>
        <nav class="sidebar__nav" v-on-clickaway="close">
            <!-- <div class="info info--bottom"><i class="fas fa-circle-notch fa-spin"></i> New changes and updates are currently being deployed and tested on your platform.<br><small style="font-size: .7rem; cursor: pointer; font-style: italic; display: block; margin-top: 1rem;" @click="displayDeploymentAlert()">Why am i seeing this message?</small></div> -->

            <ul>
                <li>Dashboards</li>

                <li>
                    <router-link to="/admin/" :class="{ active: this.$route.path === '/admin' || this.$route.path === '/admin/' }">
                        <i class="material-icons">dashboard_customize</i> Main Dashboard
                    </router-link>
                </li>

                <li v-if="canAccess('user-stats-dashboard-read')">
                    <router-link to="/admin/dashboard/users" :class="this.$route.path.includes('/admin/dashboard/users') ? 'active' : ''">
                        <i class="material-icons">query_stats</i> Guest Stats
                    </router-link>
                </li>

                <li v-if="canAccess('door-access-dashboard-read')">
                    <router-link to="/admin/dashboard/door-access" :class="this.$route.path.includes('/admin/dashboard/door-access') ? 'active' : ''">
                        <i class="material-icons">sensor_door</i> Door Access
                    </router-link>
                </li>

                <li v-if="canAccess('revenue-dashboard-read')">
                    <router-link to="/admin/dashboard/revenue/memberships" :class="this.$route.path.includes('/admin/dashboard/revenue/memberships') ? 'active' : ''">
                        <i class="material-icons">paid</i> Revenue
                    </router-link>
                </li>

                <!-- <li>
                    <router-link to="/admin/reports" :class="this.$route.path.includes('/admin/reports') ? 'active' : ''">
                        <i class="material-icons">reports</i> Reports
                    </router-link>
                </li> -->
            </ul>

            <ul v-if="canAccess('admin-read') || canAccess('member-read')">
                <li>Users</li>

                <li v-if="canAccess('admin-read')">
                    <router-link to="/admin/admins" :class="this.$route.path.includes('/admins') ? 'active' : ''">
                        <i class="material-icons">admin_panel_settings</i> Admins
                    </router-link>
                </li>

                <li v-if="canAccess('member-read')">
                    <router-link to="/admin/members" :class="this.$route.path.includes('/members') && !this.$route.path.includes('/memberships') ? 'active' : ''">
                        <i class="material-icons">assignment_ind</i> Guests
                    </router-link>
                </li>
            </ul>

            <ul>
                <li>Gyms / Locations</li>

                <li>
                    <router-link to="/admin/gyms" :class="this.$route.path.includes('/gyms') ? 'active' : ''">
                        <i class="material-icons">shopping_cart</i> My Studios
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/studio/bookings" :class="this.$route.path.includes('/studio/bookings') ? 'active' : ''">
                        <i class="material-icons">event</i> Studio Reservations
                    </router-link>
                </li>
            </ul>

            <ul>
                <li>Commerce</li>

                <li>
                    <router-link to="/admin/products" :class="this.$route.path.includes('/admin/products') ? 'active' : ''">
                        <i class="material-icons">shopping_cart</i> Products
                    </router-link>
                </li>
            </ul>

            <ul v-if="admin.privileges.includes('order-read')">
                <li>Billing</li>

                <li>
                    <router-link to="/admin/orders" :class="this.$route.path.includes('/orders') ? 'active' : ''">
                        <i class="material-icons">shopping_cart</i> Credit Packs
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/memberships" :class="(this.$route.path.includes('/memberships') && !this.$route.path.includes('/admin/dashboard/revenue/memberships')) ? 'active' : ''">
                        <i class="material-icons">shopping_cart</i> Memberships
                    </router-link>
                </li>

                <li>
                    <router-link to="/admin/product" :class="(this.$route.path.includes('/product') && !this.$route.path.includes('/admin/product')) ? 'active' : ''">
                        <i class="material-icons">shopping_cart</i> Products
                    </router-link>
                </li>
            </ul>

            <ul v-if="admin.privileges.includes('class-read')">
                <li>On Demand</li>
                <li v-if="admin.privileges.includes('class-read')">
                    <router-link to="/admin/on-demand/library" :class="this.$route.path.includes('/on-demand/library') ? 'active' : ''">
                        <i class="material-icons">video_library</i> Class Library
                    </router-link>
                </li>

                <li v-if="admin.privileges.includes('class-read')">
                    <router-link to="/admin/dashboard/penetration/on-demand" :class="this.$route.path.includes('/dashboard/penetration/on-demand') ? 'active' : ''">
                        <i class="material-icons">donut_large</i> Penetration
                    </router-link>
                </li>
            </ul>

            <ul v-if="admin.privileges.includes('lead-read')">
                <li>Lead Management</li>

                <li v-if="admin.privileges.includes('lead-read')">
                    <router-link to="/admin/leads" :class="this.$route.path === '/admin/leads' ? 'active' : ''">
                        <!-- <i class="material-icons">supervised_user_circle</i> My Dashboard -->
                        <i class="material-icons">dashboard_customize</i> My Dashboard
                    </router-link>
                </li>

                <li v-if="admin.privileges.includes('lead-read')">
                    <router-link to="/admin/leads/planner" :class="this.$route.path.includes('/leads/planner') ? 'active' : ''">
                        <!-- <i class="material-icons">supervised_user_circle</i> My Dashboard -->
                        <i class="material-icons">event</i> Day Planner
                    </router-link>
                </li>
            </ul>

            <ul>
                <li>Account</li>

                <li>
                    <a href="javascript: void()" @click="$emit('logout')">
                        <i class="material-icons">logout</i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';
export default {
    mixins: [ clickaway ],
    props: {
        show: Boolean,
        admin: Object
    },
    watch: {
        show () {
            this.checkShowState()
        }
    },
    methods: {
        canAccess(privilege) {
            return this.admin.privileges.includes(privilege) || this.admin.superadmin ? true : false;
        },
        close () {
            if (this.show) this.$emit('update:show', false)

            document.body.classList.remove('sidebar-open')
        },
        checkShowState () {
            this.show ?
                document.body.classList.add('sidebar-open') :
                document.body.classList.remove('sidebar-open');
        },
        displayDeploymentAlert() {
            alert('You may notice real-time changes and tweaks of certain areas. This is normal while our team completes the deployment process of new features to your live environment.');
        }
    },
    mounted () {
        this.checkShowState()
    },
    beforeDestroy () {
        document.body.classList.remove('sidebar-open')
    }
}
</script>
