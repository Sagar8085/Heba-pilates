<template>
    <section class="video-list">
        <header v-if="title" class="video-list__header">
            <h3 class="video-list__header__title">{{ title }}</h3>
        </header>

        <div v-if="scrollIndicators && canScrollLeft" class="video-list__arrow video-list__arrow--left">
            <i class="material-icons-outlined">
                chevron_left
            </i>
        </div>

        <div v-if="scrollIndicators && canScrollRight" class="video-list__arrow video-list__arrow--right">
            <i class="material-icons-outlined">
                chevron_right
            </i>
        </div>

        <div
            :id="'scroller' + this.scroller"
            :class="{ 'video-list__container': true, 'video-list__container--vertical': verticalScroll }"
            ref="scroller"
            @scroll="calculateScroll"
        >
            <div :class="{ wrapper: !verticalScroll, 'video-list__loading': true }">
                <loading-spinner
                    :loading="loading"
                    loadingText="videos"
                    :noData="videos.length == 0"
                />
            </div>

            <ul v-if="!loading && videos.length > 0" class="video-list__videos">
                <video-tile
                    v-for="video in videos"
                    :key="video.id"
                    :video="video"
                    :live="isLive"
                    v-on:clicked="selectVideo"
                />
            </ul>
        </div>

        <footer class="video-list__footer wrapper">
            <router-link v-if="showAllLink" :to="showAllLink" class="button button--transparent button--plain">Show
                All
            </router-link>
        </footer>

        <section class="class-info-modal" v-if="displayViewClassModal && selectedClass">
            <div class="class-info-modal__box">
                <i class="class-info-modal__close far fa-times-circle" @click="closeInfoModal()"></i>
                <img class="class-info-modal__image" :src="selectedClass.image">
                <h2 class="class-info-modal__title">{{ selectedClass.name }}</h2>
                <p class="class-info-modal__info"><strong>Duration:</strong> {{ selectedClass.duration }} minutes</p>
                <div class="class-info-modal__divider"></div>
                <p class="class-info-modal__info">
                    <strong>Equipment:</strong>
                    <span v-if="selectedClass.equipment.length > 0">
                        {{ selectedClass.equipment.map(x => x.name).join(', ') }}
                    </span>
                    <span v-else>None Needed</span>
                </p>
                <div class="class-info-modal__divider"></div>
                <p class="class-info-modal__info">
                    <strong>Class Description:</strong>{{ selectedClass.description }}
                </p>
            </div>
        </section>
    </section>
</template>

<script>
import axios from 'axios';
import LoadingSpinner from './LoadingSpinner.vue';
import VideoTile from "../components/VideoTile";

export default {
    components: {
        LoadingSpinner,
        VideoTile
    },

    props: {
        title: String,
        showAllLink: String,
        videos: Array,
        loading: Boolean,
        isLive: Boolean,
        verticalScroll: Boolean,
        // favourites: Array,
        scroller: String,
        scrollIndicators: Boolean,
    },

    data() {
        return {
            favourites: [],
            displayViewClassModal: false,
            purchasing: false,
            booking: false,
            selectedClass: null,
            canScrollLeft: false,
            canScrollRight: true,
        }
    },

    mounted() {
        this.loadFavourites();
        this.calculateScroll();
    },

    methods: {
        calculateScroll() {
            const scroller = this.$refs.scroller;
            this.canScrollRight = scroller.scrollWidth > scroller.clientWidth && (scroller.scrollLeft + scroller.clientWidth) < scroller.scrollWidth;
            this.canScrollLeft = scroller.scrollWidth > scroller.clientWidth && scroller.scrollLeft > 0;
        },

        loadFavourites() {
            axios.get('/api/ondemand/favourites').then(response => {
                this.favourites = response.data;
            }).catch(error => {
                console.log('ERROR', error);
                this.loading = false;
            });
        },

        favourited(index) {
            this.$set(this.videos[index], 'favourite', !this.videos[index].favourite);
        },

        selectVideo(video) {
            if (this.isLive) {
                console.log('live class!');
                location.href = '/live/' + video.id + '/stream?tablet=true';
            } else {
                this.$router.push({name: 'OnDemandSingle', params: {video_id: video.id}})
            }
        },

        toggleFavourite(video, index) {
            this.$set(this.videos[index], 'favourite', !this.videos[index].favourite);
            // Call API
        },

        toggleLiveClassFavourite(video, index) {
            this.$set(this.videos[index], 'favourite', !this.videos[index].favourite);
            axios.post('/api/live/class/' + video.id + '/toggle-favorite').then(response => {
                if (this.favourites.includes(video.id)) {
                    var videoIndex = this.favourites.indexOf(video.id);
                    var newFavourites = this.favourites.splice(videoIndex, 1);
                } else {
                    this.favourites.push(video.id);
                }
            });
        },

        /*
         * User wants to book a slot for this class.
         * @param {none}
         */
        book() {
            this.booking = true;

            console.log('Booking...')

            axios.post('/api/live/' + this.selectedClass.id + '/book').then(response => {
                console.log('Booking Response:');
                console.log(response)
            })
                .catch(error => console.error(error))
                .finally(() => this.booking = false)
        },

        openClassInfo(video) {
            this.selectedClass = video;
            this.displayViewClassModal = true;
            $('html, body').addClass('modal-active');
        },

        closeInfoModal() {
            this.selectedClass = null;
            this.displayViewClassModal = false;
            $('html, body').removeClass('modal-active');
        },

        scrollRight() {
            document.getElementById('scroller' + this.scroller).scrollLeft += 325;
        },
        scrollLeft() {
            document.getElementById('scroller' + this.scroller).scrollLeft -= 325;
        }
    }
}
</script>
