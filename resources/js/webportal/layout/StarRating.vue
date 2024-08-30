<template>
    <div class="star-rating star-rating--static">
        <i v-for="(star, i) in starRating" 
            :key="i" 
            :class="star">star</i>
    </div>
</template>

<script>
export default {
    props: { rating: Number },
    computed: {
        starRating () {
            const rating = [];
            for (let i = 0; i < 5; i++) {
                const icon = i < this.rating
                    ? 'material-icons star star--filled' : 'material-icons star';
                rating.push(icon)
            }
            return rating;
        }
    }
}
</script>