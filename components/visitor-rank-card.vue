<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="card" :data-aos="aostype">
        <div class="card-body flex-grow-0">
            <h5 class="card-title">
                <i class="fas fa-globe-asia"></i>
                哪些人這麼閒？
            </h5>
            <div id="country-chart" style="width: 100%; height:400px;"></div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['theme', 'ranked', 'aostype'],
    data: function () {
        return {
            chartDom: undefined,
            chart: undefined,
            option: {
                darkMode: this.theme,
                textStyle: {
                    fontFamily: 'Noto Sans TC'
                },
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    type: 'scroll',
                    top: '5%',
                    left: 'center',
                    textStyle: {
                        color: this.theme ? '#fff' : '#212529'
                    },
                    pageIconColor: '#0d6efd',
                    pageIconInactiveColor: '#6c757d',
                    pageTextStyle: {
                        color: this.theme ? '#fff' : '#212529'
                    }
                },
                series: [
                    {
                        name: '就是你！',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        center: ['50%', '55%'],
                        avoidLabelOverlap: false,
                        label: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            label: {
                                show: true,
                                fontSize: '40',
                                fontWeight: 'bold'
                            }
                        },
                        labelLine: {
                            show: false
                        },
                        data: this.ranked
                    }
                ]
            }
        }
    },
    watch: {
        theme: function (newVal) {
            this.option.darkMode = newVal;
            this.option.legend.textStyle.color = newVal ? '#fff' : '#212529'
            this.option.legend.pageTextStyle.color = newVal ? '#fff' : '#212529'
            this.chart.setOption(this.option)
        },
        ranked: function (newData) {    
            this.option.series[0].data = newData
            this.chart.setOption(this.option)
        }
    },
    mounted: function () {   
        this.chartDom = document.getElementById('country-chart'),
        this.chart = echarts.init(this.chartDom),
        this.chart.setOption(this.option)
    }
}
</script>
