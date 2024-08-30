<script>
import { HorizontalBar } from 'vue-chartjs';

export default {
    props: {
        data: Object
    },

    extends: HorizontalBar,

    mounted() {
        this.addPlugin({
            id: 'custom-labels',
            afterDraw: chart => {
                let ctx = chart.chart.ctx;
                ctx.save();
                ctx.textAlign = 'right';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 12px sans-serif';
                let yAxis = chart.scales["y-axis-0"];
                
                yAxis.ticks.forEach((v, i) => {
                    let value = chart.data.datasets[0].data[i];
                    let x = chart.width - this.options.layout.padding.right;
                    let y = yAxis.getPixelForTick(i) - this.graphData.datasets[0].barThickness;
    
                    ctx.fillText(value + '%', x, y);
                });
                ctx.restore();
            }
        })
        this.renderChart(this.graphData, this.options);
    },

    computed: {
        total () {
            return this.data.datasets[0].data.reduce((v, s) => v + s, 0);
        },
        graphData () {
            const dataset = this.data.datasets[0];
            return {
                ...this.data,
                datasets: [
                    {
                        ...dataset,
                        barThickness: dataset.barThickness || 20,
                        data: dataset.data.map(d => this.getPercentage(d)),
                        stack: true
                    },
                    {
                        backgroundColor: '#3392d020',
                        barThickness: dataset.barThickness || 20,
                        data: dataset.data.map(d => 100 - this.getPercentage(d)),
                        stack: true
                    }
                ]
            }
        }
    },

    methods: {
        getPercentage (value) {
            return Math.round((value / this.total) * 100)
        }
    },

    data () {
        return {
            options: {
                layout: {
                    padding: {
                        left: 30,
                        right: 30,
                        bottom: 30
                    }
                },
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        display: false,
                        gridLines: {
                            display: false
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        gridLines: {
                            display: false
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        title: items => this.data.labels[items[0].index],
                        label: (item) => {
                            if (item.datasetIndex < this.data.datasets.length) {
                                const value = this.data.datasets[item.datasetIndex].data[item.index];
                                const label = this.data.datasets[item.datasetIndex].label;

                                return label ? label + ': ' + value : value;
                            }
                        }
                    }
                }
            }

        }
    }
}
</script>
