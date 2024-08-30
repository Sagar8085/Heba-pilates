<template>
<section class="data-table">
    <header v-if="title" class="data-table__header">
        <h3 class="data-table__header__title">{{ title }}</h3>
        <div v-if="!!$scopedSlots.header || !!$slots.header" class="data-table__header__aside">
            <slot name="header"></slot>
        </div>
    </header>
    <div class="data-table__table-container">
        <p v-if="noDataMessage && rows.length == 0" class="data-table__no-data">{{ noDataMessage }}</p>
        <table v-else class="data-table__table">
            <thead>
                <th
                    v-for="(col, key) in cols"
                    :key="key"
                    :class="{ 'data-table__table__cell': true, 'data-table__table__cell--selected': sortColumn == key, 'data-table__table__cell--sortable': sortable && col, 'data-table__table__cell--image': !col && (key == 'image' || key == 'avatar') }"
                    @click="sortRows(key)">
                    <span>{{ col }}</span>
                    <i v-if="sortColumn == key" :class="{ 'fas fa-caret-down': !ascending, 'fas fa-caret-up': ascending }" />
                </th>
            </thead>
            <tbody>
                <tr v-for="(row, index) in sortedRows" :key="index">
                    <td
                        v-for="(heading, colKey) of cols"
                        :key="colKey"
                        :class="{ 'data-table__table__cell': true, 'data-table__table__cell--right': alignRightCriteria(row[colKey], colKey), 'data-table__table__cell--highlight': isHighlightedCol(colKey) }">
                        <slot :name="'cell-' + colKey" :item="row" :cell="row[colKey]">
                            {{ getRowValue(row[colKey]) }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        cols: Object, // { heading_name: "Heading Name" }
        rows: Array, // [ { heading_name: value } ]
        title: String,
        highlightedCol: {
            type: Number,
            default: 0 // index of column with highlighted text, use -1 for none
        },
        sortable: {
            type: Boolean,
            default: false
        },
        noDataMessage: String
    },
    data () {
        return {
            ascending: true,
            sortColumn: ''
        }
    },
    computed: {
        sortedRows () {
            if (!this.sortColumn) return this.rows;

            const rows = [ ...this.rows ];

            rows.sort((a, b) => {
                let aVal = a[this.sortColumn];
                let bVal = b[this.sortColumn];

                // handle null values
                if (aVal === null && bVal !== null)
                    return this.ascending ? 1 : -1;
                if (bVal === null && aVal !== null)
                    return this.ascending ? -1 : 1;
                if (a === null && b === null)
                    return 0;

                if (typeof aVal == 'string' && typeof bVal == 'string')
                    return this.ascending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                else if (aVal > bVal)
                    return this.ascending ? 1 : -1
                else if (aVal < bVal)
                    return this.ascending ? -1 : 1

                return 0;
            })
            return rows;
        }
    },
    methods: {
        getRowValue (value) {
            if (value && this.isDate(value))
                return this.formatDate(value);

            return value;
        },
        formatDate (dateString) {
            return moment(dateString).format('DD/MM/YYYY')
        },
        isDate (value) {
            return typeof value !== 'number' && moment(value, true).isValid();
        },
        alignRightCriteria (value, col) {
            return col == 'actions';
        },
        sortRows (col) {
            if (!this.sortable || !this.cols[col]) return;

            if (this.sortColumn === col)
                this.ascending = !this.ascending;
            else {
                this.ascending = true;
                this.sortColumn = col;
            }
        },
        isHighlightedCol (col) {
            if (this.highlightedCol == -1) return false;

            let keys = Object.keys(this.cols);

            return keys[this.highlightedCol] == col;
        }
    }
}
</script>
