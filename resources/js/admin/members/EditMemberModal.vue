<template>
    <admin-modal v-if="show" v-model="show" :title="'Edit ' + formData.name">
        <div class="row row--form">
            <div class="six-sm columns">
                <admin-input v-model="first_name" label="First Name" required />
            </div>
            <div class="six-sm columns">
                <admin-input v-model="last_name" label="Last Name" required />
            </div>
        </div>
        <div class="row row--form">
            <div class="six-sm columns">
                <admin-input v-model="email" label="Email Address" type="email" required />
            </div>
            <div class="six-sm columns">
                <admin-input v-model="phone_number" label="Phone Number" />
            </div>
        </div>
        <div class="row row--form">
            <div class="six-sm columns">
                <label>Home Studio</label>
                <select v-model="home_studio_id">
                    <option value="" selected disabled>-- Select --</option>
                    <option :value="gym.id" v-for="gym in gyms">{{ gym.name }}</option>
                </select>
            </div>
            <div class="six-sm columns">
                <admin-input v-model="new_password" label="New Password (leave blank to not update)" type="text" />
            </div>
        </div>
        <template slot="footer">
            <button class="button" :disabled="!hasChanges || formInvalid" @click="editMember">Save</button>
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
    watch: {
        formData (formData) {
            this.first_name = formData.first_name || '';
            this.last_name = formData.last_name || '';
            this.email = formData.email || '';
            this.phone_number = formData.phone_number || '';
            this.avatar = formData.avatar || '';
        }
    },
    data () {
        return {
            first_name: this.formData.first_name || '',
            last_name: this.formData.last_name || '',
            email: this.formData.email || '',
            phone_number: this.formData.phone_number || '',
            avatar: this.formData.avatar || '',
            home_studio_id: this.formData?.member_profile?.home_studio_id || '',
            new_password: '',
            newAvatar: {},
            errors: {},
            gyms: [],
        }
    },
    mounted() {
        this.loadGyms();
    },
    computed: {
        show: {
            get () { return this.value },
            set (value) { return this.$emit('input', value) }
        },
        hasChanges () {
            return this.first_name !== this.formData.first_name
                || this.last_name !== this.formData.last_name
                || this.email !== this.formData.email
                || this.phone_number !== this.formData.phone_number
                || this.newAvatar;
        },
        formInvalid () {
            return this.first_name.length == 0
                || this.last_name.length == 0
                || this.email.length == 0;
        }
    },
    methods: {
        editMember () {
            if (!this.hasChanges) return;
            axios.post('/api/admin/members/' + this.$route.params.id, {
                first_name: this.first_name,
                last_name: this.last_name,
                email: this.email,
                phone_number: this.phone_number,
                new_password: this.new_password,
                home_studio_id: this.home_studio_id
            }).then(response => {
                alert('Member Updated');
                this.$emit('updated')
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error);
            })
            .finally(() => this.loading = false);
        },

        loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                this.gyms = response.data;
            })
            .catch(error => {
                console.log('Unable to load gyms at this time.')
            });
        }
    }
}
</script>
