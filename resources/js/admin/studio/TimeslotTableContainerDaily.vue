<template>
    <section class="timeslot-table">

        <div class="timeslot-table__header">
            <h3 class="timeslot-table__header__title">
                {{ currentDate.format('dddd Do MMMM') }}
            </h3>

            <div class="pagination">
                <button
                    v-if="!currentDate.isSame(today, 'day')"
                    class="button button--icon button--transparent"
                    @click="$emit('set-date-to-today')"
                    v-tooltip="'Today'"
                >
                    <i class="fas fa-calendar-day" />
                </button>

                <button
                    class="button button--icon button--transparent"
                    @click="$emit('previousPage')"
                    v-tooltip="'Previous day'"
                >
                    <i class="fas fa-chevron-left" />
                </button>

                <span>
                    {{ currentDate.format('DD MMMM YYYY') }}
                </span>

                <button
                    class="button button--icon button--transparent"
                    @click="$emit('nextPage')"
                    v-tooltip="'Next day'"
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
                        <th v-for="(machine, index) in machines" :key="index">
                            <p class="timeslot-table__table__control-toggle" @click="toggleControls(machine.id)">
                                {{ machine.name }}
                            </p>

                            <span
                                v-if="controlsAreVisible(machine.id)"
                                class="timeslot-table__table__controls"
                            >
                                <button
                                    class="button button--icon button--transparent button--small"
                                    v-tooltip="'Block machine'"
                                    @click="$emit('block-out-machine', machine.id, currentDate)"
                                >
                                    <i class="fas fa-times-circle" />
                                </button>
                                <button
                                    class="button button--icon button--transparent button--small"
                                    v-tooltip="'Free machine'"
                                    @click="$emit('free-up-machine', machine.id, currentDate)"
                                >
                                    <i class="fas fa-check-circle" />
                                </button>
                                <button
                                    class="button button--icon button--transparent button--small"
                                    v-tooltip="'Show in 7 Day Schedule'"
                                    @click="$emit('jump-to-weekly', machine.id)"
                                >
                                    <i class="fas fa-angle-double-right" />
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
        currentDate: {
            type: Object,
            required: true,
        },

        gymId: {
            type: Number,
        },

        machines: {
            type: Array,
        }
    },

    computed: {
        today() {
            return moment();
        },
    },
    methods: {

    },
}
</script>
