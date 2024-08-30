<template>
    <FullCalendar
        defaultView="timeGridWeek"
        :nowIndicator="true"
        :editable="false"
        :eventDurationEditable="true"
        :selectable="true"
        :plugins="calendarPlugins"
        :select="this.select"
        :slotDuration="'00:60:00'"
        :firstDay="1"
        @select="select"
        @eventClick="eventClick"
        @eventDragStop="eventDragStop"
        @eventResize="eventResize"
        @datesRender="datesRender"
        :events="events"
        :eventOverlap="false"
        :selectOverlap="false"
        :minTime="'06:00:00'"
        v-bind="calendarOptions"
    />
</template>

<script>
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction'; // for selectable
import moment from 'moment';
import axios from 'axios';

export default
{
    props: {
        user: Object
    },

    components: {
        FullCalendar // make the <FullCalendar> tag available
    },

    data()
    {
        return {
            date: moment().format('YYYY-MM-DD'),
            calendarPlugins: [ timeGridPlugin, interactionPlugin ],
            week_start: '',
            week_end: '',
            events: [
                {
                    title: 'The Title',
                    start: '2019-07-17T10:00:00',
                    end: '2019-07-17T11:00:00',
                    allDay: false,
                    backgroundColor: 'rgba(0,0,0,0.25)',
                    borderColor: 'rgba(0,0,0,0.25)'
                },
            ]
        }
    },
    
    computed: {
        calendarOptions () {
            return {
                titleFormat: { year: 'numeric', month: 'short' },
                columnHeaderHtml: date => {
                    return `
                        <span class="fc-day-header-day">${moment(date).format('ddd')}</span>
                        <strong class="fc-day-header-date">${moment(date).format('D')}</strong>`
                },
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                }
            }
        }
    },

    methods: {

        select: function(info)
        {
            this.save_availability(info);
        },

        eventClick: function(info)
        {
            if (confirm("Are you sure about this change?")) {

                this.delete_availability(info.event.id);

            }
        },

        eventDragStop: function(info)
        {
            // console.log(info);
        },

        eventResizeStop: function(info)
        {
            // console.log(info);
            // console.log(info.event.start);
            // console.log(info.event.end);
        },

        eventResize: function(info)
        {
            console.log(info.event.end);
            this.update_availability(info);
        },

        delete_availability: function(availability_id)
        {
            axios.post('/api/admin/trainers/' + this.user.id + '/availability/' + availability_id + '/delete',
                {
                }
            )
            .then(response => {

                this.get_availability();

            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        update_availability: function(info)
        {
            axios.post('/api/admin/trainers/' + this.user.id + '/availability/' + info.event.id,
                {
                    end: info.event.end
                }
            )
            .then(response => {

                if (response.data.status === 'error') {

                    alert('An availability block can only span a single day.');
                    window.location.reload();

                } else {

                    this.get_availability();

                }

            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        save_availability: function(info)
        {
            axios.post('/api/admin/trainers/' + this.user.id + '/availability/save/',
                {
                    start: info.startStr,
                    end: info.endStr
                }
            )
            .then(response => {

                if (response.data.status === 'error') {

                    alert('An availability block can only span a single daysssss.');
                    window.location.reload();

                } else {

                    var event = response.data.availability;

                    this.events.push({
                        id: event.id,
                        title: "Availability",
                        start: event.start,
                        end: event.end,
                        backgroundColor: event.type !== 'free-vpt' ? '#2196F3' : '#E91E63',
                        borderColor: 'rgba(0,0,0,0.25)'
                    });

                }

            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        get_availability: function()
        {
            axios.post('/api/admin/trainers/' + this.user.id + '/availability/',
                {
                    week_start: this.week_start,
                    week_end: this.week_end
                },
            )
            .then(response => {

                var availability = [];

                // Generate the availability
                response.data.availability.forEach(event => {
                    availability.push({
                        id: event.id,
                        title: "Availability",
                        start: event.start,
                        end: event.end,
                        backgroundColor: event.type !== 'free-vpt' ? '#2196F3' : '#E91E63',
                        borderColor: 'rgba(0,0,0,0.25)'
                    });
                });

                this.events = availability;

            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        },

        datesRender: function(info)
        {
            this.week_start = info.view.currentStart;
            this.week_end = info.view.currentEnd;

            axios.post('/api/admin/trainers/' + this.user.id + '/availability/',
                {
                    week_start: info.view.currentStart,
                    week_end: info.view.currentEnd
                }
            )
            .then(response => {

                var availability = [];

                response.data.availability.forEach(event => {
                    availability.push({
                        id: event.id,
                        title: "Availability",
                        start: event.start,
                        end: event.end,
                        backgroundColor: event.type !== 'free-vpt' ? '#2196F3' : '#E91E63',
                        borderColor: 'rgba(0,0,0,0.25)'
                    });
                });

                this.events = availability;

            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);

        },

    },

    mounted()
    {
        // this.get_availability();
    }
}
</script>

<style lang='scss'>

@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';
@import '~@fullcalendar/timegrid/main.css';

</style>
