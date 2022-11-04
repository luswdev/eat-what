<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="card my-4" :data-aos="aostype">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-clock"></i>
                <span>我決定了 {{histories.length}} 次！</span>
                <div class="d-md-inline-block d-none float-md-end fs-6">
                    <span class="fw-normal">一目</span>
                    <select class="form-select form-select-sm d-inline w-auto" id="viewSelect" aria-label="viewSelect" v-model="rowPerView">
                        <option v-for="cnt in viewOption" :value="cnt" :key="cnt">
                            {{cnt == -1 ? '&#8734;' : cnt}}
                        </option>
                    </select>
                    <span class="fw-normal">行！</span>
                </div>
            </h5>
            <div class="table-responsive-md">
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
                            <td v-if="row.restaurant != null">{{row.restaurant}}</td>
                            <td v-else class="fst-italic" :class="darkTheme ? 'text-white-50' : 'text-black-50'">（已刪除的餐廳）</td>
                            <td>{{row.when}}</td>
                            <td class="font-monospace" @click="WatchIP(row.ip)">{{row.ip}}</td>
                            <td class="font-monospace">{{row.time}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button title="Preview 10 row" type="button" class="btn btn-primary" :disabled="previewBtn === false" @click="Preview10">
                        前 10 筆
                    </button>
                    <button title="Next 10 row" type="button" class="btn btn-primary" :disabled="nextBtn === false" @click="Next10">
                        後 10 筆
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['theme', 'histories', 'aostype'],
    data: function () {
        return {
            darkTheme: true,
            displayEntries: this.histories.slice(0, 10),
            currentIdx: 0,
            previewBtn: false,
            nextBtn: true,
            viewOption: [10, 20, 50, 100, -1],
            rowPerView: 10
        }
    },
    methods: {
        Preview10: function () {
            if (this.currentIdx - this.rowPerView >= 0) {
                this.displayEntries = this.histories.slice(this.currentIdx - this.rowPerView, this.currentIdx)
                this.currentIdx -= this.rowPerView
            } else {
                this.displayEntries = this.histories.slice(0, this.rowPerView)
                this.currentIdx = 0
            }
            this.UpdateButton()
        },
        Next10: function () {
            if (this.histories.length >= this.currentIdx + this.rowPerView * 2) {
                this.displayEntries = this.histories.slice(this.currentIdx + this.rowPerView, this.currentIdx + this.rowPerView * 2)
                this.currentIdx += this.rowPerView
            } else {
                this.displayEntries = this.histories.slice(this.currentIdx + this.rowPerView, this.histories.length)
                this.currentIdx = this.histories.length
            }
            this.UpdateButton()
        },
        UpdateButton: function () {
            if (this.rowPerView == -1) {
                this.nextBtn = false;
                this.previewBtn = false
            } else {
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
            }
        },
        WatchIP: function (ip) {
            window.open(`https://iplocation.com?ip=${ip}`, '_blank');
        }
    },
    watch: {
        histories: function (newEntries) {
            this.histories = newEntries;
            if (this.rowPerView == -1) {
                this.displayEntries = newEntries;
                this.currentIdx = 0
            } else {
                this.displayEntries = newEntries.slice(0, this.rowPerView)
                this.currentIdx = 0
            }
            this.UpdateButton()
        },
        rowPerView: function (newVal) {
            this.rowPerView = newVal;
            if (newVal == -1) {
                this.displayEntries = this.histories;
            } else {
                this.displayEntries = this.histories.slice(this.currentIdx, this.currentIdx + newVal)
            }
            this.UpdateButton()
        },
        theme: function (newVal) {
            this.darkTheme = newVal
        }
    }
}
</script>
