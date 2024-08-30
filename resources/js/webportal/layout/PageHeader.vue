<template>
    <div 
        class="workouts__hero" 
        :style="`background-image: linear-gradient(270deg, #FFFFFF00 30%, #00000071 80%, #000000 100%), url(${backgroundImage})`">
        <div class="workouts__container">
            <h1 class="workouts__hero__title">{{ title }}</h1>
            <p class="workouts__hero__text--small">
                <slot></slot>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        backgroundImage: String
    }
}
</script>