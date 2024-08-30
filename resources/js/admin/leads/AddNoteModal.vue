<template>
    <div>
        <div class="modal__box" v-if="!this.displayProcessingModal">
            <div class="modal__header">
                <h3 class="modal__title">{{ this.edit ? 'Add Note' : 'View Note' }}</h3>
            </div>

            <div class="modal__body">
                <div class="row row--form">
                    <div class="twelve columns">
                        <div class="form-element">
                            <span class="form-element__label">
                                * Note
                                <span v-if="this.errors['note']">{{ this.errors['note'][0] }}</span>
                            </span>
                            <div class="form-element__control">
                                <textarea v-if="this.edit" class="lead__profile__notes" rows=10 placeholder="Add notes here" v-model="note"></textarea>

                                <p v-else>{{ this.content || 'Nothing to show...' }}</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal__footer">
                <button type="button" class="button button--outline" @click="$emit('cancel')">{{ this.edit ? 'Cancel' : 'Close' }}</button>
                <button class="button" @click="save()" v-if="!this.uploading && this.edit">Save</button>
                <button class="button" v-else-if="this.edit">{{this.uploadPercentage}}%&nbsp;<i class="fas fa-spinner fa-spin"></i></button>
            </div>
        </div>

        <div class="modal__box" v-else>
            <div class="modal__header">
                <h3 class="modal__title">Note Created!</h3>
            </div>

            <div class="modal__body">
                <p class="modal__text">The note was successfully created and saved against this Lead.</p>
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
        edit: Boolean,
        note_id: Number,
        lead_id: Number
    },

    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],

            note: null,
            content: null,

            displayProcessingModal: false
        }
    },

    mounted() {
        if (this.note_id) {
            this.fetchFromId();
        }
    },

    watch: {
        note_id: function(val) {
            this.fetchFromId();
        }
    },

    methods: {
        save() {
            axios.post('/api/admin/leads/' + this.lead_id + '/notes', {
                note: this.note
            })
            .then(response => {
                this.finish();
            })
            .catch(error => {
                console.error(error);
            });
        },

        fetchFromId() {
            axios.get('/api/admin/leads/notes/' + this.note_id)
            .then(response => {
                this.content = response.data.note.content;
            })
            .catch(error => {
                console.error(error);
                this.errors = error.response.data.errors;
            });
        },

        finish() {
            Object.assign(this.$data, this.$options.data());
            this.displayProcessingModal = true;
        },
    }
}
</script>
