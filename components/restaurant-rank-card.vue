<script src="/cdn/vuejs/vue.min.js"></script>
<script src="/cdn/vuejs/httpVueLoader.js"></script>

<template>
    <div class="card h-100">
        <div class="card-body flex-grow-0">
            <h5 class="card-title">
                <i class="fas fa-bread-slice"></i>
                {{type == 'brunch' ? '早餐' : '晚餐'}}排行榜！
            </h5>
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">吃什麼</th>
                            <th scope="col">次數</th>
                            <th scope="col">機率</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row,index) in ranked" :class="[index==0?'first-rank':'']" :key="index">
                            <th scope="row">{{index+1}}</th>
                            <td>{{row.restaurant}}</td>
                            <td>{{row.rank}}</td>
                            <td class="font-monospace">
                                {{calcProb(row.rank)}}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['type', 'ranked', 'entries'],
    data() {
        return {
            probBase: 1
        }
    },
    methods: {
        calcProb: function(count) {
            let typeStr = this.type == 'brunch' ? '早餐' : '晚餐'
            this.probBase = this.entries.filter(x=>x.when==typeStr).length
            return Math.round((count/this.probBase)*10000)/100
        }
    }
}
</script>
    