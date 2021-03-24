<script src="/cdn/vuejs/vue.min.js"></script>
<script src="/cdn/vuejs/httpVueLoader.js"></script>

<template>
    <button title="Back to top" class="btn position-fixed bottom-0 end-0 m-3" :class="[darkTheme ? 'btn-secondary' : 'bg-light']" @click="backToTop" id="b2t" :style="styles">
        <i class="fas fa-chevron-up"></i>
    </button>
</template>

<script>
module.exports = {
    props: ['theme'],
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
        backToTop: function () {
            this.scrollToTop()
        },
        scrollToTop: function () {
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }
    },
    watch: {
        theme(newVal) {
            this.darkTheme = newVal;
        }
    },
    mounted: function () {
        window.addEventListener('scroll', () => {
            this.isTop = !(document.documentElement.scrollTop > 0);
            this.styles.bottom = this.isTop ? '-60px !important' : '0' 
        })
    }
}
</script>
    