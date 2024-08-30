<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        Memberships
                    </h1>

                    <h2 class="page-header__sub">All One Off and Recurring Membership Subscriptions</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="info info--bottom">
                <i class="fas fa-info-circle"></i>You can purchase a Membership on behalf of a member from within their profile.<br>
                <i class="fas fa-exclamation-triangle"></i>Direct Debit payments can take 3-9 business days to process and for funds to become available in Stripe.
            </div>

            <div class="row row--equal">
                <div class="four columns">
                    <div class="card">
                        <p class="card__title"><span>Total</span> Active Subscriptions</p>
                        <span class="card__value">{{ this.membershipStats.active }}</span>
                    </div>
                </div>

                <div class="four columns">
                    <div class="card">
                        <p class="card__title"><span>This Month</span>Expiring Subscriptions</p>
                        <span class="card__value card__value--green">{{ this.membershipStats.expiring }}</span>
                    </div>
                </div>

                <div class="four columns">
                    <div class="card">
                        <p class="card__title"><span>This Month</span>Renewing Subscriptions</p>
                        <span class="card__value">{{ this.membershipStats.renewing }}</span>
                    </div>
                </div>
            </div>

            <div class="filters">
                <div class="filters__placeholder" @click="toggleFilterDropdown()"><i class="fas fa-filter"></i></div>
                <div class="filters__placeholder" @click="loadOrders(false, pagination.current_page)"><i :class="loading ? 'fas fa-sync-alt fa-spin' : 'fas fa-sync-alt'"></i></div>
                <div class="filters__placeholder" @click="loadOrders(true, pagination.current_page)"><i :class="loading ? 'fas fa-cloud-download-alt' : 'fas fa-cloud-download-alt'"></i></div>

                <div :class="filterDropdown ? 'filters__dropdown filters__dropdown--active' : 'filters__dropdown'">
                    <div class="row">
                        <div class="twelve columns">

                            <h3>Membership Type</h3>
                            <div class="row">
                                <div class="twelve columns">
                                    <multiselect v-if="membershipTiers.length > 0" v-model="selectedMembershipTiers" :options="membershipTiers" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Filters all tiers unless at least 1 is selected" label="name" track-by="id" :preselect-first="false"></multiselect>
                                </div>
                            </div>

                            <h3>Creation Date</h3>
                            <div class="row">
                                <div class="four columns">
                                    <div class="filters__dropdown-item">
                                        <label for="paymentDateAll">All time</label>
                                        <input id="paymentDateAll" type="checkbox" value="" v-model="paymentDateAll">
                                    </div>
                                </div>

                                <div class="four columns" :class="paymentDateAll ? 'form-element--blur' : ''">
                                    <datepicker :value="start_date" @selected="this.select_start" :format="'yyyy-MM-dd'"></datepicker>
                                </div>

                                <div class="four columns" :class="paymentDateAll ? 'form-element--blur' : ''">
                                    <datepicker :value="end_date" @selected="this.select_end" :format="'yyyy-MM-dd'"></datepicker>
                                </div>
                            </div>

                            <h3>Expiry Date</h3>
                            <div class="row">
                                <div class="four columns">
                                    <div class="filters__dropdown-item">
                                        <label for="expiryDateAll">All time</label>
                                        <input id="expiryDateAll" type="checkbox" value="" v-model="expiryDateAll">
                                    </div>
                                </div>

                                <div class="four columns" :class="expiryDateAll ? 'form-element--blur' : ''">
                                    <datepicker :value="expiryStartDate" @selected="this.selectExpiryStart" :format="'yyyy-MM-dd'"></datepicker>
                                </div>

                                <div class="four columns" :class="expiryDateAll ? 'form-element--blur' : ''">
                                    <datepicker :value="expiryEndDate" @selected="this.selectExpiryEnd" :format="'yyyy-MM-dd'"></datepicker>
                                </div>
                            </div>

                            <button style="margin-top: 1.5rem;" class="button button--full" @click="filterDropdown = false; loadOrders(false, 1)">Apply Filters</button>

                        </div>
                    </div>
                </div>
            </div>

            <div class="table-list table-list--top">
                <div class="table-list__header">
                    <h3><span v-if="pagination.from">{{ pagination.from + ' - ' + pagination.to }} of </span>{{ pagination.total }} Memberships</h3>

                    <div class="table-list__header-pagination">
                        <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === 1}" @click="change_page('previous')">navigate_before</span>
                        <span :class="{'material-icons-outlined': true, 'disabled': pagination.current_page === pagination.last_page}" @click="change_page('next')">navigate_next</span>
                    </div>
                </div>

                <div class="table-list__scroll">
                    <table class="table-list__table">
                        <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Membership</th>
                                <th>Member</th>
                                <th>Status</th>
                                <th>Renew / Expiry Date</th>
                                <th>Payment</th>
                                <th>Created</th>
                                <!-- <th></th> -->
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(membership, index) in memberships" :key="index" :class="{'strike-through': membership.deleted_at === null ? false : true}">
                                <td>#{{ membership.id }}</td>
                                <td>{{ membership.name }}</td>
                                <td><router-link :to="{ name: 'MemberSingle', params: { id: membership.member.id }}">{{ membership.member.name }}</router-link></td>
                                <td><span :class="[{tag: true, 'tag--red': membership.status_human === 'Active - Does Not Renew', 'tag--green': membership.status_human === 'Active - Renews', 'tag--grey': (membership.status_human === 'Expired' || membership.status_human === 'Deleted')}]">{{ membership.status_human }}</span></td>
                                <td>{{ membership.expires_human }}</td>
                                <td>{{ membership.order ? membership.order.value_human + ' - ' + membership.order.method_human : '' }}</td>
                                <td>{{ membership.created_human }}</td>
                                <!-- <td v-if="membership.deleted_at === null"></td>
                                <td v-else>Deleted <span v-if="membership.deleter && membership.deleter !== null">by {{ membership.deleter.name }}</span><br>{{ membership.deleted_at_human }}</td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import Datepicker from 'vuejs-datepicker';

export default {
    components: {
        FilterableDataTable,
        Datepicker
    },
    data() {
        return {
            loading: true,
            ordersListHeadings: {
                order: 'Membership',
                member: 'Member',
                price: 'Paid',
                payment_method: 'Payment Method',
                date: 'Date'
            },
            orders: [],

            memberships: [],
            pagination: {},

            orderFilters: [
                { option: 'All', value: 'all' }
            ],

            membershipTiers: [],
            selectedMembershipTiers: [],
            membershipTypes: ['premium', 'standard', 'online-only', 'one-month-unlimited', 'vip-unlimited'],

            paymentMethods: ['stripe', 'apple', 'google'],
            paymentDateAll: true,
            filterDropdown: false,

            expiryDateAll: true,
            expiryStartDate: '',
            expiryEndDate: '',

            start_date: '',
            end_date: '',

            membershipStats: {
                active: 0,
                expiring: 0,
                renewing: 0,
                revenue: 0
            }
        }
    },

    mounted() {
        var date = new Date();
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        this.start_date = date.getFullYear() + '-' + month + '-01';
        this.end_date = date.getFullYear() + '-' + month + '-' + ("0" + date.getDate()).slice(-2);
        this.expiryStartDate = date.getFullYear() + '-' + month + '-01';
        this.expiryEndDate = date.getFullYear() + '-' + month + '-' + ("0" + date.getDate()).slice(-2);

        this.loadOrders();
        this.loadMembershipStats();
        this.loadMembershipTiers();
    },

    methods: {
        change_page(type) {
            if (type === 'previous') {
                var page = (this.pagination.current_page - 1);
            } else {
                var page = (this.pagination.current_page + 1);
            }

            this.loadOrders(false, page);
        },

        loadOrders(download = false, page) {
            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.post('/api/admin/billing/memberships?download=' + download + '&page=' + page, {
                paymentDateAll: this.paymentDateAll,
                startDate: this.start_date,
                endDate: this.end_date,
                expiryDateAll: this.expiryDateAll,
                expiryStartDate: this.expiryStartDate,
                expiryEndDate: this.expiryEndDate,
                paymentMethods: this.paymentMethods,
                selectedMembershipTiers: this.selectedMembershipTiers
            }).then(response => {
                if (download) {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'Membership Download.csv');

                    document.body.appendChild(fileLink);

                    fileLink.click();
                    this.downloading = false;
                } else {
                    this.memberships = response.data.data;
                    this.pagination = response.data;
                }
            })
            .catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
            .finally(() => this.loading = false);
        },

        toggleFilterDropdown() {
            if (this.filterDropdown) {
                this.filterDropdown = false;
            } else {
                this.filterDropdown = true;
            }
        },

        select_start: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.start_date = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        select_end: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.end_date = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectExpiryStart: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.expiryStartDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        selectExpiryEnd: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.expiryEndDate = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        loadMembershipStats() {
            axios.get('/api/admin/billing/memberships/stats').then(response => {
                this.membershipStats = response.data;
            });
        },

        /*
         * Load all membership tiers for filtering.
         * @param {none}
         */
        loadMembershipTiers() {
            axios.get('/api/admin/membership-tiers').then(response => {
                this.membershipTiers = response.data;
            });
        }
    }
}
</script>
