<script>
import axios from 'axios';
import { Bar } from 'vue-chartjs';

export default {
    props: {
        data: Object,
        options: Object,
        stacked: Boolean
    },

    extends: Bar,

    mounted() {
        this.renderChart(this.data, this.config);
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
                    position: 'bottom',
                    align: 'start',
                    labels: {
                        padding: 25,
                    }
                },
                scales: {
                    xAxes: [{
                        stacked: this.stacked ? true : false,
                        gridLines: {
                            color: "rgba(0, 0, 0, 0)",
                        }
                    }],
                    yAxes: [{
                        stacked: this.stacked ? true : false,
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
