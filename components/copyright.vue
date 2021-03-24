<script src="/cdn/vuejs/vue.min.js"></script>
<script src="/cdn/vuejs/httpVueLoader.js"></script>

<template>
    <div class="footer-copyright font-monospace container" :class="darkTheme ? 'text-white-50' : 'text-black-50'">
        <hr>
        <div class="row">
            <div class="col-auto">
                <span>&copy; {{year}} LuSkywalker v{{version}}</span>
            </div>
            <ul class="col-auto list-inline">
                <li class="list-inline-item">
                    <a href="https://github.com/luswdev/eat-what" target="_blank" class="text-decoration-none">參考原始碼</a>
                </li>
                <li class="list-inline-item">
                    <a href="#!" class="text-decoration-none" @click="FeedBack">意見反饋</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['theme', 'year', 'version'],
    data() {
        return {
            isTop: true,
            darkTheme: true,
            styles: {
                bottom: '-60px !important'
            }
        }
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
    watch: {
        theme(newVal) {
            this.darkTheme = newVal;
        }
    }
}
</script>
    