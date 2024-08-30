<template>
    <div>
        <fullscreen-video
            v-if="!loadingVideo"
            :loading="loadingVideo"
            :video="this.video"
            :ondemand="this.video"
            :poster="this.video.image"
            :autoplay="true"
            @back="$router.push('/tablet/on-demand')" />
    </div>
</template>

<script>
import FullscreenVideo from '../../components/FullscreenVideo.vue'
import axios from 'axios';

export default {
    components: { FullscreenVideo },

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
                    this.loadingVideo = false;
                })
                .catch(error => console.error(error));
        }
    },
    mounted(){
        this.getVideo();
    }
};
</script>
