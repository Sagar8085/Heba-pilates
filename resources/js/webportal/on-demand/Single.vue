<template>
    <div>
        <section class="wrapper page-content" v-if="authUser.subscription === null">
            <div class="row">
                <div class="columns eight-lg">
                    <section class="studio__section studio__section--bordered">
                        <h1>Upgrade To Access</h1>
                        <p>Get unlimited access to our On Demand Classes Library with any of our Heba Pilates Memberships.</p>
                    </section>

                    <section class="studio__section studio__section--bordered">
                        <div class="row row--center-v">
                            <div class="columns nine-md eight-lg">
                                <h3>How can I upgrade?</h3>
                                <p>We have 3 different memberships available to suit your fitness requirements. You can upgrade at anytime from within your account settings.</p>
                            </div>
                            <div class="columns three-md four-lg text-right">
                                <router-link class="button" to="/membership">View Memberships</router-link>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
        
        <div v-else-if="loadingVideo || !video.id" class="fullscreen-video">
            <loading-spinner
                :loading="loadingVideo"
                loadingText="video"
                :no-data="!video.id">
                <template slot="no-data">
                    Video not found.

                    <router-link class="button" to="/on-demand">View all On Demand</router-link>
                </template>
            </loading-spinner>
        </div>

        <fullscreen-video
            v-else
            :loading="loadingVideo"
            :video="this.video"
            :ondemand="this.video"
            :poster="this.video.image"
            :autoplay="true"
            @back="$router.push('/on-demand')" />
    </div>
</template>

<script>
import FullscreenVideo from '../../components/FullscreenVideo.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'
import axios from 'axios';

export default {
    components: { FullscreenVideo, LoadingSpinner },

    props: { authUser: Object },

    data () {
        return {
            loadingVideo: true,
            video: {
                playback_history: null
            }
        };
    },

    methods: {
        getVideo() {
            axios.get('/api/ondemand/video/' + this.$route.params.video_id)
                .then(response => {
                    this.video = response.data;
                    console.log('loaded single on demand video object')
                    console.log(this.video)
                    this.loadingVideo = false;
                })
                .catch(error => console.error(error))
                .finally(() => { this.loadingVideo = false })
        }
    },
    mounted(){
        this.getVideo();
    }
};
</script>
