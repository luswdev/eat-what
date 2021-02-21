/**
 * footer.js
 */

'use strict';

let footer = new Vue({
    el: '#footer',
    data: {
        'darkTheme': header.darkTheme,
        'copyrightYear': 0,
        'versionNumber': ''
    },
    methods: {
        FeedBack: async function () {
            const { value: formValues } = await Swal.fire({
                title: '啊你有什麼問題',
                html:
                `<form class="row g-3">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="你的名字" id="fb-name">
                    </div>
                    <div class="col">
                        <input type="mail" class="form-control" placeholder="e-mail" id="fb-mail">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control" placeholder="主旨" id="fb-subject">
                    </div>
                    <div class="col-12">
                        <textarea class="form-control" id="fb-body" placeholder="沒有滿 50 個字，我可是不收的喔！" required></textarea>
                    </div>
                </form>`,
                focusConfirm: false,
                preConfirm: () => {
                    /* feedback message cannot be empty */
                    if (document.getElementById('fb-body').value == '') {
                        document.getElementById('fb-body').classList.add('is-invalid')
                        return false
                    }
                    return {
                        name: document.getElementById('fb-name').value,
                        mail: document.getElementById('fb-mail').value,
                        subj: document.getElementById('fb-subject').value,
                        mesg: document.getElementById('fb-body').value,
                    }
                }
            })
              
            if (formValues) {
                API.post('post-mail', formValues).then( (res) => {                      
                    Toast.fire({
                        icon: 'success',
                        title: '你真棒！'
                    })
                })
            } else {
                Swal.fire({
                    title:              '這三小',
                    text:               '沒東西是要講個毛？',
                    icon:               'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText:  '嘻嘻',
                })
            }
        }
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
