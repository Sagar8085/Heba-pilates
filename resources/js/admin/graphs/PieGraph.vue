<script>
import { Pie } from 'vue-chartjs';

export default {
    props: {
        data: Object,
        isDoughnut: Boolean,
        isSemiCircle: Boolean,
        options: Object
    },

    extends: Pie,

    methods: {
        rerenderChart () {
            if (this.isSemiCircle) {
                this.localOptions.rotation = 1 * Math.PI;
                this.localOptions.circumference = 1 * Math.PI;
            }

            this.renderChart(this.data, this.config);
        },

        updateData () {
            this.$nextTick(() => {
                this.$data._chart.update()
                this.rerenderChart();
            })
        }
    },

    mounted() {
        this.rerenderChart()
    },

    data () {
        return {
            localOptions: {}
        }
    },

    computed: {
        config () {
            return {
                cutoutPercentage: this.isDoughnut ? 75 : 0,
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
                    align: 'start'
                },
                ...this.localOptions,
                ...this.options
            }
        }
    }
}
</script>
