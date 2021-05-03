<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 position-fixed bottom-0 start-0 m-3" id="accept-card" :data-aos="aostype">
        <div class="card m-auto m-md-4 m-lg-5">
            <div class="card-body">
                <h5 class="card-title">要吃餅乾嗎?</h5>
                <p class="card-text">
                    本網站需要你的餅乾（cookies）來讓你獲得更好的使用者體驗，請放心，餅乾是你的，我們沒有能力與別人分享你的餅乾。<br />
                    （翻譯年糕：Cookies 是存放在使用者本機上的資源，伺服器不會存放任何使用者相關的資訊。）
                </p>
                <button class="btn btn-primary" @click="AcceptCookies">同意啦</button>
                <button class="btn btn-secondary" @click="DenyCookies">不要勒</button>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['aostype'],
    methods: {
        AcceptCookies: function () {
            Cookies.set('accept-cookies', true, { expires: 365, path: 'eat' })
            Window.acceptCookies = true
            this.$emit('input', false)
        },
        DenyCookies: function () {
            Cookies.remove('accept-cookies')
            Cookies.remove('default-index')
            Window.acceptCookies = false
            this.$emit('input', false)
        }
    }
}
</script>
