<template>
    <div class="view-workout__exercise">
        <div class="view-workout__exercise__number-box">
            <span class="view-workout__exercise__number-box__number">{{number}}</span>
        </div>
        <img class="view-workout__exercise__image" :src="image" alt="workout image">
        <div class="view-workout__exercise__content">
            <h3 class="view-workout__exercise__title">{{title}}</h3>
            <p class="view-workout__exercise__subtitle">{{duration}} secs</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        number: Number,
        title: String,
        duration: Number,
        image: String
    }
}
</script>
