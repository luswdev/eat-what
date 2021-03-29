/**
 * header.js
 */

'use strict';

let header = new Vue({
    el: '#vue-header',
    data: {
        'picked': '',
        'darkTheme': undefined,
    },
    components: {
        'nav-bar': httpVueLoader('/eat/components/nav-bar.vue')
    },
    watch: {
        darkTheme(theme) {
            this.darkTheme = theme
            if (this.darkTheme) {
                document.body.classList.add('dark')
                document.body.classList.remove('light')
            } else {
                document.body.classList.add('light')
                document.body.classList.remove('dark')
            }
            app.darkTheme = this.darkTheme
            footer.darkTheme = this.darkTheme
        }
    },
    mounted: function () { 
        this.darkTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    }
})
