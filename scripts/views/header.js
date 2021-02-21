/**
 * header.js
 */

'use strict';

let header = new Vue({
    el: '#vue-header',
    data: {
        'picked': app.picked.current,
        'darkTheme': true,
    },
    mounted: function () { 
        this.darkTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches        
        app.darkTheme = this.darkTheme
    }
})
