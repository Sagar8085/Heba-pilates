<template>
    <div class="account account--wide wrapper">
        <h1 class="account__title">Change Avatar</h1>

        <div class="account-info account-info--col account-info--center">
            <profile-picture size="xlarge" :image="profilePicturePreview" :firstName="authUser.first_name" :lastName="authUser.last_name" />
            <label class="account-info__label">Current Avatar</label>
        </div>

        <div class="account-info account-info--images">
            <profile-picture
                v-for="image in profileImages"
                :key="image"
                size="xlarge"
                :image="image"
                interactive
                @click="selectAvatar(image)" />
        </div>

        <div class="account-info account-info--inline account-info--center">
            <router-link to="/myaccount" class="button button--outline">Cancel</router-link>
            <button v-if="!loading" class="button" :disabled="selectedAvatar == null" @click="updateAvatar">Save</button>
            <button v-else class="button" disabled><i class="fas fa-spinner fa-spin"></i></button>
        </div>
    </div>
</template>

<script>
import ProfilePicture from '../../components/ProfilePicture.vue'

export default {
    components: { ProfilePicture },
    props: { authUser: Object },
    data () {
        return {
            profileImages: [
                '/images/avatars/avatar-1.png',
                '/images/avatars/avatar-2.png',
                '/images/avatars/avatar-3.png',
                '/images/avatars/avatar-4.png',
                '/images/avatars/avatar-5.png',
                '/images/avatars/avatar-6.png',
                '/images/avatars/avatar-7.png',
                '/images/avatars/avatar-8.png'
            ],
            selectedAvatar: null,
            loading: false
        }
    },
    computed: {
        profilePicturePreview () {
            return this.selectedAvatar || this.authUser.avatar;
        }
    },
    methods: {
        selectAvatar (image) {
            this.selectedAvatar = image;
        },
        updateAvatar () {
            if (this.selectedAvatar == null) return;

            this.loading = true;

            // Call API
            axios.post('/api/user/placeholder-avatar', {
                avatar_path: this.selectedAvatar
            }).then(response => {
                console.log('done')
                console.log(response.data)
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.loading = false;
                this.$router.push('/myaccount')
            })
        }
    }
}
</script>
