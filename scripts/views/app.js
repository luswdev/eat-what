/**
 * render.js
 */

'use strict';

let app = new Vue({
    el: '#app',
    data: {
        'restaurantResult': '-',
        'restaurants': {
            'brunch': [],
            'dinner': []
        },
        'regionList': [],
        'selectedRegion': '',
        'picked': {
            'brunch': 'brunch',
            'dinner': 'dinner',
            'current': 'brunch'
        },
        'histories': {
            'fullEntries': [],
            'displayEntries': [],
            'currentIdx': 0
        },
        'previewBtn': false,
        'nextBtn': true,
        'newBrunch': '',
        'newDinner': '',
        'newRegion': {
            'title': '',
            'name': ''
        },
        'openCnt': 0,
        'darkTheme': true,
        'started': false,
    },
    methods: {
        ChangeRegion: function () {
            this.GetRestaurantList()

            for (let i = 0; i < this.regionList.length; ++i) {
                if (this.selectedRegion == this.regionList[i].name) {
                    Cookies.set('default-index', i)
                    break
                }
            }
        },
        PickChange: function () {
            header.picked = this.picked.current
        },
        ShowResult: function () {
            this.openCnt++
            if (this.openCnt > 10) {
                this.openCnt = 0
                Swal.fire({
                    title:              '那就吃...',
                    text:               '自己想啦幹',
                    icon:               'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText:  '幹勒'
                })
                return;
            }

            /* dump restaurant list */
            let pool = this.restaurants.dinner.slice(0, this.restaurants.dinner.length)
            if (this.picked.current == this.picked.brunch) {
                pool = this.restaurants.brunch.slice(0, this.restaurants.brunch.length)
            }

            if (!pool || !pool.length) {
                Swal.fire({
                    title:              '那就吃...',
                    text:               '我也不知道要吃什麼，呵呵',
                    icon:               'warning',
                    confirmButtonColor: '#d33',
                    confirmButtonText:  '幹勒'
                })   
                return;
            }    

            this.Shuffle(pool, 5);
            var poolIdx = this.RandomRange(pool.length)
            this.restaurantResult = pool[poolIdx].restaurant    
            Swal.fire({
                title:              '那就吃...',
                html:               `<b class="result-name">${this.restaurantResult}</b>，你說好不好？`,
                showCancelButton:   true,
                confirmButtonColor: '#d33',
                cancelButtonColor:  '#3085d6',
                confirmButtonText:  '蛤？',
                cancelButtonText:   '好！'
            }).then((result) => {
                API.post('picked-log', {
                    'rid': pool[poolIdx].rid,
                    'list': this.selectedRegion,
                }).then( (res) => {
                    this.GrabHistory() 
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
        },
        GrabHistory: function () { 
            API.get('picked-log').then( (res) => {
                let historyRow = res.data
                this.histories.fullEntries    = historyRow
                this.histories.displayEntries = this.histories.fullEntries.slice(0, 10)
                this.histories.currentIdx     = 0
            })
        },
        Preview10: function () {
            if (this.histories.currentIdx - 10 >= 0) {
                this.histories.displayEntries = this.histories.fullEntries.slice(this.histories.currentIdx - 10, this.histories.currentIdx)
                this.histories.currentIdx    -= 10
            } else {
                this.histories.displayEntries = this.histories.fullEntries.slice(0, 10)
                this.histories.currentIdx     = 0
            }
            this.UpdateButton()
        },
        Next10: function () {
            if (this.histories.fullEntries.length >= this.histories.currentIdx + 20) {
                this.histories.displayEntries = this.histories.fullEntries.slice(this.histories.currentIdx + 10, this.histories.currentIdx + 20)
                this.histories.currentIdx    += 10
            } else {
                this.histories.displayEntries = this.histories.fullEntries.slice(this.histories.currentIdx + 10, this.histories.fullEntries.length)
                this.histories.currentIdx     = this.histories.fullEntries.length
            }
            this.UpdateButton()
        },
        UpdateButton: function () {
            if (this.histories.currentIdx != 0) {
                this.previewBtn = true
            } else {
                this.previewBtn = false
            }
            if (this.histories.currentIdx != this.histories.fullEntries.length) {
                this.nextBtn = true
            } else {
                this.nextBtn = false
            }
        },
        DeleteRestaurant: function (del) {
            API.delete('restaurant', {
                'res': del,
                'list': this.selectedRegion,
            }).then( () => {
                this.GetRestaurantList()
                Toast.fire({
                    icon: 'success',
                    title: '刪除成功！我連刪了什麼都不記得了。'
                })
            })
        },
        EditRestaurant: function (res) {
            Swal.fire({
                title:            res.restaurant,
                input:            'text',
                inputLabel:       `重新命名 "${res.restaurant}"`,
                inputValue:       res.restaurant,
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return '什麼啦！'
                    }
                }
            }).then( (ret) => {
                if (!ret.isConfirmed) {
                    return 
                }

                let value = ret.value
                if (value != res.restaurant) {
                    API.post('restaurant', { 
                        'res': res.rid,
                        'new': value
                    }).then((res) => {                    
                        this.GetRestaurantList()
                        Toast.fire({
                            icon: 'success',
                            title: `成功修改名稱：${value}`
                        })
                    })                      
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: '啊不是長一樣嗎？'
                    })
                }
            })
        },
        AddBrunch: function () {
            API.post('restaurant', { 
                'new': this.newBrunch,
                'list': this.selectedRegion,
                'when': 'brunch'
            }).then((res) => {                    
                this.GetRestaurantList()
                Toast.fire({
                    icon: 'success',
                    title: `我知道有${this.newBrunch}可以吃喔！`
                })
                this.newBrunch = ''
            }).catch( (err) => {
                Toast.fire({
                    icon: 'error',
                    title: '你確定？'
                })
            })
        },
        AddDinner: function () {
            API.post('restaurant', { 
                'new': this.newDinner,
                'list': this.selectedRegion,
                'when': 'dinner'
            }).then((res) => {                    
                this.GetRestaurantList()
                Toast.fire({
                    icon: 'success',
                    title: `我知道有${this.newDinner}可以吃喔！`
                })
                this.newDinner = ''
            })
        },
        AddRegion: function () {
            if (this.newRegion.title === '' || this.newRegion.name === '') {
                return
            }

            API.post('region', { 
                'new': this.newRegion.title,
                'id': this.newRegion.name
            }).then( (res) => {
                this.GetRegionList()

                Toast.fire({
                    icon: 'success',
                    title: `新增區域：${this.newRegion.title}，或者你可以叫他：${this.newRegion.name}！`
                })
      
                this.newRegion.title = ''
                this.newRegion.name = ''
            })
        },
        DeleteRegion: function (name) {
            Swal.fire({
                title:              `你確定要刪除"${name}"？`,
                text:               "這是不可逆的動作欸，你確定？",
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor:  '#d33',
                confirmButtonText:  '對啦對啦',
                cancelButtonText:   '再看看',
            }).then((result) => {
                if (result.isConfirmed) {
                    API.delete('region', { 
                        'del': name,
                    }).then( (res) => {
                        this.GetRegionList()
                        Toast.fire({
                            icon: 'success',
                            title: '刪除成功！我連刪了什麼都不記得了。'
                        })
                    })
                }
            })
        },
        UpdateRegion: function (reg) {
            Swal.fire({
                title:            reg.title,
                input:            'text',
                inputLabel:       `重新命名 "${reg.title}"`,
                inputValue:       reg.title,
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return '什麼啦！'
                    } 
                }
            }).then( (ret) => {
                if (!ret.isConfirmed) {
                    return 
                }

                let value = ret.value
                if (value != reg.title) {
                    API.post('region', { 
                        'reg': reg.name,
                        'new': value
                    }).then( (res) => {
                        this.GetRegionList()
                        Toast.fire({
                            icon: 'success',
                            title: `已經改成${value}了！`
                        })
                    })
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: '啊不是長一樣嗎？'
                    })
                }
            })
        },
        UpdateRegionID: function (reg) {
            Swal.fire({
                title:              '警告',
                text:               '不可重新命名代號！',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonColor: '#d33',
                cancelButtonColor:  '#3085d6',
                confirmButtonText:  '為什麼？',
                cancelButtonText:   '好的'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title:              '你問題真多',
                        text:               '不行就是不行！',
                        icon:               'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText:  '喔',
                    })
                }
            })
        },
        GetRestaurantList: function () {  
            this.started = false

            API.get(`restaurant/${this.selectedRegion}`).then((res) => {
                let restaurants = res.data
                this.restaurants.brunch = restaurants.brunch
                this.restaurants.dinner = restaurants.dinner
                this.started = true
            })
        },
        GetRegionList: function () {           
            API.get('region').then( (res) => {
                let defaultIdx = Cookies.get('default-index')

                let reg = res.data
                this.regionList = reg
                this.selectedRegion = this.regionList[defaultIdx].name
                this.GetRestaurantList()
            })
        },
        WatchIP: function (ip) { 
            window.open(`https://iplocation.com?ip=${ip}`, '_blank');
        }
    },
    mounted: function () {    
        if (!Cookies.get('default-index')) {
            Cookies.set('default-index', 0)
        }

        this.GetRegionList()
        this.GrabHistory()  

        /* check type by time default */
        let curHour = new Date().getHours()
        if (curHour > 16 || curHour < 2) {
            /* if is over 4 p.m. or early then 2 a.m. now */
            this.picked.current = this.picked.dinner
        } else {
            /* otherwise */
            this.picked.current = this.picked.brunch
        }

        /* update every 10 mins */
        setInterval(() => {
            this.GrabHistory()
        }, 600000);
    }
})
