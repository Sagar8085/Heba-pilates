<template>
    <div v-if="loading || noData" :class="{ 'loading-spinner': true, 'loading-spinner--no-data': noData }">
        <template v-if="loading">
            <i class='fas fa-spinner fa-spin' />
            <span>
                <slot>Loading{{ loadingText ? ' ' + loadingText : '' }}...</slot>
            </span>
        </template>
        <template v-else-if="noData">
            <slot name="no-data">
                {{ noDataText ? noDataText : loadingText ? `No ${loadingText} were found.` : 'No data could be found.' }}
            </slot>
        </template>
    </div>
</template>
<script>
export default {
    props: {
        loading: Boolean,
        loadingText: String,
        noData: Boolean,
        noDataText: String
    }
}
</script>
