<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="card" :data-aos="aostype">
        <div class="card-body flex-grow-0">
            <h5 class="card-title">
                <i class="fas fa-question-circle"></i>
                吃哪餐好呢？
            </h5>
            <div id="when-chart" style="width: 100%; height:400px;"></div>
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
            option:  {
                color: [ '#5470c6', '#fac858', '#91cc75', '#ee6666', '#73c0de', '#3ba272', '#fc8452', '#9a60b4', '#ea7ccc' ],
                darkMode: this.theme,
                textStyle: {
                    fontFamily: 'Noto Sans TC',
                    color: this.theme ? '#fff' : '#212529'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: ['早餐', '晚餐'],
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
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: ['次數']
                },
                series: [
                    {
                        name: '早餐',
                        type: 'bar',
                        stack: 'total',
                        label: {
                            show: true
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: [this.ranked.filter(x => x.when === '早餐').length]
                    },
                    {
                        name: '晚餐',
                        type: 'bar',
                        stack: 'total',
                        label: {
                            show: true
                        },
                        emphasis: {
                            focus: 'series'
                        },
                        data: [this.ranked.filter(x => x.when === '晚餐').length]
                    }
                ]
            }
        }
    },
    watch: {
        theme: function (newVal) {
            this.option.darkMode = newVal;
            this.option.textStyle.color = newVal ? '#fff' : '#212529'
            this.option.legend.textStyle.color = newVal ? '#fff' : '#212529'
            this.option.legend.pageTextStyle.color = newVal ? '#fff' : '#212529'
            this.chart.setOption(this.option)
        },
        ranked: function (newData) {    
            this.option.series[0].data[0] = newData.filter(x => x.when === '早餐').length
            this.option.series[1].data[0] = newData.filter(x => x.when === '晚餐').length
            this.chart.setOption(this.option)
        }
    },
    mounted: function() {
        this.chartDom = document.getElementById('when-chart'),
        this.chart = echarts.init(this.chartDom),
        this.chart.setOption(this.option)
    }
}
</script>
