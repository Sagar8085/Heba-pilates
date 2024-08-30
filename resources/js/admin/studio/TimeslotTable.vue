<template>
    <tbody>
    <tr
        v-for="(columns, timeslot) in viewableData"
        :key="timeslot"
    >
        <td class="timeslot-table__table__time">
            <p class="timeslot-table__table__control-toggle" @click="toggleControls(timeslot)">
                {{ standardTimeTo12HourTime(timeslot) }}
            </p>

            <span
                v-if="controlsAreVisible(timeslot)"
                class="timeslot-table__table__controls"
            >
                <button
                    class="button button--icon button--transparent button--small"
                    v-tooltip="'Block out timeslot'"
                    @click="$emit('block-out-timeslot', timeslot, tableData[timeslot])"
                >
                    <i class="fas fa-times-circle" />
                </button>
                <button
                    class="button button--icon button--transparent button--small"
                    v-tooltip="'Free up timeslot'"
                    @click="$emit('free-up-timeslot', timeslot, tableData[timeslot])"
                >
                    <i class="fas fa-check-circle" />
                </button>
            </span>
        </td>

        <td v-for="(cell, cellIndex) in columns" :key="cellIndex">
            <span
                v-if="cell.bookingStatus.status === 'closed'"
                class="timeslot-table__table__tag timeslot-table__table__tag--black"
            >
                Closed
            </span>

            <span
                v-else-if="cell.bookingStatus.status === 'blockedOut'"
                class="timeslot-table__table__tag timeslot-table__table__tag--black"
            >
                N/A
            </span>

            <span
                v-else-if="cell.bookingStatus.status === 'trainerOnBreak'"
                class="timeslot-table__table__tag timeslot-table__table__tag--black"
            >
                Break
            </span>

            <span
                v-else-if="cell.bookingStatus.status === 'notWorking'"
                class="timeslot-table__table__tag timeslot-table__table__tag--red"
            >
                {{ cell.bookingStatus.notWorkingReason }}
            </span>

            <span
                v-else-if="cell.bookingStatus.status === 'unavailable'"
                class="timeslot-table__table__tag timeslot-table__table__tag--interactive"
                @click="unavailable"
            >
                <!-- Unavailable due to a booking in the following slot. -->
            </span>

            <span
                v-else-if="cell.bookingStatus.status === 'available'"
                class="timeslot-table__table__tag timeslot-table__table__tag--green timeslot-table__table__tag--interactive"
                @click="$emit('select-available', cell.date, timeslot, cell.machineId)"
            >
                Available
            </span>

            <router-link
                v-else
                :to="`/admin/members/${cell.bookingStatus.memberId}`"
                :class="getBookingCssClasses(cell.bookingStatus)"
                v-tooltip="getTooltipText(cell)"
            >
                {{ cell.bookingStatus.memberName }}
            </router-link>
        </td>
    </tr>
    </tbody>
</template>

<script>
import moment from 'moment';
import tableControls from '../../mixins/tableControls';

/**
 * @typedef {Object} BookingStatus
 * @property {String} status
 * @property {String} notWorkingReason
 * @property {Number} memberId
 * @property {String} memberName
 * @property {Boolean} isMemberFirstSession
 * @property {Boolean} memberHasCreditsRemaining
 * @property {string[]|string} memberRequiresAttentionBecause
 * @property {Boolean} memberHasCompletedPARQ
 * @property {Boolean} memberHasNewNote
 * @see app/Types/BookingTableStatus.php
 */

/**
 * @typedef {Object} TableCell
 * @property {Number} machineId
 * @property {String} date
 * @property {BookingStatus} bookingStatus
 */

/**
 * @typedef {TableCell[]} TableRow
 */

/**
 * @typedef {Object.<string, TableRow>} TableData
 */

export default {
    mixins: [
        tableControls,
    ],

    props: {
        tableData: {
            type: Object,
            required: true,
        },

        firstTimeslotShown: {
            type: String,
            default: '06:00:00',
        },

        lastTimeslotShown: {
            type: String,
            default: '22:30:00',
        },
    },

    computed: {
        /**
         * @returns {TableData}
         */
        viewableData() {
            /** @var {TableData} viewableData */
            const viewableData = {};

            Object.keys(this.tableData)
                .filter(timeslot => timeslot >= this.firstTimeslotShown && timeslot <= this.lastTimeslotShown)
                .forEach(timeslot => viewableData[timeslot] = this.tableData[timeslot]);

            return viewableData;
        },
    },

    methods: {
        /**
         * @param {String} time
         * @returns {String}
         */
        standardTimeTo12HourTime(time) {
            return moment(time, 'HH:mm:ss').format('h.mma').replace('.00', '');
        },

        unavailable() {
            alert('You cannot schedule this 1 hour slot as there is another booking in 30 minutes.');
        },

        /**
         * @param {BookingStatus} bookingStatus
         * @returns {String}
         *
         * @note Schedule colour coding
         * Orange - denotes a first time visit (working fine)
         * Red - denotes a person who has no more active credits left to book
         *      with or who’s subscription has been cancelled (so therefore to
         *      clarify - someone who is still active within their current
         *      subscription but who has a plan that is not due to renew).
         * Blue - denotes one of these three pieces (any of, not all of) of data
         *      is missing in their Guest profile:
         *          Email address (obviously this is not nullable so will always be
         *              present regardless, fine, just go blue if the other two are missing).
         *          Contact number
         *          HubPreferred Studio
         * Purple - denotes a Guest who does not have a completed PARQ.
         * Green - denotes that a new note has been added to this Guests profile
         *      notes within the last 7days (the general profile page notes, not
         *      the lead section notes within their profile)
         * Grey - denotes none of the above factors are apparent/valid for the
         *      Guest who has booked.
         * Black - Reserved for N/A, Breaks, Closed and Admin user bookings.
         * Please also note the hierarchy of these colours (top to bottom in
         *      order of priority/heirarchy please, so for example: If someone
         *      ticks all needs for all colours then they show orange and not
         *      any of the other colours; If someone has a new note within the
         *      last 7 days and also has a missing contact number field then
         *      they would show blue not green.
         */
        getBookingCssClasses(bookingStatus) {
            const classes = [
                'timeslot-table__table__tag',
                'timeslot-table__table__tag--interactive',
            ];

            if (bookingStatus.isMemberFirstSession) {
                classes.push('timeslot-table__table__tag--orange');
            } else if (!bookingStatus.memberHasCreditsRemaining) {
                classes.push('timeslot-table__table__tag--red');
            } else if (bookingStatus.memberRequiresAttentionBecause !== '') {
                classes.push('timeslot-table__table__tag--blue');
            } else if (!bookingStatus.memberHasCompletedPARQ) {
                classes.push('timeslot-table__table__tag--purple');
            } else if (bookingStatus.memberHasNewNote) {
                classes.push('timeslot-table__table__tag--green');
            }

            return classes.join(' ');
        },

        /**
         * @param {TableCell} cell
         * @returns {String}
         */
        getTooltipText({bookingStatus}) {
            const text = [];

            if (bookingStatus.isMemberFirstSession) {
                text.push('First visit');
            }

            if (!bookingStatus.memberHasCreditsRemaining) {
                text.push('No credits remaining');
            }

            text.push(bookingStatus.memberRequiresAttentionBecause.join('<br>'));

            if (!bookingStatus.memberHasCompletedPARQ) {
                text.push('Incomplete PARQ');
            }

            if (bookingStatus.memberHasNewNote) {
                text.push('New note');
            }

            return text.join('<br><br>');
        },
    },
}
</script>
