<template>
    <div>
        <div class="modal__box">
            <div class="modal__header">
                <h3 class="modal__title">Update {{ product.name }}</h3>
            </div>

            <form @submit.prevent="update">
                <div class="modal__body">
                    <div class="row row--form">
                        <div class="twelve columns">
                            <div class="form-element">
                            <span class="form-element__label">
                                * Name
                                <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                            </span>
                                <div class="form-element__control">
                                    <input type="text" v-model="name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--form">
                        <div class="twelve columns">
                            <div class="form-element">
                            <span class="form-element__label">
                                * Price
                                <span v-if="this.errors['price']">{{ this.errors['price'][0] }}</span>
                            </span>
                                <div class="form-element__control">
                                    <input type="text" v-model.lazy="price">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--form">
                        <div class="twelve columns">
                            <div class="form-element">
                                <label class="form-element__label">
                                    * Active
                                    <span v-if="this.errors['active']">{{ this.errors['active'][0] }}</span>
                                </label>
                                <div class="form-element__control">
                                    <select class="form-element__select" v-model="active">
                                        <option :value="1">Yes</option>
                                        <option :value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal__footer">
                    <button type="button" class="button button--outline" @click="$emit('cancel')">Cancel</button>
                    <button class="button" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import FormatsWatchedPrice from '../../mixins/handlesPrice';

export default {
    mixins: [
        FormatsWatchedPrice
    ],

    props: {
        product: Object,
    },

    data() {
        return {
            errors: [],
            name: this.product.name,
            price: this.product.price,
            active: this.product.active ? 1 : 0,
        }
    },

    methods: {
        update() {
            axios
                .patch('/api/admin/product/' + this.product.id, {
                    name: this.name,
                    price: this.price,
                    active: this.active,
                })
                .then(() => this.$emit('complete'))
                .catch(error => this.errors = error.response.data.errors);
        },
    }
}
</script>
