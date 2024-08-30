<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Upgrade existing member / trainer to admin</h3>
            </div>

            <div class="modal__body">
                <div class="row">
                        <div class="eight columns">
                            <div class="form-element">
                                <div class="form-element__control" style="margin-top: 0;">
                                    <input type="text" v-model="memberSearch" placeholder="Enter member name / email">
                                </div>
                            </div>
                        </div>

                        <div class="four columns">
                            <button class="button" @click="searchMember()">Search</button>
                        </div>
                    </div>

                    <div class="form-element" v-if="members.length > 0">
                        <hr style="margin: 0 0 2.5rem; width: 100%;">
                        <span class="form-element__label">Select a Member</span>

                        <div class="form-element__control">
                            <select v-model="member">
                                <option value="null" disabled selected>-- Select a Member --</option>
                                <option :value="user" v-for="(user, index) in members">{{ '#' + user.id + ' - ' + user.name + ' - ' + user.email }}</option>
                            </select>
                        </div>
                    </div>

                
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="$emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Save</button>
                <button class="button" v-else>{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Admin Created!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">An invitation has been emailed to <strong>{{this.email}}</strong> to confirm their account.</p>
            </div>


            <div class="modal__footer">
                <button type="button" class="button" @click="$emit('complete')">Okay!</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],

            memberSearch: '',
            memberName: '',

            members: [],
            member: null,

            displayProcessingModal: false
        }
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/member-upgrade', {
                id: this.member.id,
            })
            .then(response => {
               this.finish();
            })
            .catch(error => {
                this.uploading = false;
                this.errors = error.response.data.errors;
            });
        },

        finish() {
            this.uploading = false;
            this.displayUploadModal = false;
            this.image = [];
            this.name = '';
            this.description = '';
            this.displayProcessingModal = true;
        },

        openImageFileBrowser() {
            this.$refs.image.click();
        },

        handleImageUpload() {
            this.image = this.$refs.image.files;
            console.log(this.image);
        },
        searchMember() {
            this.member = null;
            this.creditType = '';
            this.creditPacks = [];

            axios.post('/api/admin/member-search', {
                keyword: this.memberSearch
            }).then(response => {
                this.members = response.data;
            });
        },
    }
}
</script>
