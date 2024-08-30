<template>
<div>
    <PageHeader :title="booking.reformer.name + ' Reservation'" :subtitle="studio.name" icon="event" />

    <breadcrumbs :links="breadcrumbLinks" />

    <div class="page-content">
        <BookingInfoCard
            class="card"
            :status="booking.status"
            :datetime="booking.date_human + ' • ' + booking.time_human"
            :memberInfo="booking.member"
            :bookingId="booking.id"
            :equipmentName="booking.reformer.name"
            />
    </div>

</div>
</template>

<script>
import Breadcrumbs from '../../components/Breadcrumbs.vue';
import BookingInfoCard from '../../components/BookingInfoCard.vue';
import PageHeader from '../layout/PageHeader.vue'
import axios from 'axios'

export default {
    components: { PageHeader, Breadcrumbs, BookingInfoCard },
    data () {
        return {
            studio: {},
            booking: {
                reformer: {}
            }
        }
    },
    computed: {
        breadcrumbLinks () {
            return [
                { title: 'Studio Reservations', link: `/admin/studio/${this.studio.id}` },
                { title: 'Nuforma Machines', link: `/admin/studio/${this.studio.id}/machines` },
                { title: this.booking.reformer.name + ' Reservations', link: `/admin/studio/${this.studio.id}/machines/${this.booking.reformer.id}` },
                { title: this.booking.date_human + ' • ' + this.booking.time_human }
            ]
        }
    },
    methods: {
        getBooking () {
            axios.get('/api/admin/gyms/reservations/' + this.$route.params.booking_id).then(response => {
                this.booking = response.data;
            });
        },
        getStudio () {
            this.studio = {
                id: this.$route.params.studio_id,
                name: 'Studio ' + this.$route.params.studio_id,
                machines: [
                    { id: 1, name: 'Nuforma #1', location: 'Room 1', status: 'active' },
                    { id: 2, name: 'Nuforma #2', location: 'Room 1', status: 'maintenance' },
                    { id: 3, name: 'Nuforma #3', location: 'Room 2', status: 'active' }
                ]
            }
        },
        async fetchData () {
            await Promise.all([ this.getStudio(), this.getBooking() ]);
        }
    },
    async mounted () {
        await this.fetchData();
    }
}
</script>
