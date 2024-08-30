<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        Memberships
                    </h1>

                    <h2 class="page-header__sub">View Membership</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="cards">
                <div class="card booking-card booking-card--large">
                    <section class="booking-card__header">
                        <h1 :class="`booking-card__header__title`">
                            Membership #{{ order.id }}
                        </h1>
                        <p class="booking-card__header__subtitle">
                            <template>{{ order.orderable_type_human }}</template>
                        </p>
                        <dl class="booking-card__header__list">
                            <div class="booking-card__header__list__item">
                                <dt>Paid</dt>
                                <dd>{{ order.value_human }}</dd>
                            </div>
                            <div class="booking-card__header__list__item">
                                <dt>Method</dt>
                                <dd>{{ order.method_human }}</dd>
                            </div>
                            <div class="booking-card__header__list__item">
                                <dt>When</dt>
                                <dd>{{ order.created_human }}</dd>
                            </div>
                        </dl>
                        <dl class="booking-card__header__list">
                            <div class="booking-card__header__list__item">
                                <dt>Expires</dt>
                                <dd>
                                    {{ order.expires_human }}
                                    <a href="javascript: void();" @click="editExpiryModal = true" class="edit-expiry">Edit Expiry</a>
                                </dd>
                            </div>
                        </dl>
                    </section>
                    <section class="booking-card__content">
                        <section class="booking-card__section booking-card__section--row" style="padding-bottom: 0;">
                            <div class="booking-card__trainer">
                                <p class="booking-card__trainer__content">
                                    <router-link :to="`/admin/members/${order.member.id}`" class="router-link router-link--block">
                                        {{ order.member.name }}
                                    </router-link>
                                    <span>{{ order.member.email }}</span>
                                </p>
                            </div>
                        </section>
                    </section>
                </div>
            </div>
        </section>

        <modal v-model="editExpiryModal" hideCancel>
            <template>
                <span class="modal__title">Please select a new expiry date</span>

                <datepicker :value="order.expires" @selected="selectExpiry" inline class="datepicker"></datepicker>

                <div class="modal__buttons" v-if="newExpiry">
                    <button class="button" @click="updateExpiry">Save</button>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
import axios from 'axios';
import Datepicker from 'vuejs-datepicker';
import Modal from '../../components/Modal.vue';

export default {
    components: { Modal, Datepicker },

    data() {
        return {
            loading: true,
            editExpiryModal: false,
            newExpiry: null,
            order: {
                member: {},
                trainer: {},
                orderable: {}
            }
        }
    },

    mounted() {
        this.loadOrder();
    },

    methods: {
        loadOrder() {
            axios.get('/api/admin/billing/memberships/' + this.$route.params.id).then(response => {
                this.order = response.data;
            })
            .catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
            .finally(() => this.loading = false);
        },

        selectExpiry(payload) {
            this.newExpiry = payload
        },

        updateExpiry() {
            axios.patch(`/api/admin/billing/memberships/${this.$route.params.id}`, {
                expires: this.newExpiry
            }).then((response) => {
                this.editExpiryModal = false;
                this.order = response.data.data;
            });
        },
    }
}
</script>

<style scoped>
.edit-expiry {
    margin-left: 1rem;
    font-size: .75rem;
    color: #17B5C8;
}

.datepicker {
    display: block;
    margin: auto;
    width: 300px;
}

.booking-card__header__list:not(:first-of-type) {
    margin-top: 1rem;
}
</style>
