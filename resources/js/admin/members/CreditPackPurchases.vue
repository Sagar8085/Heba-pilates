<template>
<div>
    <PageHeader title="Member Profile" :subtitle="'Member #' + this.$route.params.id + ' - Credit Pack History'" icon="assignment_ind" />

    <section class="back">
        <router-link :to="'/admin/members/' + this.$route.params.id">
            <img src="/images/icons/backblue.png" alt="Back"> &nbsp; Back to Member Profile
        </router-link>
    </section>

    <div class="page-content">
        <div class="table-list">
            <div class="table-list__header">
                <h3>{{ creditPacks.length }} Credit Packs</h3>
            </div>

            <div class="table-list__scroll">
                <table class="table-list__table">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Pack</th>
                            <th>Studio Credits</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(creditPack, index) in creditPacks" :key="index" :class="{'strike-through': creditPack.deleted_at === null ? false : true}">
                            <td><router-link :to="'/admin/orders/' + creditPack.id">#{{ creditPack.id }}</router-link></td>
                            <td>{{ creditPack.pack.name }}</td>
                            <td>{{ creditPack.studio_credits + ' / ' + creditPack.pack.studio_credits }} Remaining</td>
                            <td><span :class="{'tag': true, 'tag--green': !creditPack.expired, 'tag--red': creditPack.expired}">{{ creditPack.expired ? 'Expired' : 'Active' }}</span></td>
                            <td>{{ creditPack.expires_human }}</td>
                            <td v-if="creditPack.deleted_at === null">
                                <i class="fas fa-pencil-alt colour--blue cursor--pointer" @click="editCreditPack(creditPack.id, index)" style="margin-right: 1rem;"></i>
                                <i class="far fa-trash-alt colour--red cursor--pointer" @click="confirmDeleteCreditPack(creditPack.id, index)"></i>
                            </td>
                            <td v-else>Deleted by {{ creditPack.deleter.name }}<br>{{ creditPack.deleted_at_human }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <modal v-model="showDeleteCreditPackConfirmationModal" hideClose hideCancel>
        <template>
            <span class="modal__title">Are you sure you wish to delete this credit pack?</span>

            <div class="modal__buttons">
                <button class="button" @click="showDeleteCreditPackConfirmationModal = false">No, Cancel</button>
                <button class="button button--red" @click="deleteCreditPack">Yes, Delete</button>
            </div>
        </template>
    </modal>
</div>
</template>

<script>
import axios from 'axios'
import PageHeader from '../layout/PageHeader.vue'
import Datepicker from 'vuejs-datepicker';
import Modal from '../../components/Modal.vue';

export default {
    components: { PageHeader, Datepicker, Modal },

    data () {
        return {
            creditPacks: [],
            loading: true,
            showDeleteCreditPackConfirmationModal: false
        }
    },

    mounted() {
        this.loadMemberships();
    },

    methods: {
        loadMemberships() {
            axios.get('/api/admin/members/' + this.$route.params.id + '/credit-packs/all').then(response => {
                this.creditPacks = response.data;
            });
        },

        confirmDeleteCreditPack(credit_pack_id, ref) {
            this.creditPackToDelete = credit_pack_id;
            this.creditPackToDeleteRef = ref;
            this.showDeleteCreditPackConfirmationModal = true;
        },

        deleteCreditPack() {
            axios.delete('/api/admin/members/' + this.$route.params.id + '/credit-packs/' + this.creditPackToDelete).then(response => {
                alert('Credit Pack Deleted! If you would like to refund this purchase, please do it through Stripe.');
                this.creditPackToDelete = null;
                this.creditPackToDeleteRef = null;
                this.showDeleteCreditPackConfirmationModal = false;
                this.loadMemberships();
            })
        },

        editCreditPack(creditPackId) {
            this.$router.push('/admin/orders/' + creditPackId);
        },
    }
}
</script>
