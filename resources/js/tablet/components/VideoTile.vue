<template>
    <li class="video-list__video video-list__video--tablet video-list__video--live">

        <div class="video-list__video__image" @click="select">
            <img :src="live ? video.category.image : video.image" :alt="video.name"/>
        </div>

        <h4 class="video-list__video__title">
            {{ video.name }}
        </h4>

        <div v-if="live">
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

            <ul class="video-list__video__footer" v-if="video.equipment.length > 0">
                <li class="video-list__video__footer__item">
                    Equipment Needed:&nbsp;<span v-for="equipment in video.equipment">{{ equipment.name }},&nbsp;</span>
                </li>
            </ul>

            <ul class="video-list__video__footer" v-if="video.equipment.length == 0">
                <li class="video-list__video__footer__item">
                    No equipment is needed for this class.
                </li>
            </ul>
        </div>

        <div v-if="!live">
            <dl class="video-list__video__dl">
                <dt class="video-list__video__dl__title">{{ video.description }}</dt>
            </dl>

            <div class="video-list__video__footer">
                <favourite-button :video="video"/>
                <div v-if="video.watched">
                    <i class="material-icons">check_circle</i>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
import FavouriteButton from "./FavouriteButton";

export default {
    components: {
        FavouriteButton
    },
    props: {
        video: {
            type: Object,
            required: true
        },
        live: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        select() {
            this.$emit('clicked', this.video);
        }
    }
}
</script>

<style scoped>
.material-icons {
    color: green;
}
</style>