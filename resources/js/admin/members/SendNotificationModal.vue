<template>
    <admin-modal v-if="!sent" v-model="show" title="Send Push Notification">
        <admin-input
            v-model="title"
            :label="`Title (${title.length}/${maxTitleCharacters})`"
            type="text"
            :maxlength="maxTitleCharacters"
            required />

        <admin-input
            v-model="message"
            :label="`Message (${message.length}/${maxCharacters})`"
            type="textarea"
            :maxlength="maxCharacters"
            required />

        <template slot="footer">
            <button class="button" :disabled="formInvalid" @click="sendNotification">Send</button>
        </template>
    </admin-modal>

    <div v-else class="modal-alert modal-alert--active">
        <div class="modal-alert__box modal-alert__box--small modal-alert__body">
            <h2 class="modal-alert__title">Notification Sent</h2>

            <p>Your notification has been sent to the users device, they will receive it shortly.</p>

            <div class="modal-alert__buttons">
                <button class="button button--green" @click="$emit('complete')">Okay</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import AdminInput from '../layout/AdminInput.vue'
import AdminModal from '../layout/AdminModal.vue'

export default {
    props: {
        member: Object,
        value: Boolean,
        formData: Object
    },
    components: { AdminModal, AdminInput },
    data () {
        return {
            title: '',
            message: '',
            maxTitleCharacters: 35,
            maxCharacters: 100,
            sending: false,
            sent: false
        }
    },
    computed: {
        show: {
            get () { return this.value },
            set (value) { return this.$emit('input', value) }
        },
        formInvalid () {
            return (this.message.length == 0 || this.title.length === 0) || (this.message.length > this.maxCharacters || this.title.length > this.maxTitleCharacters) || this.sending;
        }
    },
    methods: {
        sendNotification () {
            if (this.formInvalid) return;

            this.sending = true;

            axios.post('/api/admin/members/' + this.member.id + '/push-notification', {
                title: this.title,
                message: this.message
            }).then(response => {
                this.sending = false;
                this.title = '';
                this.message = '';
                this.sent = true;
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
                this.sending = false;
            });
        }
    }
}
</script>
