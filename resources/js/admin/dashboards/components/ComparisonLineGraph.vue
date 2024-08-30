<template>
  <ComparisonGraphWrapper
      :showInfo="showInfo"
      :largeTitle="largeTitle"
      :title="title"
      :subtitle="subtitle"
      :graphValues="graphValueData"
      @info="openInfo">

    <line-graph
        :data="localGraphData"
        :options="graphOptions"
        :width="null"
        :height="graphHeight"/>

  </ComparisonGraphWrapper>
</template>

<script>
import LineGraph from './../../graphs/LineGraph.vue';
import ComparisonGraphWrapper from './ComparisonGraphWrapper.vue';

export default {
  components: {ComparisonGraphWrapper, LineGraph},

  props: {
    graphData: Object,
    largeTitle: Boolean,
    title: String,
    subtitle: String,
    graphHeight: {
      default: 200
    },
    graphValueData: Array
  },

  data() {
    return {
      graphOptions: {
        layout: {
          padding: {left: 0, right: 0, top: 0, bottom: 8}
        },
        elements: {
          line: {
            tension: 0,
            backgroundColor: 'rgba(0,0,0,0)'
          },
          point: {
            radius: 0,
            hitRadius: 10
          }
        },
        scales: {
          xAxes: [{
            gridLines: {
              color: "rgba(0, 0, 0, 0)",
            }
          }],
          yAxes: [{
            ticks: {
              display: false
            }
          }]
        },
      }
    }
  },

  computed: {
    localGraphData() {
      const data = this.graphData;

      if (!data || !data.datasets || data.datasets[0] === undefined) return data;

      data.datasets[0].borderColor = '#17B5C8';

      return data;
    },

    showInfo() {
      return !!this.$listeners.info;
    }
  },

  methods: {
    openInfo() {
      this.$emit('info');
    }
  }
}
</script>
