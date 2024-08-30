<template>
    <div class="row">
        <div class="columns eight-lg">
            <section class="studio__section studio__section--bordered">
                <h1>Booking Confirmed!</h1>
                <p>A booking confirmation email is on its way to you.</p>
            </section>

            <section class="studio__section studio__section--bordered">
                <div class="row row--center-v">
                    <div class="columns nine-md eight-lg">
                        <h3>Where is my booking stored?</h3>
                        <p>The booking will be stored in your Bookings.</p>
                    </div>
                    <div class="columns three-md four-lg text-right">
                        <router-link class="button" to="/bookings">View Bookings</router-link>
                    </div>    
                </div>
            </section>

            <section class="studio__section studio__section--bordered">
                <div class="row">
                    <div class="columns five-md studio__location">
                        <h3>Studio location</h3>
                        <p class="studio__location__address">
                            {{ studio.name }}
                            <br>
                            {{ studio.address.line_1 }}
                            <br>
                            {{ studio.address.postcode }}
                        </p>
                        <p><strong>Call:</strong> {{ studio.phone_number }}</p>

                        <h3 class="opening">Opening Times</h3>
                        <table class="opening_table">
                            <tr>
                                <td>Monday</td>
                                <td>{{this.studio.operating_hours.monday[0]}} to {{this.studio.operating_hours.monday[1]}} </td>
                            </tr>
                            <tr>
                                <td>Tuesday</td>
                                <td>{{this.studio.operating_hours.tuesday[0]}} to {{this.studio.operating_hours.tuesday[1]}} </td>
                            </tr>
                            <tr>
                                <td>Wednesday</td>
                                <td>{{this.studio.operating_hours.wednesday[0]}} to {{this.studio.operating_hours.wednesday[1]}} </td>
                            </tr>
                            <tr>
                                <td>Thursday</td>
                                <td>{{this.studio.operating_hours.thursday[0]}} to {{this.studio.operating_hours.thursday[1]}} </td>
                            </tr>
                            <tr>
                                <td>Friday</td>
                                <td>{{this.studio.operating_hours.friday[0]}} to {{this.studio.operating_hours.friday[1]}} </td>
                            </tr>
                            <tr>
                                <td>Saturday</td>
                                <td>{{this.studio.operating_hours.saturday[0]}} to {{this.studio.operating_hours.saturday[1]}} </td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>{{this.studio.operating_hours.sunday[0]}} to {{this.studio.operating_hours.sunday[1]}} </td>
                            </tr>
                        </table>
                    </div>
                    <div class="columns seven-md">
                        <google-map class="studio__map" :markers="[ studio.position ]" />
                    </div>
                </div>
            </section>
        </div>

        <div class="columns four-lg">
            <h2>Booking Summary</h2>
            
            <div class="live-class__row">
                <span class="live-class__time">{{ bookingDetails.timeslot.time_human }}</span>
            </div>
            
            <div class="live-class__row">
                <p><strong>Date:</strong> {{ bookingDetails.timeslot.date_human }}</p>
            </div>
            
            <div class="live-class__row">
                <p><strong>Duration:</strong> {{ bookingDetails.timeslot.duration }}</p>
            </div>
            
            <div class="live-class__row">
                <p><strong>Equipment:</strong> {{ bookingDetails.equipment }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import GoogleMap from '../../components/GoogleMap.vue'

export default {
    components: { GoogleMap },
    props: { studio: Object, bookingDetails: Object }
}
</script>