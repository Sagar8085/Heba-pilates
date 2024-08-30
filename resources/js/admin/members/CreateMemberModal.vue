<template>
    <admin-modal v-if="show" v-model="show" title="Create Member">
        <div class="row row--form">
            <div class="six-sm columns">
                <admin-input v-model="first_name" label="First Name" required :error="this.errors['first_name'] ? this.errors['first_name'] : ''" />
            </div>
            <div class="six-sm columns">
                <admin-input v-model="last_name" label="Last Name" required :error="this.errors['last_name'] ? this.errors['last_name'] : ''" />
            </div>
        </div>
        <div class="row row--form">
            <div class="six-sm columns">
                <admin-input v-model="email" label="Email Address" type="email" required :error="this.errors['email'] ? this.errors['email'] : ''" />
            </div>
            <div class="six-sm columns">
                <admin-input v-model="phone_number" label="Phone Number" />
            </div>
        </div>
         <div class="row row--form">
            <div class="twelve columns">

                 <admin-input v-model="send_email" type="select" label="Send Member Invitation Email" required>
                      <option value="yes">Yes</option>
                      <option value="no">No</option>
                </admin-input>
                <small>If this person is a new lead, you may not want to send them and invitation email automatically.</small>
            </div>
        </div>


        <div class="info" v-if="this.send_email == 'yes'">This member will be sent an invitation email, to complete their account setup.</div>

        <template slot="footer">
            <button class="button" :disabled="formInvalid" @click="createMember">Create</button>
        </template>
    </admin-modal>
</template>

<script>
import axios from 'axios';
import AdminInput from '../layout/AdminInput.vue'
import AdminModal from '../layout/AdminModal.vue'
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    props: {
        value: Boolean,
        formData: Object
    },

    components: { AdminModal, AdminInput, ProfilePicture },

    data () {
        return {
            first_name: '',
            last_name: '',
            email: '',
            phone_number: '',
            send_email: '',
            errors: {},
            show: true
        }
    },
    computed: {
        formInvalid () {
            return this.first_name.length == 0
                || this.last_name.length == 0
                || this.email.length == 0;
        }
    },
    methods: {
        createMember () {
            axios.post('/api/admin/members/new', {
                first_name: this.first_name,
                last_name: this.last_name,
                email: this.email,
                phone_number: this.phone_number,
                send_email: this.send_email,
            }).then(response => {
                alert('Member Create');
                this.$emit('created')
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
                this.errors = error.response.data.errors;
            })
            .finally(() => this.loading = false);
        }
    }
}
</script>
