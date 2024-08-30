<script>
import axios from 'axios';
import { Line } from 'vue-chartjs';

export default {
    props: {
        data: Object,
        options: Object
    },

    extends: Line,
    methods: {
        rerenderChart () {
            this.renderChart(this.data, this.config);
        }
    },

    mounted() {
        this.rerenderChart();
    },

    computed: {
        config () {
            return {
                layout: {
                    padding: {
                        left: 30,
                        right: 30,
                        top: 30,
                        bottom: 30
                    }
                },
                maintainAspectRatio: false,
                legend: {
                    display: false,
                    position: 'bottom',
                    align: 'start',
                    labels: {
                        padding: 25,
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
                            beginAtZero: true,
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, 0.035)",
                        }
                    }]
                },
                ...this.options
            }
        }
    }
}
</script>
