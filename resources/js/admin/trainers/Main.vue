<template>
    <div>
        <section class="page-header page-header--no-bottom" v-if="this.trainers.length > 0">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">supervisor_account</i>
                        </div>
                        Instructors
                    </h1>

                    <h2 class="page-header__sub">All Instructors</h2>
                </div>

                <div class="page-header__col">
                    <button @click="displayCreateModal = true" class="button">
                        Create Instructor
                        <i class="material-icons">add_circle</i>
                    </button>
                </div>
            </div>
        </section>

        <section>
            <div v-if="this.trainers.length > 0" class="page-content page-content--small-top">
                <filterable-data-table
                    :cols="trainersListHeadings"
                    :rows="trainers"
                    :pagination="pagination"
                    :highlightedCol="1"
                    title="Instructors"
                    searchPlaceholder="Search Instructors"
                    v-on:nextPage="loadTrainers((pagination.current_page + 1))"
                    v-on:previousPage="loadTrainers((pagination.current_page - 1))">
                    <template v-slot:cell-image="{ item }">
                        <profile-picture :image="item.avatar" :firstName="item.first_name" :lastName="item.last_name" />
                    </template>
                    <template v-slot:cell-name="{ item, cell }">
                        <router-link :to="{ name: 'TrainerSingle', params: { id: item.id }}">{{ cell }}</router-link>
                    </template>
                    <!-- end dummy data -->
                    <template v-slot:cell-actions="{ item }">
                        <router-link :to="'/admin/trainers/' + item.id " class="button button--icon button--transparent">
                            <i class="fas fa-pen" />
                        </router-link>
                    </template>
                </filterable-data-table>
            </div>

            <section class="forbidden forbidden--full" v-else-if="!this.loading && this.trainers.length == 0">
                <div>
                    <img src="/images/illustrations/video-streaming.svg">
                    <h2>No Trainers</h2>
                    <p>Why not create your first one?</p>
                    <br><br>
                    <button @click="displayCreateModal = true" class="button">Create Trainer <i class="fas fa-plus"></i></button>
                </div>
            </section>
        </section>

        <div v-if="displayCompletedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Successfully Updated Trainer</h2>

                <p>Trainer successfully updated</p>

                <div class="modal-alert__buttons">
                    <button class="button button--green" @click="displayCompletedModal = false">Okay!</button>
                </div>
            </div>
        </div>

        <create-trainer-modal v-on:cancel="displayCreateModal = false" v-on:complete="displayCreateModal = false" :class="displayCreateModal ? 'modal modal--active' : 'modal'" />
        <edit-trainer-modal v-on:cancel="displayEditModal = false" :user="user" v-on:complete="displayEditModal = false; displayCompletedModal = true; loadTrainers(pagination.current_page)" :class="displayEditModal ? 'modal modal--active' : 'modal'" />
    </div>
</template>

<script>
import axios from 'axios';
import CreateTrainerModal from './CreateTrainerModal';
import EditTrainerModal from './EditTrainerModal';
import FilterableDataTable from '../layout/FilterableDataTable.vue';
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    components: {
        CreateTrainerModal,
        EditTrainerModal,
        FilterableDataTable,
        ProfilePicture
    },

    data() {
        return {
            loading: true,
            displayCreateModal: false,
            displayEditModal: false,
            displayCompletedModal: false,
            trainers: [],
            user: {
                trainer: {
                    biography: "asdasda",
                    qualifications: []
                }
            },
            pagination: {},
            trainersListHeadings: {
                image: '',
                name: 'Name',
                email: 'Email',
                phone_number: 'Phone Number',
                created_at: 'Registered',
                actions: ''
            }
        }
    },

    mounted() {
        this.loadTrainers();
    },

    methods: {
        editTrainer (user) {
            this.displayEditModal = true;
            this.user = user;
        },

        scrollToTop() {
            document.getElementById('app').scrollIntoView();
        },

        loadTrainers(page) {
            console.log('Loading Trainers...');

            if (typeof page === 'undefined') {
                page = 1;
            }

            axios.get('/api/admin/trainers?page=' + page).then(response => {
                this.trainers = response.data.data;
                this.pagination = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        change_page(type) {
            this.loading = true;

            if (type === 'previous') {
                this.loadTrainers((this.pagination.current_page - 1));
            } else {
                this.loadTrainers((this.pagination.current_page + 1));
            }

            this.scrollToTop();
        }
    }
}
</script>
