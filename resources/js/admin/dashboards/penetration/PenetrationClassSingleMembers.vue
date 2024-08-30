<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon icon--dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        Classes | {{ this.class.name }}
                    </h1>

                    <h2 class="page-header__sub">Single Class Member Penetration List</h2>
                </div>
            </div>
        </section>

        <section class="page-content">
            <div class="results-table">
                <div class="results-table__sidebar">

                    <div class="results-table__filter">
                        <h2>Age Bracket</h2>

                        <div @click="toggleAge('16-25')">
                            <label>16-25</label>
                            <div>
                                <div :class="this.ages.includes('16-25') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>

                        <div @click="toggleAge('26-40')">
                            <label>26-40</label>
                            <div>
                                <div :class="this.ages.includes('26-40') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>

                        <div @click="toggleAge('41-55')">
                            <label>41-55</label>
                            <div>
                                <div :class="this.ages.includes('41-55') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>

                        <div @click="toggleAge('56+')">
                            <label>56+</label>
                            <div>
                                <div :class="this.ages.includes('56+') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="results-table__filter">
                        <h2>Gender</h2>

                        <div @click="toggleGender('male')">
                            <label>Male</label>
                            <div>
                                <div :class="this.genders.includes('male') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>

                        <div @click="toggleGender('female')">
                            <label>Female</label>
                            <div>
                                <div :class="this.genders.includes('female') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>

                        <div @click="toggleGender('other')">
                            <label>Other</label>
                            <div>
                                <div :class="this.genders.includes('other') ? 'results-table__filter-icon results-table__filter-icon--checked' : 'results-table__filter-icon'">
                                    <i class="fas fa-check"></i>
                                    <i class="fas fa-times fa-times--red"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="results-table__filter">
                        <button class="button" @click="loadMembers()">Apply Filter</button>
                        <button class="button button--outline" @click="loadMembers(true)" style="margin-top: 1rem">Download</button>
                    </div>
                </div>

                <div class="results-table__content">
                    <filterable-data-table
                        :cols="sessionListHeadings"
                        :rows="sessions"
                        :pagination="pagination"
                        :filterOptions="sessionFilters"
                        title="Members"
                        v-on:nextPage="loadMembers(false, (pagination.current_page + 1))"
                        v-on:previousPage="loadMembers(false, (pagination.current_page - 1))">

                        <template v-slot:cell-member="{ item, cell }">
                            <router-link :to="'/admin/members/' + item.id">{{ item.name }}</router-link>
                        </template>

                        <template v-slot:cell-email="{ item, cell }">
                            {{ item.email }}
                        </template>

                        <template v-slot:cell-phoneNumber="{ item, cell }">
                            {{ item.phone_number }}
                        </template>

                        <template v-slot:cell-gender="{ item, cell }">
                            {{ item.gender }}
                        </template>

                        <template v-slot:cell-age="{ item, cell }">
                            {{ item.age }}
                        </template>

                        <template v-slot:cell-actions="{ item }">
                            <router-link class="button button--icon button--transparent" :to="'/admin/sessions/' + item.id">
                                <i class="fas fa-arrow-right"></i>
                            </router-link>
                        </template>

                    </filterable-data-table>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';
import FilterableDataTable from '../../layout/FilterableDataTable.vue'

export default {
    components: { FilterableDataTable },

    data() {
        return {
            class: {},
            category: {},

            loading: true,
            sessions: [],
            pagination: {},

            sessionListHeadings: {
                member: 'Member',
                email: 'Email Address',
                phoneNumber: 'Phone Number',
                gender: 'Gender',
                age: 'Age'
            },
            sessionFilters: [
                { option: 'Recently registered', value: 'recent' }
            ],

            ages: ['16-25', '26-40', '41-55', '56+'],
            genders: ['male', 'female', 'other']
        }
    },

    mounted() {
        this.loadWorkout();
        this.loadWorkoutCategory();
        this.loadMembers();
    },

    methods: {
        /*
         * Load workout.
         * @param {none}
         */
        loadWorkout() {
            axios.get('/api/admin/on-demand/library/' + this.$route.params.class_id).then(response => {
                this.class = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        /*
         * Load workout category.
         * @param {none}
         */
        loadWorkoutCategory() {
            axios.get('/api/admin/workout/category/' + this.$route.params.category_id).then(response => {
                this.category = response.data;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        loadMembers(download = false, page = 1) {
            axios.post('/api/admin/dashboard/penetration/members?type=class&id=' + this.$route.params.class_id + '&page=' + page + '&download=' + download, {
                ages: this.ages,
                genders: this.genders
            }).then(response => {
                if (download) {
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'Member Download.csv');

                    document.body.appendChild(fileLink);

                    fileLink.click();
                    this.downloading = false;
                } else {
                    this.sessions = response.data.data;
                    this.pagination = response.data;
                }
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        toggleGender(gender) {
            if (this.genders.includes(gender)) {
                var index = this.genders.indexOf(gender);
                this.genders.splice(index, 1);
            } else {
                this.genders.push(gender);
                console.log('adding: ' + gender)
            }
        },

        toggleAge(age) {
            if (this.ages.includes(age)) {
                var index = this.ages.indexOf(age);
                this.ages.splice(index, 1);
            } else {
                this.ages.push(age);
                console.log('adding: ' + age)
            }
        }
    }
}
</script>
