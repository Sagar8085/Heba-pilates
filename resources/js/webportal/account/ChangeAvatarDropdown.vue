<template>
    <menu-dropdown v-model="showMenu">
        <button class="account-info__button">Change avatar</button>

        <template slot="items">
            <li class="menu-dropdown__item">
                <button class="menu-dropdown__link" @click="openFileBrowser">
                    <i class="material-icons material-icons-outlined">folder</i> Browse Images

                    <input ref="customAvatar" id="customAvatar" type="file" class="hidden" accept="image/jpg,image/jpeg,image/png,image/webp" v-on:change="uploadCustomAvatar()" />
                </button>
            </li>
            <li class="menu-dropdown__item">
                <router-link to="/myaccount/avatar" class="menu-dropdown__link">
                    <i class="material-icons material-icons-outlined">account_circle</i> Choose Avatar
                </router-link>
            </li>
        </template>
    </menu-dropdown>
</template>

<script>
import axios from 'axios';
import { mixin as clickaway } from 'vue-clickaway';
import MenuDropdown from '../../components/MenuDropdown.vue';

export default {
    components: { MenuDropdown },

    mixins: [ clickaway ],

    data () {
        return {
            showMenu: false,
            image: []
        }
    },

    methods: {
        openFileBrowser () {
            this.$refs.customAvatar.click();
        },

        uploadCustomAvatar() {
            let formData = new FormData();
            formData.append('files[0]', this.$refs.customAvatar.files[0]);

            /*
             * Make the request to the POST /multiple-files URL
             */
            axios.post('/api/user/custom-avatar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(response => {
                this.$emit('avatarUpdated', response.data.avatar)
            })
            .catch(function(error){
                console.log('FAILURE!!');
                console.log(error);
            });
        }
    }
}
</script>
