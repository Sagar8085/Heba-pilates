<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">video_library</i>
                        </div>
                        On Demand Category Order
                    </h1>

                    <h2 class="page-header__sub">Re-order Categories</h2>
                </div>
            </div>
        </section>

        <section class="back">
            <a @click="$router.go(-1)" class="navigation__back"><img src="/images/icons/backblue.png" alt=""> &nbsp; Back</a>
        </section>

        <section class="page-content">
            <h3 style="margin-bottom: 1.5rem;">{{ saved ? 'Changes Saved.' : 'Drag and drop your categories to which order you fit, your changes will save automatically.' }}</h3>

            <draggable class="draggable" v-model="categories" tag="transition-group" item-key="id" group="order" @start="drag=true" @end="drag=false; updateOrder()">
              <div v-for="(element, index) in categories" :key="element.id">#{{ (index + 1) }} - {{element.name}}</div>
            </draggable>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import draggable from 'vuedraggable'

export default {
    components: { draggable },

    data() {
        return {
            categories: [],
            saved: false
        }
    },

    mounted() {
        this.loadClasses();
    },

    methods: {
        loadClasses() {
            axios.get('/api/admin/on-demand/categories/order/edit').then(response => {
                this.categories = response.data;
                console.log(response.data);
            });
        },

        updateOrder() {
            axios.patch('/api/admin/on-demand/categories/order/update', {
                categories: this.categories
            }).then(response => {
                this.saved = true;

                var _this = this;
                setTimeout(function() {
                    _this.saved = false;
                }, 2500);
            });
        }
    }
}
</script>
