<template>
    <button
        class="video-list__video__footer__item button button--plain button--transparent button--grey"
        @click="favourite"
    >
        <template v-if="favourited">
            <i class="material-icons">favorite</i>{{ unfavouritedLabel }}
        </template>

        <template v-else>
            <i class="material-icons">favorite_border</i>{{ favouritedLabel }}
        </template>
    </button>
</template>

<script>

import eventBus from "../../eventBus";

export default {
    props: {
        video: Object,
        favouritedLabel: {
            type: String,
            default: 'Unfavourite'
        },
        unfavouritedLabel: {
            type: String,
            default: 'Favourite'
        },
    },

    data() {
        return {
            favourited: this.video.favourited
        }
    },

    created() {
        eventBus.$on('favourited-' + this.video.id, item => {
            this.favourited = item.favourited;
        });
    },

    methods: {
        favourite() {
            axios.post('/api/ondemand/' + this.video.id + '/toggle-favorite')
                .then(({data}) => {
                    this.favourited = data.favourited;
                    eventBus.$emit('favourited-' + this.video.id, {
                        favourited: data.favourited
                    });
                });

        },
    }
};
</script>
