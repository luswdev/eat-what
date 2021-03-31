<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <nav class="navbar navbar-expand py-3" :class="darkTheme ? 'bg-dark navbar-dark' : 'bg-light navbar-light'">
        <div class="container">
            <span class="navbar-brand mb-0 h1 mr-0 mr-md-2">
                <i v-if="picked === 'brunch'" class="fas fa-bread-slice"></i>
                <i v-else class="fas fa-hamburger"></i>
                {{title}}
            </span>
            <div class="d-flex form-check form-switch">
                <input class="form-check-input" type="checkbox" id="themeSwitch" v-model="darkTheme">
                <label class="form-check-label ms-2" for="themeSwitch">選我！</label>
            </div>
        </div>
    </nav>
</template>

<script>
module.exports = {
    props: ['title', 'theme', 'picked'],
    data: function () {
        return {
            darkTheme: true
        }
    },
    watch: {
        darkTheme: function (newVal) {
            this.$emit('input', newVal)
        },
        theme: function (newVal) {
            this.darkTheme = newVal;
        }
    }
}
</script>
