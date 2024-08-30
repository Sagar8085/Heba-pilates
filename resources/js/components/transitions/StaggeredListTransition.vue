<template>
    <transition-group
        appear
        name="staggered-fade"
        :tag="tag"
        v-bind:css="false"
        v-on:before-enter="beforeEnter"
        v-on:enter="enter"
        v-on:leave="leave">
        <slot></slot>
    </transition-group>
</template>

<script>
export default {
    props: {
        transitionTime: {
            type: Number,
            default: .75
        },
        tag: {
            type: String,
            default: 'div'
        }
    },
    methods: {
        beforeEnter: function (el) {
            el.style.opacity = 0
            el.style.transform = 'scale(.9)'
            el.style.transition = `${this.transitionTime}s ease`
        },
        enter: function (el, done) {
            let delay = el.dataset.index * (this.transitionTime * 1000 / 3)
            setTimeout(() => {
                el.style.opacity = 1;
                el.style.transform = 'scale(1)'

                setTimeout(done, this.transitionTime * 1000)
            }, delay)
        },
        leave: function (el, done) {
            let delay = el.dataset.index * (this.transitionTime * 1000 / 3)
            setTimeout(() => {
                el.style.opacity = 0;
                el.style.transform = 'scale(.9)'

                setTimeout(done, this.transitionTime * 1000)
            }, delay)
        }
    }
}
</script>