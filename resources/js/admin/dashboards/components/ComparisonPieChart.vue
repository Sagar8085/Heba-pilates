<template>
    <ComparisonGraphWrapper
        :showInfo="showInfo"
        :largeTitle="largeTitle"
        :title="title"
        :subtitle="subtitle"
        :graphValues="graphValueData"
        @info="openInfo">

        <toggle-switch
            v-if="hasComparisonData"
            slot="toggle"
            :showLabel="!graphValueData"
            :label="toggleSwitchLabel"
            :title="showComparisonData ? 'View Original Data' : 'View Comparison Data'"
            v-model="showComparisonData"
            @input="updateChartData" />

        <pie-graph
            ref="chart"
            :data="localGraphData"
            :options="graphOptions"
            :width="null"
            :height="graphHeight" />

    </ComparisonGraphWrapper>
</template>

<script>
import moment from 'moment'
import PieGraph from './../../graphs/PieGraph.vue';
import ToggleSwitch from './../../layout/ToggleSwitch.vue';
import ComparisonGraphWrapper from './ComparisonGraphWrapper.vue';

export default {
    components: { ComparisonGraphWrapper, PieGraph, ToggleSwitch },

    props: {
        graphData: Object,
        largeTitle: Boolean,
        title: String,
        subtitle: String,
        graphHeight: {
            default: 200
        },
        graphValueData: Array,
        dateRanges: Array,
        chartLinks: {
          default: [],
          type: Array,
        }
    },

    data () {
        return {
            showComparisonData: false,
            graphOptions: {
                layout: {
                    padding: { left: 0, right: 0, top: 0, bottom: 8 }
                },
                legend: {
                    position: 'right'
                },
                onClick: (event, elements) => {
                  if (elements.length && this.chartLinks.length) {
                    let link = this.chartLinks.find(chartLink => chartLink.index === elements[0]._index).link;
                    this.$router.push(link);
                  }
                }
            }
        }
    },

    computed: {
        localGraphData () {
            const data = this.graphData;

            if (!data || !data.datasets) return data;

            data.datasets.forEach(ds => ds.borderWidth = 0);

            const dsIndex = this.hasComparisonData && this.showComparisonData ? 1 : 0;

            return { ...data, datasets: [ data.datasets[dsIndex] ] };
        },

        hasComparisonData () {
            return this.graphData && this.graphData.datasets && this.graphData.datasets.length > 1;
        },

        toggleSwitchLabel () {
            if (!this.hasComparisonData || !this.dateRanges || this.dateRanges.length !== 2) return '';

            const formatDate = date => moment(date).format('YYYY/MM/DD');

            const formatLabel = ({from, to}) => {
                if (from && to)
                    return `${formatDate(from)} — ${formatDate(to)}`;
                else if (from)
                    return formatDate(from);
                else if (to)
                    return formatDate(to);
                else
                    return this.showComparisonData ? 'Comparison Data' : 'Original Data';
            }

            return this.showComparisonData
                ? formatLabel(this.dateRanges[1])
                : formatLabel(this.dateRanges[0]);
        },

        showInfo () {
            return !!this.$listeners.info;
        }
    },

    methods: {
        openInfo () {
            this.$emit('info');
        },

        updateChartData () {
            this.$refs.chart.updateData();
        }
    }
}
</script>