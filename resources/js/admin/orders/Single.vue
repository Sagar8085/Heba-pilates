<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">shopping_cart</i>
                        </div>
                        Credit Packs
                    </h1>

                    <h2 class="page-header__sub">Credit Purchase #{{ this.purchase.id }}</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="cards">
                <div class="card booking-card booking-card--large">
                    <section class="booking-card__header">
                        <h1 :class="`booking-card__header__title`">
                            Credit Purchase #{{ this.purchase.id }}
                        </h1>
                        <p class="booking-card__header__subtitle">
                            <template>{{ this.purchase.pack.name }}</template>
                        </p>
                        <dl class="booking-card__header__list">
                            <div class="booking-card__header__list__item">
                                <dt>Remaining Credits</dt>
                                <dd>{{ this.purchase.studio_credits }}</dd>
                            </div>
                            <div class="booking-card__header__list__item">
                                <dt>Expires</dt>
                                <dd>{{ this.purchase.expires_human }} <a href="javascript: void();" @click="editExpiryModal = true" style="margin-left: 1rem; font-size: .75rem; color: #17B5C8">Edit Expiry</a></dd>
                            </div>
                        </dl>

                        <br>

                        <span :class="{'tag': true, 'tag--green': !this.purchase.expired, 'tag--red': this.purchase.expired}">{{ this.purchase.expired ? 'Expired' : 'Active' }}</span>
                    </section>
                </div>
            </div>

            <div class="lead-management__profile__details__about__box" v-if="purchase.events.length > 0">
                <div class="lead-management__profile__details__about__box__activity" v-for="log in purchase.events">
                    <div class="lead-management__profile__details__about__box__activity__border"></div>

                    <img
                        class="lead-management__profile__details__about__box__activity__icon"
                        :src="log.type ? log.type.image_path : '/images/icons/note-24px.svg'"
                    />

                    <div class="lead-management__profile__details__about__box__activity__container">
                        <label class="lead-management__profile__details__about__box__activity__details">{{ log.message }}</label>
                        <label class="lead-management__profile__details__about__box__activity__extra">
                            {{ log.author.name }} - {{ log.created_human }}
                            <span v-if="log.note_id"> &bull; <a href="javascript:void(0)" @click="showNoteModal(log)">View note</a></span>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <modal v-model="editExpiryModal" hideCancel>
            <template>
                <span class="modal__title">Please select a new expiry date</span>

                <datepicker :value="newExpiry" @selected="this.selectExpiry" :format="'yyyy-MM-dd'" inline style="display: block; margin: 1rem auto 0 auto; width: 300px"></datepicker>

                <div class="modal__buttons" v-if="newExpiry !== ''">
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
            purchase: {},
            editExpiryModal: false,
            newExpiry: ''
        }
    },

    mounted() {
        this.loadPurchase();
    },

    methods: {
        loadPurchase() {
            axios.get('/api/admin/billing/credit-packs/' + this.$route.params.id).then(response => {
                this.purchase = response.data;
            })
            .catch(error => {
                if(error.response.status === 403) {
                    this.$router.push('/admin/permission-denied');
                }
            })
            .finally(() => this.loading = false);
        },

        selectExpiry: function(payload) {
            var newDate = new Date(payload);
            var newMonth = ("0" + (newDate.getMonth() + 1)).slice(-2);
            this.newExpiry = newDate.getFullYear() + '-' + newMonth + '-' + ("0" + newDate.getDate()).slice(-2);
        },

        updateExpiry() {
            axios.patch('/api/admin/billing/credit-packs/' + this.$route.params.id + '/expiry', {
                expiry: this.newExpiry
            }).then(response => {
                this.editExpiryModal = false;
                this.loadPurchase();
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        }
    }
}
</script>
