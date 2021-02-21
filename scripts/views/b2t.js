/**
 * b2t.js
 */

'use strict';

let b2t = new Vue({
    el: '#b2t',
    data: {
        'isTop': true,
        'darkTheme': header.darkTheme,
        'styles': {
            bottom: '-60px !important'
        }
    },
    methods: {
        backToTop: function () {
            this.scrollToTop()
            document.getElementById('b2t').blur()
        },
        scrollToTop: function () {
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }
    },
    mounted: function () {
        window.addEventListener('scroll', () => {
            this.isTop = !(document.documentElement.scrollTop > 0);
            this.styles.bottom = this.isTop ? '-60px !important' : '0' 
        })
    }
})
