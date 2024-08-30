<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        Credit Packs
                    </h1>

                    <h2 class="page-header__sub">All Credit Pack Purchases</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="info info--bottom"><i class="fas fa-info-circle"></i>You can purchase a Credit Pack on behalf of a member from within their profile.</div>

            <div class="filters">
                <div class="filters__placeholder" @click="toggleFilterDropdown()"><i class="fas fa-filter"></i></div>
                <div class="filters__placeholder" @click="loadOrders(false, pagination.current_page)"><i :class="loading ? 'fas fa-sync-alt fa-spin' : 'fas fa-sync-alt'"></i></div>
                <div class="filters__placeholder" @click="loadOrders(true, pagination.current_page)"><i :class="loading ? 'fas fa-cloud-download-alt' : 'fas fa-cloud-download-alt'"></i></div>

                <div :class="filterDropdown ? 'filters__dropdown filters__dropdown--active' : 'filters__dropdown'">
                    <div class="row">
                        <div class="twelve columns">

                            <h3>Purchase</h3>
                            <div class="row">
                                <div class="twelve columns">
                                    <multiselect v-if="creditPacks.length > 0" v-model="selectedCreditPacks" :options="creditPacks" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Filters all packs unless at least 1 is selected" label="name" track-by="id" :preselect-first="false"></multiselect>
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

                            <button style="margin-top: 1.5rem;" class="button button--full" @click="filterDropdown = false; loadOrders(false, 1)">Apply Filters</button>

                        </div>
                    </div>
                </div>
            </div>

            <div class="table-list table-list--top">
                <div class="table-list__header">
                    <h3><span v-if="pagination.from">{{ pagination.from + ' - ' + pagination.to }} of </span>{{ pagination.total }} Credit Pack Purchases</h3>

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
                                <th>Credit Pack</th>
                                <th>Member</th>
                                <th>Payment</th>
                                <th>Created</th>
                                <!-- <th></th> -->
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(order, index) in orders" :key="index" :class="{'strike-through': order.deleted_at === null ? false : true}">
                                <td><router-link :to="'/admin/orders/' + order.id">#{{ order.id }}</router-link></td>
                                <td>{{ order.pack.name }}</td>
                                <td><router-link :to="{ name: 'MemberSingle', params: { id: order.member.id }}">{{ order.member.name }}</router-link></td>
                                <td>{{ order.order ? order.order.value_human + ' - ' + order.order.method_human : '' }}</td>
                                <td>{{ order.created_human }}</td>
                                <!-- <td v-if="order.deleted_at === null"></td>
                                <td v-else>Deleted <span v-if="order.deleter && order.deleter !== null">by {{ order.deleter.name }}</span><br>{{ order.deleted_at_human }}</td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- <div class="table-list table-list--top">
                <div class="table-list__header">
                    <h3>{{ orders.length }} Orders</h3>
                </div>

                <div class="table-list__scroll">
                    <table class="table-list__table">
                        <thead>
                            <tr>
                                <th>Purchase</th>
                                <th>Guest</th>
                                <th>Paid</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item, index) in orders" :key="index">
                                <td><router-link :to="{ name: 'OrderSingle', params: { id: item.id }}">{{ item.orderable.name }}</router-link></td>
                                <td><router-link :to="{ name: 'MemberSingle', params: { id: item.member.id }}">{{ item.member.name }}</router-link></td>
                                <td>{{ item.value_human }}</td>
                                <td>{{ item.created_human }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import Datepicker from 'vuejs-datepicker';

export default {
    components: { FilterableDataTable, Datepicker },

    data() {
        return {
            loading: true,
            orders: [],
            creditPacks: [],
            selectedCreditPacks: [],
            creditPackIds: [],
            paymentMethods: ['stripe', 'apple', 'google'],
            filterDropdown: false,

            creditPackStats: {},
            pagination: {},

            paymentDateAll: true,
            start_date: '',
            end_date: '',
        }
    },

    mounted() {
        var date = new Date();
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        this.start_date = date.getFullYear() + '-' + month + '-01';
        this.end_date = date.getFullYear() + '-' + month + '-' + ("0" + date.getDate()).slice(-2);

        this.loadCreditPacks();
        this.loadOrders(false, 1);
        this.loadCreditPackStats();
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

        loadCreditPacks() {
            axios.get('/api/admin/billing/credit-packs').then(response => {
                this.creditPacks = response.data;
            });
        },

        loadOrders(download = false, page = 1) {
            axios.post('/api/admin/billing/orders?page=' + page + '&download=' + download, {
                selectedCreditPacks: this.selectedCreditPacks,
                paymentDateAll: this.paymentDateAll,
                startDate: this.start_date,
                endDate: this.end_date
            }).then(response => {
                if (download) {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'Credit Pack Orders.csv');

                    document.body.appendChild(fileLink);

                    fileLink.click();
                    this.loading = false;
                } else {
                    this.orders = response.data.data;
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

        loadCreditPackStats() {
            axios.get('/api/admin/billing/credit-packs/stats').then(response => {
                this.creditPackStats = response.data;
            });
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
    }
}
</script>
