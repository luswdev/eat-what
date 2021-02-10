/**
 * main.js
 */

'use strict';

let app = new Vue({
    el: '#app',
    data: {
        'shown': false,
        'shown_bad': false,
        'shown_none': false,
        'restuarant_result': '-',
        'brunch': [],
        'dinner': [],
        'restuarant_list': [],
        'selected_list': '',
        'full_history': [],
        'history': [],
        'open_cnt': 0,
        'pick_brunch': 'brunch',
        'pick_dinner': 'dinner',
        'pick': 'brunch',
        'current_histroy_index': 0,
        'preview_btn': false,
        'next_btn': true,
        'new_brunch': '',
        'new_dinner': '',
        'new_region': {
            'title': '',
            'name': ''
        }
    },
    methods: {
        ChangeRegion: function () {
            this.GetRestuarantList()
        },
        PickChange: function () {
            header.pick = this.pick
        },
        ShowResult: function () {
            this.shown = false
            this.shown_bad = false
            this.shown_none = false

            this.open_cnt++
            if (this.open_cnt > 10) {
                this.open_cnt = 0
                Swal.fire({
                    title: '那就吃...',
                    text: '自己想啦幹',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: '幹勒'
                })
                return;
            }

            let pool = this.dinner.slice(0, this.dinner.length)
            if (this.pick == 'brunch') {
                pool = this.brunch.slice(0, this.brunch.length)
            }

            if (!pool || !pool.length) {
                Swal.fire({
                    title: '那就吃...',
                    text: '我也不知道要吃什麼，呵呵',
                    icon: 'warning',
                    confirmButtonColor: '#d33',
                    confirmButtonText: '幹勒'
                })   
                return;
            }    

            this.Shuffle(pool);
            var pool_index = this.RandomRange(pool.length)
            this.restuarant_result = pool[pool_index].restaurant    
            Swal.fire({
                title: '那就吃...',
                html: `<b class="result-name">${this.restuarant_result}</b>，你說好不好？`,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '蛤？',
                cancelButtonText: '好！'
            }).then((result) => {
                $.ajax({
                    type: 'POST',
                    url: 'api/log',
                    data: { 
                        'rid': pool[pool_index].rid,
                        'list': this.selected_list,
                    },
                    success: () => {
                        this.GrabHistroy() 
                    }
                })

                if (result.isConfirmed) {
                    this.ShowResult()
                }
            })

           
        },
        CloseResult: function () {
            this.shown = false
            this.shown_bad = false
            this.shown_none = false
        },
        Shuffle: function (array) {
            for (let i = array.length - 1; i > 0; i--) {
                let j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        },
        RandomRange: function (max) {
            return Math.floor(Math.random() * Math.floor(max));
        },
        GrabHistroy: function () { 
            $.getJSON(`api/log`, (histroy_row) => {
                this.full_history = histroy_row
                this.history = this.full_history.slice(0, 10);
                this.current_histroy_index = 0
            })
        },
        Preview10: function () {
            if (this.current_histroy_index - 10 >= 0) {
                this.history = this.full_history.slice(this.current_histroy_index - 10, this.current_histroy_index)
                this.current_histroy_index -= 10
            } else {
                this.history = this.full_history.slice(0, 10)
                this.current_histroy_index = 0
            }
            this.UpdateButton()
        },
        Next10: function () {
            if (this.full_history.length >= this.current_histroy_index + 20) {
                this.history = this.full_history.slice(this.current_histroy_index + 10, this.current_histroy_index + 20)
                this.current_histroy_index += 10
            } else {
                this.history = this.full_history.slice(this.current_histroy_index + 10, this.full_history.length)
                this.current_histroy_index = this.full_history.length
            }
            this.UpdateButton()
        },
        UpdateButton: function () {
            if (this.current_histroy_index != 0) {
                this.preview_btn = true
            } else {
                this.preview_btn = false
            }
            if (this.current_histroy_index != this.full_history.length) {
                this.next_btn = true
            } else {
                this.next_btn = false
            }
        },
        DeleteRestaruant: function (del) {
            $.ajax({
                type: 'DELETE',
                url: 'api/res',
                data: { 
                    'res': del,
                    'list': this.selected_list,
                },
                success: () => {                    
                    this.GetRestuarantList()
                }
            })
        },
        EditRestaruant: function (res) {
            Swal.fire({
                title: res.restaurant,
                input: 'text',
                inputLabel: `重新命名 "${res.restaurant}"`,
                inputValue: res.restaurant,
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return '什麼啦！'
                    } else {
                        $.ajax({
                            type: 'POST',
                            url: 'api/res',
                            data: { 
                                'res': res.rid,
                                'new': value
                            },
                            success: () => {                    
                                this.GetRestuarantList()
                            }
                        })
                    }
                }
            })
        },
        AddBrunch: function () {
            $.ajax({
                type: 'POST',
                url: 'api/res',
                data: { 
                    'new': this.new_brunch,
                    'list': this.selected_list,
                    'when': 'brunch'
                },
                success: () => {
                    this.GetRestuarantList()                    
                }
            })
            this.new_brunch = ''
        },
        AddDinner: function () {
            $.ajax({
                type: 'POST',
                url: 'api/res',
                data: { 
                    'new': this.new_dinner,
                    'list': this.selected_list,
                    'when': 'dinner'
                },
                success: () => {
                    this.GetRestuarantList()
                }
            })
            this.new_dinner = ''
        },
        AddRegion: function () {
            if (this.new_region.title === '' || this.new_region.name === '') {
                return
            }

            $.ajax({
                type: 'POST',
                url: 'api/reg',
                data: { 
                    'new': this.new_region.title,
                    'id': this.new_region.name
                },
                success: () => {
                    this.GetRegionList()
                }
            })
            this.new_region.title = ''
            this.new_region.name = ''
        },
        DeleteRegion: function (name) {
            Swal.fire({
                title: `你確定要刪除"${name}"？`,
                text: "這是不可逆的動作欸，你確定？",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '對啦對啦',
                cancelButtonText: '再看看',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: 'api/reg',
                        data: { 
                            'del': name,
                        },
                        success: () => {                    
                            this.GetRegionList()
                        }
                    })
                    Swal.fire(
                        '好喔！',
                        '成功刪除了...我刪了什麼？',
                        'success'
                    )
                }
            })
        },
        UpdateRegion: function (reg) {
            Swal.fire({
                title: reg.title,
                input: 'text',
                inputLabel: `重新命名 "${reg.title}"`,
                inputValue: reg.title,
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return '什麼啦！'
                    } else {
                        $.ajax({
                            type: 'POST',
                            url: 'api/reg',
                            data: { 
                                'reg': reg.name,
                                'new': value
                            },
                            success: () => {                    
                                this.GetRegionList()
                            }
                        })
                    }
                }
            })

        },
        GetRestuarantList: function () {  
            $.getJSON(`api/res/${this.selected_list}`, (res) => {
                this.brunch = res.brunch
                this.dinner = res.dinner
            })
        },
        GetRegionList: function () {            
            $.getJSON('api/reg', (reg) => {
                this.restuarant_list = reg
                this.selected_list = this.restuarant_list[0].name
                this.GetRestuarantList()
            })
        } 
    },
    mounted: function () {    
        $.getJSON('api/reg', (reg) => {
            this.restuarant_list = reg
            this.selected_list = this.restuarant_list[0].name
        }).then(()=> {
            this.GetRestuarantList()
        })   
        this.GrabHistroy()  

        /* check type by time default */
        let time = new Date().getHours()
        if (time > 16 || time < 2) {
            /* if is over 4 p.m. or early then 2 a.m. now */
            this.pick = 'dinner'
        } else {
            /* otherwise */
            this.pick = 'brunch'
        }

        /* update every 10 mins if picker is today */
        let updateLoop = setInterval(() => {
            this.GrabHistroy()
        }, 600000);
    }
})

let header = new Vue({
    el: '#vue-header',
    data: {
        'pick': app.pick
    }
})