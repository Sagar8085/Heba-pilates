<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">Upgrade to Admin</h3>
            </div>

            <div class="modal__body">
                <p>Would you like to upgrade this member to admin?</p>
            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="$emit('cancel')">Cancel</button>
                <button class="button" @click="save()" v-if="!this.uploading">Yes Please!</button>
                <button class="button" v-else>{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Admin Created!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">This user was updated to an Admin!</p>
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
    props: {
        value: Boolean,
        formData: Object
    },
    
    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],
            id: this.formData.id,
            first_name: this.formData.first_name,

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
                id: this.$route.params.id,
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
