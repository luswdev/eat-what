<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="card h-100" :data-aos="aostype">
        <div class="card-body flex-grow-0">
            <h5 class="card-title">                
                <i v-if="type === 'brunch'" class="fas fa-bread-slice"></i>
                <i v-else class="fas fa-hamburger"></i>
                有這些{{type === 'brunch' ? '早餐' : '晚餐'}}！
            </h5>
        </div>
        <ul class="list-group list-group-flush">
            <li v-for="res in restaurants" class="list-group-item list-group-item-action" @click="EditRestaurant(res)" :key="res.rid">
                <span class="restuarant-name">{{res.restaurant}}</span>
                <button title="delete restaurant" type="button" class="btn-close float-end" :class="darkTheme ? 'btn-close-white' : ''" @click.stop="DeleteRestaurant(res.rid)"></button>
            </li>
            <li class="list-group-item">
                <form @submit.prevent="AddRestaruant">
                    <div class="input-group mb-3">
                        <input class="form-control" type="text" placeholder="吃這家早餐啦" aria-label="new brunch" v-model="newRestaurant">
                        <button title="add restaurant" type="button" class="btn btn-secondary" :disabled="newRestaurant === ''" @click="AddRestaruant"><i class="fas fa-plus"></i></button>
                    </div>
                </form>
            </li>
        </ul> 
    </div>
</template>

<script>
module.exports = {
    props: ['type', 'restaurants', 'theme', 'region', 'aostype'],
    data: function () {
        return {
            darkTheme: true,
            newRestaurant: ''
        }
    },
    methods: {
        AddRestaruant: function () {
            API.post('restaurant', {
                'new':  this.newRestaurant,
                'list': this.region,
                'when': this.type
            }).then( () => {
                this.$parent.GetRestaurantList()
                Toast.fire({
                    icon: 'success',
                    title: `我知道有「${this.newRestaurant}」可以吃喔！`
                })
                this.newRestaurant = ''
            }).catch( () => {
                Toast.fire({
                    icon:  'error',
                    title: '你確定？'
                })
            })
        },
        DeleteRestaurant: function (del) {
            API.delete('restaurant', {
                'res': del,
                'list': this.region,
            }).then( () => {
                this.$parent.GetRestaurantList()
                Toast.fire({
                    icon: 'success',
                    title: '刪除成功！我連刪了什麼都不記得了。'
                })
            })
        },
        EditRestaurant: function (res) {
            Swal.fire({
                title: res.restaurant,
                input: 'text',
                inputLabel: `重新命名 "${res.restaurant}"`,
                inputValue: res.restaurant,
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '就這樣！',
                cancelButtonText: '還是算了',
                inputValidator: (value) => {
                    if (!value) {
                        return '什麼啦！'
                    }
                }
            }).then( (ret) => {
                if (!ret.isConfirmed) {
                    return 
                }

                let value = ret.value
                if (value != res.restaurant) {
                    API.post('restaurant', {
                        'res': res.rid,
                        'new': value
                    }).then((res) => {
                        this.$parent.GetRestaurantList()
                        Toast.fire({
                            icon: 'success',
                            title: `成功修改名稱：${value}`
                        })
                    })
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: '啊不是長一樣嗎？'
                    })
                }
            })
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
