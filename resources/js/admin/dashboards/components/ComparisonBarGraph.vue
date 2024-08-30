<template>
    <ComparisonGraphWrapper
        :showInfo="showInfo"
        :largeTitle="largeTitle"
        :title="title"
        :subtitle="subtitle"
        :graphValues="graphValueData"
        @info="openInfo">

        <bar-graph
            :data="graphData"
            :options="graphOptions"
            :width="null"
            :height="graphHeight" />

    </ComparisonGraphWrapper>
</template>

<script>
import BarGraph from './../../graphs/BarGraph.vue';
import ComparisonGraphWrapper from './ComparisonGraphWrapper.vue';

export default {
    components: { ComparisonGraphWrapper, BarGraph },

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

    data () {
        return {
            graphOptions: {
                layout: { 
                    padding: { left: 0, right: 0, top: 0, bottom: 8 }
                },
                legend: {
                    display: false
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
                }
            }
        }
    },

    computed: {
        showInfo () {
            return !!this.$listeners.info;
        }
    },

    methods: {
        openInfo () {
            this.$emit('info');
        }
    }
}
</script>