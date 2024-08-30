<template>
    <section class="timeslot-table">

        <div class="timeslot-table__header">
            <slot name="machineList"></slot>

            <div class="pagination">
                <button
                    v-if="!dates[0].isSame(today, 'day')"
                    class="button button--icon button--transparent"
                    @click="$emit('set-date-to-today')"
                    v-tooltip="'This week'"
                >
                    <i class="fas fa-calendar-week" />
                </button>

                <button
                    class="button button--icon button--transparent"
                    @click="$emit('previousPage')"
                    v-tooltip="'Previous week'"
                >
                    <i class="fas fa-chevron-left" />
                </button>

                <span>
                    {{ formattedStartDate }} - {{ formattedEndDate }}
                </span>

                <button
                    class="button button--icon button--transparent"
                    @click="$emit('nextPage')"
                    v-tooltip="'Next week'"
                >
                    <i class="fas fa-chevron-right" />
                </button>
            </div>
        </div>

        <div class="timeslot-table__table-container">
            <table class="timeslot-table__table">
                <thead>
                    <tr>
                        <th></th>
                        <th
                            v-for="(date, index) in formattedHeadingDates"
                            :key="index"

                        >
                            <p
                                :class="{
                                    'timeslot-table__table__control-toggle': true,
                                    'timeslot-table__table__heading--today': today.format('YYYY-MM-DD') === date,
                                }"
                                @click="toggleControls(date)"
                            >
                                {{ date }}
                            </p>

                            <span
                                v-if="controlsAreVisible(date)"
                                class="timeslot-table__table__controls"
                            >
                                    <button
                                        class="button button--icon button--transparent button--small"
                                        v-tooltip="'Show in Daily Schedule'"
                                        @click="$emit('jump-to-daily', date)"
                                    >
                                        <i class="fas fa-angle-double-left" />
                                    </button>
                                    <button
                                        class="button button--icon button--transparent button--small"
                                        v-tooltip="'Block out day'"
                                        @click="$emit('block-out-day', date)"
                                    >
                                        <i class="fas fa-times-circle" />
                                    </button>
                                    <button
                                        class="button button--icon button--transparent button--small"
                                        v-tooltip="'Free up day'"
                                        @click="$emit('free-up-day', date)"
                                    >
                                        <i class="fas fa-check-circle" />
                                    </button>
                            </span>
                        </th>
                    </tr>
                </thead>

                <slot name="timeslotTable"></slot>

            </table>
        </div>
    </section>
</template>

<script>

import moment from 'moment';
import tableControls from '../../mixins/tableControls';

export default {
    mixins: [
        tableControls,
    ],

    props: {
        dates: {
            type: Array,
            required: true,
        }
    },

    computed: {
        formattedStartDate() {
            return this.dates[0].format('dddd Do MMMM');
        },

        formattedEndDate() {
            return this.dates[this.dates.length - 1].format('dddd Do MMMM');
        },

        formattedHeadingDates() {
            return this.dates.map(date => date.format('YYYY-MM-DD'));
        },

        today() {
            return moment();
        },
    },

    methods: {

    },
}
</script>
