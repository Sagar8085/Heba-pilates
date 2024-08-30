<template>
<div>
    <p class="create__label">Choose an icon that best describes the workout</p>
    <ul class="image-select">
        <li 
            v-for="icon in workoutIcons" 
            :key="icon" 
            class="image-select__item"
            @click="onSelectImage(icon)">
            <div :class="{ 'image-select__thumbnail': true, 'image-select__thumbnail--selected': localValue == icon }">
                <inline-svg class="image-select__img" :src="icon" />
            </div>
        </li>
    </ul>
</div>
</template>

<script>
import InlineSvg from 'vue-inline-svg';
export default {
    props: { value: String },
    components: { InlineSvg },
    computed: {
        localValue: {
            get () { return this.value },
            set (value) { this.$emit('input', value) }
        }
    },
    data () {
        return {
            workoutIcons: [
                '/images/icon-quickFix.svg',
                '/images/icon-getASweatOn.svg',
                '/images/icon-mindBody.svg'
            ]
        }
    },
    methods: {
        onSelectImage (image){
            this.localValue = image;
        }
    }
}
</script>