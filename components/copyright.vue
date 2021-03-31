<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="footer-copyright font-monospace container" :class="darkTheme ? 'text-white-50' : 'text-black-50'">
        <hr />
        <div class="row">
            <div class="col-auto mt-1">
                <span>&copy; {{ year }} LuSkywalker v{{ version }}</span>
            </div>
            <ul class="col-auto list-inline mt-1">
                <li class="list-inline-item">
                    <a href="https://github.com/luswdev/eat-what" target="_blank" class="text-decoration-none">
                        參考原始碼
                    </a>
                </li>
                <li class="list-inline-item">
                    <a href="https://github.com/luswdev/eat-what/issues/new" target="_blank" class="text-decoration-none">
                        意見反饋
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['theme', 'year', 'version'],
    data: function () {
        return {
            isTop: true,
            darkTheme: true,
            styles: {
                bottom: '-60px !important'
            }
        }
    },
    watch: {
        theme: function (newVal) {
            this.darkTheme = newVal
        }
    }
}
</script>
