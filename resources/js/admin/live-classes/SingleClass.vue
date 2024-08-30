<template>
    <div>
        <section class="page-header page-header--no-bottom">
            <div class="wrapper">
                <div class="page-header__col">
                    <h1 class="page-header__title">
                        <div class="icon">
                            <i class="material-icons">video_library</i>
                        </div>
                        Live Streaming
                    </h1>

                    <h2 class="page-header__sub">Class Schedule & Library</h2>
                </div>
            </div>
        </section>

        <section class="tab">
            <ul>
                <li class="active"><a>Class Details</a></li>
            </ul>
        </section>

        <section class="page-content">
            <div class="row">
                <div class="four columns">
                    <div class="form-element form-element--read-only">
                        <span class="form-element__label">Scheduled Time</span>
                        <div class="form-element__control">
                            <p class="form-element__static">{{ this.liveclass.datetime_human }}</p>
                        </div>
                    </div>
                </div>

                <div class="four columns">
                    <div class="form-element form-element--read-only">
                        <span class="form-element__label">Instructor</span>
                        <div class="form-element__control">
                            <p class="form-element__static">{{ this.liveclass.instructor.name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="four columns">
                    <div class="form-element form-element--read-only">
                        <span class="form-element__label">Category</span>
                        <div class="form-element__control">
                            <p class="form-element__static">{{ this.liveclass.category.name }}</p>
                        </div>
                    </div>
                </div>

                <div class="four columns">
                    <div class="form-element form-element--read-only">
                        <span class="form-element__label">Host</span>
                        <div class="form-element__control">
                            <p class="form-element__static"><a href="javascript: alert('This feature is currently in development.')">Start Stream</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab tab--no-top">
            <ul>
                <li class="active"><a>Bookings</a></li>
            </ul>
        </section>

        <section class="page-content page-content--no-top" v-if="this.liveclass.bookings.length > 0">
            <div class="list-wrap">
                <table class="list list--no-top list--with-hover">
                    <thead>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                    </thead>
                    <tbody>
                        <tr v-for="booking in this.liveclass.bookings">
                            <td>
                                <router-link :to="'/app/members/' + booking.id">{{ booking.name }}</router-link>
                            </td>
                            <td><a :href="'mailto:' + booking.email">{{ booking.email }}</a></td>
                            <td><a :href="'tel:' + booking.phone_number">{{ booking.phone_number }}</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="forbidden forbidden--small" v-if="this.liveclass.bookings.length == 0">
            <div>
                <img src="/images/illustrations/no-data.svg" class="small">
                <h2>No Attendees</h2>
                <p>No-one has signed up for this class yet.</p>
            </div>
        </section>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            liveclass: {
                bookings: [],
                instructor: []
            }
        }
    },

    mounted() {
        this.loadClass();
    },

    methods: {
        loadClass() {
            axios.get('/api/admin/live-classes/' + this.$route.params.id).then(response => {
                this.liveclass = response.data;
            });
        }
    }
}
</script>
