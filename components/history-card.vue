<script src="/cdn/vuejs/vue.min.js"></script>
<script src="/cdn/vuejs/httpVueLoader.js"></script>

<template>
    <div class="card my-4">
        <div class="card-body">                    
            <h5 class="card-title">
                <i class="fas fa-clock"></i> 
                我決定了 {{histories.length}} 次！
            </h5>
            <div class="table-responsive-sm">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">吃什麼</th>
                            <th scope="col">吃哪餐</th>
                            <th scope="col">誰按的</th>
                            <th scope="col">幾點</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in displayEntries" :key="row.pid">
                            <th scope="row">{{row.pid}}</th>
                            <td>{{row.restaurant}}</td>
                            <td>{{row.when}}</td>
                            <td class="font-monospace" @click="WatchIP(row.ip)">{{row.ip}}</td>
                            <td class="font-monospace">{{row.time}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button title="Preview 10 row" type="button" class="btn btn-primary" :disabled="previewBtn == false" @click="Preview10">前 10 筆</button>
                    <button title="Next 10 row" type="button" class="btn btn-primary" :disabled="nextBtn == false" @click="Next10">後 10 筆</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['histories'],
    data() {
        return {
            displayEntries: this.histories.slice(0, 10),
            currentIdx: 0,
            previewBtn: false,
            nextBtn: true
        }
    }, 
    methods: {
        Preview10: function () {
            if (this.currentIdx - 10 >= 0) {
                this.displayEntries = this.histories.slice(this.currentIdx - 10, this.currentIdx)
                this.currentIdx    -= 10
            } else {
                this.displayEntries = this.histories.slice(0, 10)
                this.currentIdx     = 0
            }
            this.UpdateButton()
        },
        Next10: function () {
            if (this.histories.length >= this.currentIdx + 20) {
                this.displayEntries      = this.histories.slice(this.currentIdx + 10, this.currentIdx + 20)
                this.currentIdx         += 10
            } else {
                this.displayEntries       = this.histories.slice(this.currentIdx + 10, this.histories.length)
                this.currentIdx = this.histories.length
            }
            this.UpdateButton()
        },
        UpdateButton: function () {
            if (this.currentIdx != 0) {
                this.previewBtn = true
            } else {
                this.previewBtn = false
            }
            if (this.currentIdx != this.histories.length) {
                this.nextBtn = true
            } else {
                this.nextBtn = false
            }
        },
        WatchIP: function (ip) { 
            window.open(`https://iplocation.com?ip=${ip}`, '_blank');
        }
    },
    watch: {
        histories(newEntries) {
            this.displayEntries = newEntries.slice(0, 10)
            this.currentIdx = 0
            this.previewBtn = false
            this.nextBtn = true
        }
    }
}
</script>
    