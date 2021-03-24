<script src="/cdn/vuejs/vue.min.js"></script>
<script src="/cdn/vuejs/httpVueLoader.js"></script>

<template>
    <nav class="navbar navbar-expand py-3" :class="[darkTheme==true ? 'bg-dark navbar-dark' : 'bg-light navbar-light']">
        <div class="container">
            <span class="navbar-brand mb-0 h1 mr-0 mr-md-2">
                <i v-if="picked=='brunch'" class="fas fa-bread-slice"></i>
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
    data() {
        return {
            darkTheme: true
        }
    },
    watch: {
        darkTheme(newVal) {
            this.$emit('input', newVal)
        },
        theme(newVal) {
            this.darkTheme = newVal;
        }
    }
}
</script>
    