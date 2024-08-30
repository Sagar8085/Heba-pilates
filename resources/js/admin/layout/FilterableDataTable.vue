<template>
    <section>
        <div v-if="enableSearch || pagination" class="filter-bar">
            <div class="filter-bar__left">
                <label class="search-input" v-if="enableSearch">
                    <input v-model="searchTerm" :placeholder="searchPlaceholder" />
                </label>
                <!-- <label v-if="filterOptions" for="filter-select" class="form-element__control--select">
                    <select id="filter-select" v-model="localFilter">
                        <option value="" disabled="disabled" selected="selected" hidden="hidden">Filter</option>
                        <option
                            v-for="filter in filterOptions"
                            :key="filter.value"
                            :value="filter.value">
                            {{ filter.option }}
                        </option>
                    </select>
                </label> -->
            </div>
            <div v-if="pagination && pagination.last_page > 1" class="pagination">
                <button class="button button--icon button--transparent" @click="$emit('previousPage', pagination.current_page - 1)" :disabled="pagination.current_page < 2">
                    <i class="fas fa-chevron-left" />
                </button>
                <span>
                    Page {{ pagination.current_page }} of {{ pagination.last_page }}
                </span>
                <button class="button button--icon button--transparent" @click="$emit('nextPage', pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page">
                    <i class="fas fa-chevron-right" />
                </button>
            </div>
        </div>
        <data-table
            :style="!pagination || pagination.last_page == 1 ? 'margin-top: 1.5rem;' : '' "
            sortable
            :title="pagination && pagination.total ? `${pagination.total} ${title}` : `${rows.length} ${title}`"
            :rows="rows"
            :cols="cols"
            :highlightedCol="highlightedCol"
            :noDataMessage="noDataMessage">
            <template v-for="(index, name) in $scopedSlots" v-slot:[name]="data">
                <slot :name="name" v-bind="data"></slot>
            </template>
        </data-table>
    </section>
</template>

<script>
import DataTable from '../layout/DataTable.vue'
export default {
    components: {
        DataTable
    },
    props: {
        searchPlaceholder: {
            default: 'Search',
            type: String
        },
        filterOptions: Array, // [ { option: "Filter", value: "filter" } ]
        filter: String,
        rows: Array,
        cols: Object,
        noDataMessage: String,
        title: {
            default: 'Items',
            type: String
        },
        highlightedCol: Number,
        enableSearch: Boolean,
        pagination: Object
    },
    data () {
        return {
            searchTerm: '',
        }
    },
    computed: {
        localFilter: {
            get () { return this.filter },
            set (val) { this.$emit('update:filter', val) }
        }
    }
}
</script>
