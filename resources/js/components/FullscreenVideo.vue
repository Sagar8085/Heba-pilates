<template>
    <div class="fullscreen-video">
        <loading-spinner
            :loading="loading"
            loadingText="video"
            :no-data="!video.id"
            noDataText="Video not found."
        />
        <template v-if="!loading && video.id">
            <video-player
                class="vjs-custom-skin"
                ref="videoPlayer"
                :options="playerOptions"
                :currentTime="20"
                @ready="onPlayerReadied"
                @timeupdate="onTimeupdate"
                @play="prePlay"
                @ended="showAutomaticClose"
            >
            </video-player>

            <div
                v-if="wantsAutomaticClose"
                class="fullscreen-video__complete_banner"
            >
                <div class="fullscreen-video__complete_banner__content">
                    <h1>Session complete!</h1>
                    <div>
                        Well done, we hope you're feeling great and that you have enjoyed today's Heba workout.
                    </div>
                    <div class="fullscreen-video__complete_banner__content__controls">
                        <favourite-button
                            :video="video"
                            favourited-label="Add this session to your favourite list"
                            unfavourited-label="Remove this session to your favourite list"
                        />

                        <button
                            type="button"
                            class="video-list__video__footer__item button button--plain button--transparent button--grey"
                            @click="back"
                        >
                            <i class="material-icons">home</i> Return home to your session library
                        </button>
                    </div>
                    <div>
                        Your session will log out in {{ seconds }} seconds!
                    </div>
                </div>
            </div>

            <Transition>
                <div
                    v-if="!isPlaying && canShowBanner"
                    class="fullscreen-video--banner"
                >
                    {{ video.category.name }}: {{ video.name }}
                </div>
            </Transition>

            <div class="audio-button-group">
                <div
                    v-if="!showOptions"
                    class="audio-button"
                    @click="showOptions = !showOptions"
                >
                    <i class="fas fa-chevron-circle-right"></i>
                </div>
                <template v-if="showOptions">
                    <div class="audio-button audio-button--playpause" @click="togglePlay">
                        <i v-if="!isPlaying" class="fas fa-play"></i>
                        <i v-else class="fas fa-pause"></i>
                    </div>
                    <div class="audio-button audio-button--backward" @click="skipBackward()">
                        <i class="fas fa-fast-backward"></i>
                    </div>
                    <div class="audio-button audio-button--forward" @click="skipForward()">
                        <i class="fas fa-fast-forward"></i>
                    </div>
                    <div class="audio-button audio-button--down" @click="volumeDown()">
                        <i class="fas fa-volume-down"></i>
                    </div>
                    <div :class="{'audio-button': true, 'audio-button--up': true}" @click="volumeUp()">
                        <i class="fas fa-volume-up"></i>
                    </div>
                    <div class="audio-button audio-button--playpause" @click="back">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="audio-button audio-button--close" @click="showOptions = !showOptions">
                        <i class="fas fa-chevron-circle-left"></i>
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
import LoadingSpinner from '../webportal/layout/LoadingSpinner.vue';
import axios from 'axios';
import FavouriteButton from "../tablet/components/FavouriteButton";
import EndsTabletSession from '../mixins/endsTabletSession';

export default {
    mixins: [EndsTabletSession],

    components: {FavouriteButton, LoadingSpinner},

    props: {
        poster: String,
        video: Object,
        backText: {
            default: 'Back',
            type: String
        },
        autoplay: Boolean,
        loading: Boolean,
        ondemand: Object
    },

    methods: {

        showAutomaticClose() {
            this.wantsAutomaticClose = this.count > 5;
        },

        back() {
            this.$listeners.back
                ? this.$emit('back')
                : this.$router.go(-1);
        },

        onPlayerReadied() {
            if (!this.initialized) {
                this.initialized = true
                this.currentTech = this.player.techName_
                this.player.volume(0.7);
                this.currentVol = (this.player.volume() * 100);

                if (this.ondemand.playback_history !== null) {
                    this.player.currentTime(this.ondemand.playback_history.time_seconds);
                }
            }
        },
        onTimeupdate(e) {
            //TODO: Decide why this is needed
        },
        enterStream() {
            this.playerOptions.sources[1].src = this.streams.hls
            this.playerOptions.sources[0].src = this.streams.rtmp
            this.playerOptions.autoplay = true
        },
        changeTech() {
            if (this.currentTech === 'Html5') {
                this.playerOptions.techOrder = ['html5']
            } else if (this.currentTech === 'Flash') {
                this.playerOptions.techOrder = ['flash']
            }
            this.playerOptions.autoplay = true
        },

        skipForward() {
            this.player.currentTime((this.player.currentTime() + 15));
        },

        skipBackward() {
            this.player.currentTime((this.player.currentTime() - 15));

        },

        togglePlay() {
            if (this.player.paused()) {
                this.play()
            } else {
                this.pause()
            }
        },

        prePlay() {
            this.canShowBanner = true;
            this.play();
        },

        play() {
            this.player.play();
            this.isPlaying = true;
        },

        pause() {
            this.player.pause();
            this.isPlaying = false;
        },

        volumeDown() {
            var current = this.player.volume();
            var newvol = (current - 0.1)
            this.player.volume(newvol);
            this.currentVol = Math.round(this.player.volume() * 100);
        },

        volumeUp() {
            var current = this.player.volume();

            if (current >= 1) {
                return false;
            }

            var newvol = (current + 0.1)
            this.player.volume(newvol);
            this.currentVol = Math.round(this.player.volume() * 100);
        },
    },
    computed: {
        player() {
            return this.$refs.videoPlayer.player
        },
        currentStream() {
            return this.currentTech === 'Flash' ? 'RTMP' : 'HLS'
        },
    },
    beforeDestroy() {
        clearInterval(this.progressUpdater);
        document.body.classList.remove('fullscreen-video-open');
    },
    mounted() {
        var _this = this;
        this.progressUpdater = setInterval(function () {
            var currentTime = Math.round(_this.player.currentTime());
            axios.patch('/api/ondemand/video/' + _this.ondemand.id + '/progress', {
                progress: currentTime,
                video_length: _this.player.duration()
            });
        }, 10000);
        document.body.classList.add('fullscreen-video-open');

        setInterval((item) => {
            this.count++;

            if (!this.wantsAutomaticClose) {
                return;
            }

            if (this.seconds <= 0 ) {
                this.endTabletSession();
                return;
            };

            this.seconds--;
        }, 1000);

    },
    data() {
        return {
            count: 0,
            wantsAutomaticClose: false,
            seconds: 30,
            canShowBanner: false,
            showOptions: false,
            currentVol: 70,
            isPlaying: false,
            progressUpdater: null,
            initialized: false,
            currentTech: '',
            streams: {
                hls: ''
            },
            playerOptions: {
                overNative: true,
                autoplay: true,
                controls: true,
                techOrder: ['flash', 'html5'],
                sourceOrder: true,
                flash: {
                    hls: {withCredentials: false},
                },
                html5: {hls: {withCredentials: false}},
                sources: [
                    {
                        withCredentials: false,
                        type: 'application/x-mpegURL',
                        src: this.ondemand.video
                    }
                ],
                poster: this.poster
                // controlBar: {
                //   timeDivider: false, // 时间分割线
                //   durationDisplay: false, // 总时间
                //   progressControl: true, // 进度条
                //   customControlSpacer: true, // 未知
                //   fullscreenToggle: true // 全屏
                // },
            }
        }
    }
}
</script>


<style scoped>
.video-player {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.liveView {
    position: relative;
}

.selectWrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    line-height: 30px;
    margin: 20px;
    vertical-align: baseline;
}

.inputWrapper {
    width: 500px;
    margin: 0 auto;
}

.buttonWrapper {
    text-align: center;
}
</style>
