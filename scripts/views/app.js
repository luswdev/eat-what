/**
 * render.js
 */

'use strict'

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
        'fullEntries': [],
        'ranked': {
            'brunchRank': [],
            'dinnerRank': [],
            'countryRank': [],
        },
        'darkTheme': true,
        'started': false,
        'needAccept': false,
        'aos': {
            'up': '',
            'left': '',
            'right': ''
        }
    },
    components: {
        'cookies-accept': httpVueLoader('/eat/components/cookies-accept.vue'),
        'picker': httpVueLoader('/eat/components/picker-card.vue'),
        'history': httpVueLoader('/eat/components/history-card.vue'),
        'restaurant-list': httpVueLoader('/eat/components/restaurant-list-card.vue'),
        'region-list': httpVueLoader('/eat/components/region-list-card.vue'),
        'restaurant-rank': httpVueLoader('/eat/components/restaurant-rank-card.vue'),
        'visitor-rank': httpVueLoader('/eat/components/visitor-rank-card.vue'),
        'picked-time-rank': httpVueLoader('/eat/components/picked-time-rank-card.vue')
    },
    methods: {
        GrabHistory: function () { 
            API.get('picked-log').then( (res) => {
                let historyRow = res.data
                this.fullEntries = historyRow

                API.get(`rank/country`).then( (ret) => {
                    this.ranked.countryRank = ret.data
                })
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
        GetRegionList: function (defaultRegion = '') {
            API.get('region').then( (res) => {
                let reg = res.data
                this.regionList = reg

                if (defaultRegion !== '') {
                    let validRegion = false;
                    this.regionList.forEach(region => {
                        if (region.name === defaultRegion) {
                            validRegion = true
                            this.selectedRegion = defaultRegion
                        }
                    })

                    if (!validRegion) {
                        document.location.href = '/eat'
                    }
                } else if (Window.acceptCookies) {
                    let defaultIdx = Cookies.get('default-index')
                    this.selectedRegion = this.regionList[defaultIdx].name
                } else {
                    this.selectedRegion = this.regionList[0].name
                }

                this.GetRestaurantList()
            })
        },
        GetRankList: function (type) {
            API.get(`rank/${type}`).then( (res) => {
                let ranked = res.data
                this.ranked.brunchRank = ranked.brunch
                this.ranked.dinnerRank = ranked.dinner
            })
        }
    },
    beforeMount: function () {
        if (window.innerWidth >= 768) {
            this.aos.up = 'fade-up'
            this.aos.left = 'fade-left'
            this.aos.right = 'fade-right'
        } else {
            this.aos.up = 'fade-up'
            this.aos.left = 'fade-up'
            this.aos.right = 'fade-up'
        }
    },
    mounted: function () {
        Window.acceptCookies = true
        if (!Cookies.get('accept-cookies')) {
            this.needAccept = true
            Window.acceptCookies = false
        } 

        this.GrabHistory()
        this.GetRankList('restaurant')

        /* update every 10 mins */
        setInterval( () => {
            this.GrabHistory()
        }, 600000);
    },
    updated: function () {
        AOS.init({
            easing: 'ease-in-out-sine',
            duration: 700
        })
    }
})
