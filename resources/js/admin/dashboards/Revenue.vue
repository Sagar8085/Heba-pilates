<template>
  <div>
    <section class="page-header page-header--no-bottom">
      <div class="wrapper">
        <div class="page-header__col">
          <h1 class="page-header__title">
            <div class="icon icon--dashboard">
              <i class="material-icons">paid</i>
            </div>
            Revenue
          </h1>

          <h2 class="page-header__sub">Dashboard</h2>
        </div>
      </div>
    </section>

    <section class="page-content">
      <ReportingFilters @filter="filter"/>

      <div class="row row--equal">
        <div class="columns six-sm four-xl">
          <h3 class="page__title">Total Revenue</h3>
          <ComparisonLineGraph
              title="Total Revenue by Volume of Sales"
              :graphData="totalRevenueChart"
              :graphValueData="totalRevenueData"
              :key="componentKey"
          />
        </div>

        <div class="columns six-sm four-xl">
          <h3 class="page__title">Average Yield</h3>
          <ComparisonLineGraph
              title="Mean Average Charge per Guest"
              :graphData="averageYieldChart"
              :graphValueData="meanAverageData"
              :key="componentKey + 1"
          />
        </div>

        <div class="columns six-sm four-xl">
          <h3 class="page__title">Purchase Type Breakdown</h3>
          <ComparisonPieChart
              title="Mean Average Charge per Guest"
              :graphData="purchaseTypeBreakdownChart"
              :chart-links="purchaseTypeBreakdownLinks"
              :dateRanges="dateRanges"
              :key="componentKey + 2"
          />
        </div>

        <div class="columns six-sm four-xl">
          <h3 class="page__title">Total Subscription Revenue</h3>
          <ComparisonLineGraph
              title="All Subscription Revenue"
              :graphData="allSubscriptionChart"
              :graphValueData="allSubscriptionData"
              :key="componentKey + 3"
          />
        </div>

        <div class="columns six-sm four-xl">
          <h3 class="page__title">Total Credit Pack Revenue</h3>
          <ComparisonLineGraph
              title="All Credit Pack Revenue"
              :graphData="allCreditPackChart"
              :graphValueData="allCreditPackData"
              :key="componentKey + 4"
          />
        </div>

        <div class="columns six-sm four-xl">
          <h3 class="page__title">Total Promo Revenue</h3>
          <ComparisonLineGraph
              title="All Promo Revenue"
              :graphData="allPromoChart"
              :graphValueData="allPromoData"
              :key="componentKey + 5"
          />
        </div>
      </div>

      <h3 class="page__title page__title--top">Subscription Revenue Breakdown</h3>

      <div class="row row--equal">
        <div class="columns six-sm four-xl">
          <multiselect
              v-model="filters.subscriptionTypes"
              :options="subscriptionTypes"
              :multiple="true"
              :close-on-select="false"
              :clear-on-select="false"
              :searchable="false"
              :show-labels="false"
              label="name"
              track-by="slug"
              placeholder="All"
              :preselect-first="false"
              @input="getRevenueTotals"
          >
            <p slot="noOptions">Loading subscription types...</p>
          </multiselect>
        </div>
      </div>

      <div class="row row--equal">
        <div class="columns six-sm four-xl">
          <ComparisonLineGraph
              title="Subscription Renewals"
              :graphData="subscriptionRenewalsChart"
              :graphValueData="subscriptionRenewalsData"
              :key="componentKey + 6"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonLineGraph
              title="New Subscriptions"
              :graphData="subscriptionNewChart"
              :graphValueData="subscriptionNewData"
              :key="componentKey + 7"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonLineGraph
              title="Subscription Churn"
              :graphData="subscriptionChurnChart"
              :graphValueData="subscriptionChurnData"
              :key="componentKey + 8"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonPieChart
              title="Volume of Subscription Sales"
              :graphData="volumeOfSubscriptionSalesChart"
              :dateRanges="dateRanges"
              :key="componentKey + 10"
              :chart-links="volumeOfSubscriptionSalesLinks"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonBarGraph
              title="Pending Subscription Revenue"
              :graphData="pendingSubscriptionRevenueChart"
              :graphValueData="pendingSubscriptionRevenueData"
              :dateRanges="dateRanges"
              :key="componentKey + 11"
          />
        </div>
      </div>

      <h3 class="page__title page__title--top">Credit Pack Revenue Breakdown</h3>

      <div class="row row--equal">
        <div class="columns six-sm four-xl">
          <multiselect
              v-model="filters.creditPackTypes"
              :options="creditPackTypes"
              :multiple="true"
              :close-on-select="false"
              :clear-on-select="false"
              :searchable="false"
              :show-labels="false"
              label="name"
              track-by="id"
              placeholder="All"
              :preselect-first="false"
              @input="getRevenueTotals"
          >
            <p slot="noOptions">Loading credit pack types...</p>
          </multiselect>
        </div>
      </div>

      <div class="row row--equal">

        <div class="columns six-sm four-xl">
          <ComparisonLineGraph
              title="Credit Pack Renewals"
              :graphData="creditPackRenewalsChart"
              :graphValueData="creditPackRenewalsData"
              :key="componentKey + 12"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonLineGraph
              title="New Credit Pack Purchases"
              :graphData="creditPackNewChart"
              :graphValueData="creditPackNewData"
              :key="componentKey + 13"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonLineGraph
              title="Credit Pack Churn"
              :graphData="creditPackChurnChart"
              :graphValueData="creditPackChurnData"
              :key="componentKey + 14"
          />
        </div>

        <div class="columns six-sm four-xl">
          <ComparisonPieChart
              title="Volume of Credit Pack Sales"
              :graphData="volumeOfCreditPackSalesChart"
              :dateRanges="dateRanges"
              :key="componentKey + 16"
              :chart-links="volumeOfCreditPackSalesLinks"
          />
        </div>
      </div>

      <h3 class="page__title page__title--top">Promotions</h3>

      <div class="row row--equal">
        <div class="columns six-sm four-xl">
          <ComparisonPieChart
              title="Promo Conversion"
              :graphData="promoConversionsChart"
              :dateRanges="dateRanges"
              :key="componentKey + 17"
              :chart-links="promoConversionsLinks"
          />
        </div>
      </div>

    </section>

    <div v-if="infoModal" class="modal-alert modal-alert--active">
      <div class="modal-alert__box modal-alert__box--small modal-alert__body">
        <h2 class="modal-alert__title">{{ this.infoModalTitle }}</h2>

        <p>{{ this.infoModalMessage }}</p>

        <div class="modal-alert__buttons">
          <button class="button button--outlines" @click="infoModal = false">Close</button>
          <a class="button button--outline" href="https://dashboard.stripe.com/dashboard">Open Stripe</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import ReportingFilters from './components/ReportingFilters.vue';
import ComparisonLineGraph from './components/ComparisonLineGraph.vue';
import ComparisonPieChart from './components/ComparisonPieChart.vue';
import ComparisonBarGraph from './components/ComparisonBarGraph.vue';

export default {
  components: {
    ReportingFilters,
    ComparisonLineGraph,
    ComparisonPieChart,
    ComparisonBarGraph
  },

  data() {
    return {
      infoModal: false,
      infoModalTitle: '',
      infoModalMessage: '',

      thirtyDayTrendGraphSum: 0,
      thirtyDayTrendGraph: [],
      thirtyDayTrendGraphLoading: true,

      tierPerformances: [],

      genderData: [],
      loadingGenderDemographic: true,

      allSubscriptionChart: {},
      allSubscriptionData: [],

      allCreditPackChart: {},
      allCreditPackData: [],

      allPromoChart: {},
      allPromoData: [],

      subscriptionRenewalsChart: [],
      subscriptionRenewalsData: [],

      subscriptionNewChart: [],
      subscriptionNewData: [],

      subscriptionChurnChart: [],
      subscriptionChurnData: [],
      premiumMonthlySubscribersChart: {},
      premiumMonthlySubscribersData: [],

      standardMonthlySubscribersChart: {},
      standardMonthlySubscribersData: [],

      volumeOfSubscriptionSalesChart: [],
      volumeOfSubscriptionSalesLinks: [],

      pendingSubscriptionRevenueChart: [],
      pendingSubscriptionRevenueData: [],

      creditPackRenewalsChart: [],
      creditPackRenewalsData: [],

      creditPackNewChart: [],
      creditPackNewData: [],

      onePackCreditGuestsChart: {},
      onePackCreditGuestsData: [],

      introPackThreeCreditGuestsChart: {},
      introPackThreeCreditGuestsData: [],
      creditPackChurnChart: [],
      creditPackChurnData: [],

      purchaseTypeBreakdownChart: [],
      purchaseTypeBreakdownLinks: [],

      volumeOfCreditPackSalesChart: [],
      volumeOfCreditPackSalesLinks: [],

      promoConversionsChart: [],
      promoConversionsLinks: [],

      totalRevenueChart: [],

      averageYieldChart: [],

      totalRevenueData: [],

      componentKey: 0,

      purchaseTypeBreakdownChart: {},

      meanAverageData: [],

      dateRanges: [
        {from: '', to: ''},
        {from: '', to: ''}
      ],

      chartUnit: '',

      locations: [],

      subscriptionTypes: [],
      creditPackTypes: [],

      filters: {
        subscriptionTypes: [],
        creditPackTypes: [],
      },
    }
  },

  mounted() {
    this.getRevenueTotals();
    this.getSubscriptionTypes();
    this.getCreditPackTypes();
    // this.loadThirtyDayTrendGraph();
    // this.loadTierPerformances();
  },

  methods: {
    filter(filters) {
      this.dateRanges = [
        {from: filters.dateFrom, to: filters.dateTo},
        {from: filters.compareDateFrom, to: filters.compareDateTo}
      ];

      this.chartUnit = filters.chartUnit;

      this.locations = filters.locations;

      // Call API
      this.getRevenueTotals();
      this.getSubscriptionTypes();
    },
    getRevenueTotals() {
      axios.get('/api/admin/dashboard/revenue/total', {
        params: {
          report_date_from: this.dateRanges[0].from,
          report_date_to: this.dateRanges[0].to,
          compare_date_from: this.dateRanges[1].from,
          compare_date_to: this.dateRanges[1].to,
          chartUnit: this.chartUnit,
          locations: this.locations,
          subscriptionTypes: this.filters.subscriptionTypes.map(type => type.slug),
          creditPackTypes: this.filters.creditPackTypes.map(type => type.id),
        }
      }).then(response => {
        //Total Revenue
        this.totalRevenueChart = response.data.totalRevenue.chart;
        this.totalRevenueData = response.data.totalRevenue.metrics;

        //Average Yield
        this.averageYieldChart = response.data.averageYield.chart;
        this.meanAverageData = response.data.averageYield.metrics;

        //Purchase Type Breakdown
        this.purchaseTypeBreakdownChart = response.data.purchaseTypeBreakdown.chart;
        this.purchaseTypeBreakdownLinks = response.data.purchaseTypeBreakdown.links;

        //Total Subscriptions Revenue
        this.allSubscriptionChart = response.data.allSubscriptions.chart;
        this.allSubscriptionData = response.data.allSubscriptions.metrics;

        //Total Credit Pack Revenue
        this.allCreditPackChart = response.data.allCreditPacks.chart;
        this.allCreditPackData = response.data.allCreditPacks.metrics;

        //Total Promo Revenue
        this.allPromoChart = response.data.allPromos.chart;
        this.allPromoData = response.data.allPromos.metrics;

        //Subscription Renewals
        this.subscriptionRenewalsChart = response.data.subscriptionRenewals.chart;
        this.subscriptionRenewalsData = response.data.subscriptionRenewals.metrics;

        //New Subscriptions
        this.subscriptionNewChart = response.data.subscriptionNew.chart;
        this.subscriptionNewData = response.data.subscriptionNew.metrics;

        //Subscription Churn
        this.subscriptionChurnChart = response.data.subscriptionChurn.chart;
        this.subscriptionChurnData = response.data.subscriptionChurn.metrics;

        //Volume of Subscriptions Sales
        this.volumeOfSubscriptionSalesChart = response.data.volumeOfSubscriptionSales.chart;
        this.volumeOfSubscriptionSalesLinks = response.data.volumeOfSubscriptionSales.links;

        //Pending Subscription Revenue
        this.pendingSubscriptionRevenueChart = response.data.pendingSubscriptionRevenue.chart;
        this.pendingSubscriptionRevenueData = response.data.pendingSubscriptionRevenue.metrics;

        //Credit Pack Renewals
        this.creditPackRenewalsChart = response.data.creditPackRenewals.chart;
        this.creditPackRenewalsData = response.data.creditPackRenewals.metrics;

        //New Credit Packs
        this.creditPackNewChart = response.data.creditPackNew.chart;
        this.creditPackNewData = response.data.creditPackNew.metrics;

        //Credit Pack Churn
        this.creditPackChurnChart = response.data.creditPackChurn.chart;
        this.creditPackChurnData = response.data.creditPackChurn.metrics;

        //Volume of Credit Pack Sales
        this.volumeOfCreditPackSalesChart = response.data.volumeOfCreditPackSales.chart;
        this.volumeOfCreditPackSalesLinks = response.data.volumeOfCreditPackSales.links;

        //Promo Conversion
        this.promoConversionsChart = response.data.promoConversions.chart;
        this.promoConversionsLinks = response.data.promoConversions.links;

        this.componentKey++;

      }).catch(error => {
        console.log('ERROR');
        console.log(error)
      }).finally(() => this.loading = false);
    },

    getSubscriptionTypes() {
      axios.get('/api/admin/dashboard/subscriptionTypes')
          .then(({data}) => this.subscriptionTypes = data);
    },

    getCreditPackTypes() {
      axios.get('/api/admin/dashboard/creditPackTypes')
          .then(({data}) => this.creditPackTypes = data);
    }

  },
}
</script>
