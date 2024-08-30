<template>
    <section class="filter-bar">
        <form-input v-model="filterDate" type="calendar" placeholder="Date" />

        <form-input v-model="filterCategory" type="select" placeholder="Hebe Journey">
            <option v-if="allOption" value="">All Journeys</option>
            <option :value="category.id" v-for="category in categories" :key="category.id">{{ category.name }}</option>
        </form-input>

        <form-input v-model="filterDuration" type="select" placeholder="Duration">
            <option v-if="allOption" value="">All Durations</option>
            <option value="30">30 minutes</option>
            <option value="45">45 minutes</option>
            <option value="60">1 hour</option>
            <option value="90">1 hour 30 minutes</option>
        </form-input>
    </section>
</template>

<script>
import FormInput from './FormInput.vue'

export default {
    components: { FormInput },
    props: {
        date: [Date, String],
        category: [String,Number],
        categories: Array,
        duration: [Number,String],
        allOption: Boolean
    },
    computed: {
        filterDate: {
            get () { return this.date },
            set (val) { this.$emit('update:date', val) }
        },
        filterCategory: {
            get () { return this.category },
            set (val) { this.$emit('update:category', val) }
        },
        filterDuration: {
            get () { return this.duration },
            set (val) { this.$emit('update:duration', val) }
        }
    }
}
</script>