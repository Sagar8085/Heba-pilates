<template>
<section class="video-list">
    <header v-if="title" class="video-list__header wrapper">
        <h3 class="video-list__header__title">{{ title }}</h3>
    </header>
    <div :class="{ 'video-list__container': true, 'video-list__container--vertical': verticalScroll }">
        <div :class="{ wrapper: !verticalScroll, 'video-list__loading': true }">
            <loading-spinner
                :loading="loading"
                loadingText="videos"
                :noData="videos.length == 0" />
        </div>

        <ul v-if="!loading && videos.length > 0" class="video-list__videos">
            <li
                v-for="(video, index) in videos"
                :key="video.id"
                :class="{ 'video-list__video': true, 'video-list__video--live': isLive }">

                <div class="video-list__video__image" @click="selectVideo(video)">
                    <img :src="isLive ? video.category.image : video.image" :alt="title" />
                </div>


                <h4 class="video-list__video__title">
                    {{ isLive ? video.category.name : video.name }}
                </h4>

                <!-- <p class="video-list__video__author">
                    <img v-if="!isLive" class="video-list__video__author__image" :src="video.author.avatar" />
                    {{ isLive ? video.category.name : video.author.name }}
                </p> -->

                <div v-if="isLive">
                    <ul class="video-list__video__footer">
                        <li class="video-list__video__footer__item">
                            {{ video.date_human }}
                        </li>
                        <li class="video-list__video__footer__item">
                            {{ video.time_human }}
                        </li>
                        <li class="video-list__video__footer__item">
                            {{ video.duration }} mins
                        </li>
                    </ul>

                    <ul class="video-list__video__footer" v-if="video.equipment && video.equipment.length > 0">
                        <li class="video-list__video__footer__item">
                            Equipment Needed:&nbsp;{{ video.equipment.map(x => x.name).join(', ') }}
                        </li>
                    </ul>

                    <ul class="video-list__video__footer" v-if="video.equipment.length == 0">
                        <li class="video-list__video__footer__item">
                            No equipment is needed for this class.
                        </li>
                    </ul>

                    <ul class="video-list__video__footer">
                        <button
                            v-if="isLive && favourites"
                            class="video-list__video__footer__item button button--plain button--transparent button--grey"
                            @click="toggleLiveClassFavourite(video, index)">
                            <template v-if="favourites.includes(video.id)">
                                <i class="material-icons">favorite</i>Unfavourite
                            </template>

                            <template v-else>
                                <i class="material-icons">favorite_border</i>Favourite
                            </template>
                        </button>

                        <button
                            class="video-list__video__footer__item button button--plain button--transparent button--grey"
                            @click="selectVideo(video)">
                            <i class="material-icons">info</i>Class Info
                        </button>
                    </ul>
                </div>

                <div v-if="!isLive">
                    <dl class="video-list__video__dl">
                        <dt class="video-list__video__dl__title">{{ video.description }}</dt>
                    </dl>

                    <ul class="video-list__video__footer">
                        <button
                            v-if="favourites"
                            class="video-list__video__footer__item button button--plain button--transparent button--grey"
                            @click="toggleOnDemandFavourite(video, index)">
                            <template v-if="favourites.includes(video.id)">
                                <i class="material-icons">favorite</i>Unfavourite
                            </template>

                            <template v-else>
                                <i class="material-icons">favorite_border</i>Favourite
                            </template>
                        </button>

                        <button
                            class="video-list__video__footer__item button button--plain button--transparent button--grey"
                            @click="openClassInfo(video)">
                            <i class="material-icons">info</i>Class Info
                        </button>
                    </ul>

                    <!-- <ul class="video-list__video__footer">
                        <li class="video-list__video__footer__item">
                            <i class="material-icons">schedule</i>
                            {{ video.duration }} mins
                        </li>
                        <li class="video-list__video__footer__item" v-if="!isLive">
                            <i class="material-icons">fitness_center</i>
                            Body Weight
                        </li>
                        <li class="video-list__video__footer__item" v-if="!isLive">
                            <i class="material-icons">network_cell</i>
                            Beginner
                        </li>
                    </ul> -->
                </div>
            </li>
        </ul>
    </div>

    <footer class="video-list__footer wrapper">
        <router-link v-if="showAllLink" :to="showAllLink" class="button button--transparent button--plain">Show All</router-link>
    </footer>

    <section class="class-info-modal" v-if="displayViewClassModal && selectedClass">
        <div class="class-info-modal__box">
            <i class="class-info-modal__close far fa-times-circle" @click="closeInfoModal()"></i>

            <router-link :to="'/on-demand/video/' + selectedClass.id" class="class-info-modal__image-wrap">
                <img class="class-info-modal__image" :src="selectedClass.image">
                <i class="material-icons">play_circle_outline</i>
            </router-link>

            <h2 class="class-info-modal__title">{{ selectedClass.name }}</h2>

            <!-- <p class="class-info-modal__info">{{ category.name }}</p> -->
            <p class="class-info-modal__info"><strong>Duration:</strong> {{ selectedClass.duration }} minutes</p>
            <p class="class-info-modal__info">
                <button
                    class="button button--plain button--transparent button--grey button--with-icon"
                    @click="toggleOnDemandFavourite(selectedClass, 0)">
                    <template v-if="favourites.includes(selectedClass.id)">
                        <i class="material-icons">favorite</i>Unfavourite
                    </template>

                    <template v-else>
                        <i class="material-icons">favorite_border</i>Favourite
                    </template>
                </button>
            </p>

            <div class="class-info-modal__divider"></div>

            <p v-if="selectedClass.instructor" class="class-info-modal__instructor">
                <img :src="selectedClass.instructor.avatar">
                <span>
                    <span style="display: block">{{ selectedClass.instructor.name }}</span>
                    <button
                        style="margin-left: 0; padding-left: 0; border-left: none; margin-left: -2px; font-size: 12px;"
                        :class="{ 'button button--icon button--plain button--transparent': true, 'button--grey': !favourites.includes(selectedClass.id) }"
                        @click="$router.push('/instructors/' + selectedClass.instructor_id)">
                        <i class="material-icons" style="font-size: 16px;">info</i>&nbsp;&nbsp;View Instructor Profile
                    </button>
                </span>
            </p>

            <p class="class-info-modal__info"><strong>Class Description:</strong> {{ selectedClass.description }}</p>
        </div>
    </section>

    <!-- <div v-if="displayViewClassModal" :class="{ modal: true, 'modal--active': displayViewClassModal }">
        <div class="modal__box modal__box--small modal__body">
            <h2 class="modal__title">{{ this.selectedClass.category.name }}</h2>

            <p>This live event will take place at {{ this.selectedClass.datetime_human }}, book your slot by hitting 'Book'</p>

            <div class="modal__buttons">
                <button class="button button--outline" @click="displayViewClassModal = false">Cancel</button>
                <button class="button button--with-icon button--green" @click="book()" v-if="!booking">
                    Book
                </button>
                <button class="button button--with-icon button--green" v-else>
                    <i class="fas fa-spinner fa-spin"></i>One Moment
                </button>
            </div>
        </div>
    </div> -->
</section>
</template>

<script>
import axios from 'axios';
import LoadingSpinner from './LoadingSpinner.vue';

export default {
    components: { LoadingSpinner },

    props: {
        title: String,
        showAllLink: String,
        videos: Array,
        loading: Boolean,
        isLive: Boolean,
        verticalScroll: Boolean,
        favourites: Array,
    },

    data() {
        return {
            displayViewClassModal: false,
            purchasing: false,
            booking: false,
            selectedClass: null
        }
    },

    mounted() {
    },

    methods: {
        selectVideo (video) {
            if (this.isLive) {
                console.log('live class!');
                // this.selectedClass = video;
                // this.displayViewClassModal = true;
                // location.href = '/live/' + video.id + '/stream';
                this.$router.push({ name: 'LiveClassSingle', params: { id: video.id } })
            } else {
                this.$router.push({ name: 'OnDemandSingle', params: { video_id: video.id } })
            }
        },

        toggleFavourite (video, index) {
            this.$set(this.videos[index], 'favourite', !this.videos[index].favourite);
            // Call API
        },

        toggleOnDemandFavourite(video, index) {
            this.$set(this.videos[index], 'favourite', !this.videos[index].favourite);
            axios.post('/api/ondemand/' + video.id + '/toggle-favorite').then(response => {
                if (this.favourites.includes(video.id)) {
                    var videoIndex = this.favourites.indexOf(video.id);
                    var newFavourites = this.favourites.splice(videoIndex, 1);
                } else {
                    this.favourites.push(video.id);
                }
            });
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
        }
    }
}
</script>
