<template>
    <section class="reporting-filters">
        <div class="reporting-filters__header">
            <h3 class="page__title">Reporting</h3>

            <button class="button button--icon" @click="showFilters = !showFilters">
                <i class="material-icons">filter_alt</i>
            </button>
        </div>

        <div :class="{ 'reporting-filters__filters': true, 'reporting-filters__filters--show': showFilters }">

            <admin-input class="reporting-filters__filter reporting-filters__filter--location" label="Select Location">
                <multiselect
                    v-model="filters.locations"
                    :options="gyms"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :searchable="false"
                    :show-labels="false"
                    label="name"
                    track-by="id"
                    placeholder="All"
                    :preselect-first="false"
                    @select="onLocationSelect">
                    <p slot="noOptions">Loading locations...</p>
                    <template v-slot:option="{ option }">
                        <i v-if="option.id === null && filters.locations.length == 0" class="material-icons">check_box</i>
                        <i v-else class="material-icons">{{ filterObject.locations.includes(option.id) ? 'check_box' : 'check_box_outline_blank' }}</i> {{ option.name }}
                    </template>
                </multiselect>
            </admin-input>

            <admin-input class="reporting-filters__filter reporting-filters__filter--units" label="Select Chart Units" type="select" v-model="filters.chartUnit">
                <option
                    v-for="option of chartUnitOptions"
                    :key="option.value"
                    :value="option.value">
                    {{ option.label }}
                </option>
            </admin-input>

            <div class="reporting-filters__filter-section reporting-filters__filter-section--dates">
                <admin-input
                    class="reporting-filters__filter"
                    label="Date From"
                    type="calendar"
                    placeholder="All Time"
                    :max-date="filters.dateTo"
                    v-model="filters.dateFrom"
                    @input="syncDateRangeTo" />

                <admin-input
                    class="reporting-filters__filter"
                    label="Date To"
                    type="calendar"
                    placeholder="All Time"
                    :min-date="filters.dateFrom"
                    v-model="filters.dateTo"
                    @input="syncDateRangeTo" />
            </div>

            <p class="reporting-filters__compare-text">Compare to</p>

            <div class="reporting-filters__filter-section reporting-filters__filter-section--comparison">
                <admin-input
                    class="reporting-filters__filter"
                    label="Date From"
                    type="calendar"
                    placeholder="Select Date"
                    :max-date="filters.compareDateTo"
                    v-model="filters.compareDateFrom"
                    @input="syncDateRangeTo" />

                <admin-input
                    class="reporting-filters__filter"
                    label="Date To"
                    type="calendar"
                    placeholder="Select Date"
                    :min-date="filters.compareDateFrom"
                    v-model="filters.compareDateTo"
                    @input="syncDateRangeFrom" />
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import AdminInput from '../../layout/AdminInput.vue'

export default {
    components: { AdminInput },

    watch: {
        // Watch all filters and emit filterObject when any change
        filterObject (filters) {
            this.$emit('filter', filters);
        }
    },

    data () {
        return {
            showFilters: false,

            gyms: [],

            filters: {
                locations: [],
                chartUnit: '',
                dateFrom: '',
                dateTo: '',
                compareDateFrom: '',
                compareDateTo: ''
            },

            chartUnitOptions: [
                { label: 'All', value: '' },
                { label: 'Daily', value: 'day' },
                { label: 'Weekly', value: 'week' },
                { label: 'Monthly', value: 'month' },
                { label: 'Annually', value: 'year' }
            ]
        }
    },

    computed: {
        filterObject () {
            const dateFormat = date => moment(date).format('YYYY-MM-DD');

            return {
                locations: this.filters.locations.map(x => x.id),
                chartUnit: this.filters.chartUnit,
                dateFrom: this.filters.dateFrom && dateFormat(this.filters.dateFrom),
                dateTo: this.filters.dateTo && dateFormat(this.filters.dateTo),
                compareDateFrom: this.filters.compareDateFrom && dateFormat(this.filters.compareDateFrom),
                compareDateTo: this.filters.compareDateTo && dateFormat(this.filters.compareDateTo)
            }
        }
    },

    methods: {
        /*
         * Load all of this users accessable gyms.
         * @param {none}
         * @return {json}
         */
        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = [
                    { id: null, name: 'All' },
                    ...response.data
                ];
            })
            .catch(error => {
                console.error('Unable to load your gyms at this time.')
            })
        },

        onLocationSelect (option) {
            this.$nextTick(() => {
                if (option.id === null)
                    this.filters.locations = [];
            })
        },

        /*
        * Automatically sync date ranges so that
        * there will always be the same amount of days between them
        */
        syncDateRangeTo () {
            if (this.filters.compareDateFrom && this.filters.dateTo && this.filters.dateFrom) {
                const dateFrom = moment(this.filters.dateFrom)
                const dateTo = moment(this.filters.dateTo)
                const compareDateFrom = moment(this.filters.compareDateFrom)

                const daysBetween = dateTo.diff(dateFrom, 'days')

                this.filters.compareDateTo = compareDateFrom.add(daysBetween, 'days').toDate();
            }
        },

        syncDateRangeFrom () {
            if (this.filters.compareDateTo && this.filters.dateTo && this.filters.dateFrom) {
                const dateFrom = moment(this.filters.dateFrom)
                const dateTo = moment(this.filters.dateTo)
                const compareDateTo = moment(this.filters.compareDateTo)

                const daysBetween = dateFrom.diff(dateTo, 'days')

                this.filters.compareDateFrom = compareDateTo.add(daysBetween, 'days').toDate();
            }
        }
    },

    mounted () {
        this.loadGyms();
    }
}
</script>