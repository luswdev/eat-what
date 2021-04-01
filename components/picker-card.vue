<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="card my-4" :data-aos="aostype">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-utensils"></i>
                我想吃...
            </h5>
            <div class="row g-2">
                <div class="form-floating col-md-8">
                    <select class="form-select" id="regionSelect" aria-label="regionSelect" v-model="selecting">
                        <option v-for="list in regions" :value="list.name" :key="list.name">
                            {{list.title}}
                        </option>
                    </select>
                    <label for="regionSelect">選擇區域</label>
                </div>
                <div class="col-md-4 text-end">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" v-model="picked.current" :value="picked.brunch">
                        <label class="form-check-label" for="inlineRadio1">吃早餐</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" v-model="picked.current" :value="picked.dinner">
                        <label class="form-check-label" for="inlineRadio2">吃晚餐</label>
                    </div>
                    <button class="btn mr-1 btn-primary" @click="ShowResult" :disabled="!started">幫我想！</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['regions', 'current', 'started', 'restaurants', 'aostype'],
    data: function () {
        return {
            restaurantResult: '-',
            openCnt: 0,
            selecting: '',
            picked: {
                brunch: 'brunch',
                dinner: 'dinner',
                current: ''
            },
        }
    }, 
    methods: {
        ShowResult: function () {
            this.openCnt++
            if (this.openCnt > 10) {
                this.openCnt = 0
                Swal.fire({
                    title: '那就吃...',
                    text: '自己想啦幹',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: '幹勒'
                })
                return;
            }

            /* dump restaurant list */
            let pool = this.restaurants.dinner.slice(0, this.restaurants.dinner.length)
            if (this.picked.current === this.picked.brunch) {
                pool = this.restaurants.brunch.slice(0, this.restaurants.brunch.length)
            }

            if (!pool || !pool.length) {
                Swal.fire({
                    title: '那就吃...',
                    text: '我也不知道要吃什麼，呵呵',
                    icon: 'warning',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: '幹勒'
                })
                return;
            }

            this.Shuffle(pool, 5);
            var poolIdx = this.RandomRange(pool.length)
            this.restaurantResult = pool[poolIdx].restaurant
            Swal.fire({
                title: '那就吃...',
                html: `<b class="result-name">${this.restaurantResult}</b>，你說好不好？`,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#0d6efd',
                confirmButtonText: '蛤？',
                cancelButtonText: '好！'
            }).then( (result) => {
                API.post('picked-log', {
                    'rid': pool[poolIdx].rid,
                    'list': this.cur,
                }).then( () => {
                    this.$parent.GrabHistory()
                })

                if (result.isConfirmed) {
                    this.ShowResult()
                }
            })
        },
        Shuffle: function (array, cnt) {
            while (cnt--) {
                for (let i = array.length - 1; i > 0; i--) {
                    let j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]]
                }
            }
        },
        RandomRange: function (max) {
            return Math.floor(Math.random() * Math.floor(max))
        }
    },
    watch: {
        current: function (newVal) {
            this.selecting = newVal
        },
        'picked.current': function (newVal) {
            header.picked = newVal
        },
        selecting: function (newVal) {
            this.$emit('input', newVal)
            this.$parent.GetRestaurantList()

            if (Window.acceptCookies) {  
                for (let i = 0; i < this.regions.length; ++i) {
                    if (this.selecting === this.regions[i].name) {
                        Cookies.set('default-index', i, { expires: 365 })
                        break
                    }
                }
            }
        }
    },
    mounted: function () {
        if (Window.acceptCookies) {
            if (!Cookies.get('default-index')) {
                Cookies.set('default-index', 0, { expires: 365 })
            }
        }

        let defaultRegion = ''
        if (document.location.href.length >= 5 && document.location.href.split('/')[4] === 'region') {
            defaultRegion = document.location.href.split('/')[5]
        }
        
        this.$parent.GetRegionList(defaultRegion)

        /* check type by time default */
        let curHour = new Date().getHours()
        if (curHour > 16 || curHour < 2) {
            /* if is over 4 p.m. or early then 2 a.m. now */
            this.picked.current = this.picked.dinner
        } else {
            /* otherwise */
            this.picked.current = this.picked.brunch
        }

        this.selectingRegion = this.current
    }
}
</script>
