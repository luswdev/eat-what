/**
 * footer.js
 */

'use strict';

let footer = new Vue({
    el: '#footer',
    data: {
        'darkTheme': true,
        'copyrightYear': 0,
        'versionNumber': '',
    },
    components: {
        'copyright': httpVueLoader('/eat/components/copyright.vue'),
        'b2t': httpVueLoader('/eat/components/b2t.vue')
    },
    mounted: function () {
        API.get('web-info').then((ret) => {
            let json = ret.data
            this.copyrightYear = json.copyright
            this.versionNumber = json.version

            let curYear = new Date().getFullYear()
            if (curYear != parseInt(this.copyrightYear)) {
                this.copyrightYear = `${this.copyrightYear} ~ ${curYear}`
            }
        })  
    }
})
