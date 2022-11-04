<script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.min.js"
    integrity="sha256-KSlsysqp7TXtFo/FHjb1T9b425x3hrvzjMWaJyKbpcI=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/http-vue-loader@1.4.2/src/httpVueLoader.js"></script>

<template>
    <div class="card my-4" :data-aos="aostype">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-map-marked-alt"></i>
                有這些區域！
            </h5>
            <div class="table-responsive-sm">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">區域名</th>
                            <th scope="col">代號</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="reg in regions" :key="reg.name">
                            <td @click="UpdateRegion(reg)">{{reg.title}}</td>
                            <td @click="UpdateRegionID(reg)">{{reg.name}}</td>
                            <td>
                                <button title="delete region" type="button" class="btn-close float-end" :class="darkTheme ? 'btn-close-white' : ''" @click.stop="DeleteRegion(reg.title)"></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="我要這個區域" aria-label="new title" v-model="newRegion.title">
                <input type="text" class="form-control" placeholder="就叫它這個代號！" aria-label="new name" v-model="newRegion.name">
                <button title="add region"  type="button" class="btn btn-secondary" :disabled="newRegion.title === '' | newRegion.name === ''" @click="AddRegion">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: ['theme', 'regions', 'aostype'],
    data: function () {
        return {
            darkTheme: true,
            newRegion: {
                title: '',
                name: ''
            },
        }
    },
    methods: {
        AddRegion: function () {
            if (this.newRegion.title === '' || this.newRegion.name === '') {
                return
            }

            API.post('region', {
                'new': this.newRegion.title,
                'id': this.newRegion.name
            }).then( () => {
                this.$parent.GetRegionList()

                Toast.fire({
                    icon: 'success',
                    title: `新增區域：${this.newRegion.title}，或者你可以叫他：${this.newRegion.name}！`
                })
      
                this.newRegion.title = ''
                this.newRegion.name = ''
            })
        },
        DeleteRegion: function (name) {
            Swal.fire({
                title: `你確定要刪除"${name}"？`,
                text: "這是不可逆的動作欸，你確定？",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                focusCancel: true,
                confirmButtonText: '對啦對啦',
                cancelButtonText: '再看看',
                customClass: {
                    confirmButton: 'btn btn-primary mx-1',
                    cancelButton: 'btn btn-danger mx-1'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    API.delete('region', {
                        'del': name,
                    }).then( () => {
                        this.$parent.GetRegionList()
                        Toast.fire({
                            icon: 'success',
                            title: '刪除成功！我連刪了什麼都不記得了。'
                        })
                    })
                }
            })
        },
        UpdateRegion: function (reg) {
            Swal.fire({
                title: reg.title,
                input: 'text',
                inputLabel: `重新命名 "${reg.title}"`,
                inputValue: reg.title,
                showCancelButton: true,
                buttonsStyling: false,
                focusCancel: true,
                confirmButtonText: '就這樣！',
                cancelButtonText: '還是算了',
                customClass: {
                    confirmButton: 'btn btn-primary mx-1',
                    cancelButton: 'btn btn-secondary mx-1'
                },
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
                if (value != reg.title) {
                    API.post('region', {
                        'reg': reg.name,
                        'new': value
                    }).then( () => {
                        this.$parent.GetRegionList()
                        Toast.fire({
                            icon: 'success',
                            title: `已經改成${value}了！`
                        })
                    })
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: '啊不是長一樣嗎？',
                    })
                }
            })
        },
        UpdateRegionID: function (reg) {
            Swal.fire({
                title: '警告',
                text: '不可重新命名代號！',
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                focusCancel: true,
                confirmButtonText: '為什麼？',
                cancelButtonText: '好的',
                customClass: {
                    confirmButton: 'btn btn-danger mx-1',
                    cancelButton: 'btn btn-primary mx-1'
                }
            }).then( (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '你問題真多',
                        text: '不行就是不行！',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: '喔',
                        customClass: {
                            confirmButton: 'btn btn-danger',
                        }
                    })
                }
            })
        }
    },
    watch: {
        theme: function (newVal) {
            this.darkTheme = newVal;
        }
    },
}
</script>
