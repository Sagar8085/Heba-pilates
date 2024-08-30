<template>
    <div>
        <h3>When would you like to visit?</h3>
        <div class="row">
            <div class="columns six-md">
                <form-input v-model="chosenDate" type="select" placeholder="Select a date">
                    <option :value="date.raw" v-for="date in dates" :key="date.raw">{{ date.formatted }}</option>
                </form-input>

                <small style="font-size: .75rem;"><i class="fas fa-info-circle"></i> Reservations are limited depending on your membership type.</small>
            </div>
        </div>

        <!-- <h3>Do you need any equipment?</h3>
        <div class="row">
            <div class="columns six-md">
                <form-input v-model="chosenEquipment" type="select" placeholder="Select equipment">
                    <option value="None">No equipment</option>
                    <option value="Reformer">Reformer</option>
                </form-input>
            </div>
        </div> -->

        <template v-if="chosenDate">

            <loading-spinner
                :loading="loading"
                loadingText="time slots"
                :noData="timeslots.length == 0"
                noDataText="Unfortunately there are no time slots available for this studio on the date selected." />

            <template v-if="!loading && timeslots.length > 0">
                <h3>{{ timeslots.length }} available time {{ timeslots.length == 1 ? 'slot' : 'slots' }}</h3>

                <staggered-list-transition>
                    <div v-for="(slot, i) in timeslots" :key="slot.time_human" :data-index="i" class="horizontal-card horizontal-card--4-cols horizontal-card--bordered">
                        <div class="horizontal-card__section">
                            <label class="horizontal-card__section__title">Time</label>
                            <span class="horizontal-card__section__value horizontal-card__section__value--large">{{ slot.time_human }}</span>
                        </div>

                        <div class="horizontal-card__section">
                            <label class="horizontal-card__section__title">Date</label>
                            <span class="horizontal-card__section__value horizontal-card__section__value--large">{{ slot.date_human }}</span>
                        </div>

                        <div class="horizontal-card__section">
                            <label class="horizontal-card__section__title">Duration</label>
                            <span class="horizontal-card__section__value horizontal-card__section__value--large">{{ slot.duration }} mins</span>
                        </div>

                        <div class="horizontal-card__section">
                            <button class="button" @click="selectTimeslot(slot)">Select</button>
                        </div>
                    </div>
                </staggered-list-transition>
            </template>
        </template>

        <p v-else>
            Please select a date to view available time slots.
        </p>
    </div>
</template>

<script>
import FormInput from '../../components/FormInput.vue'
import StaggeredListTransition from '../../components/transitions/StaggeredListTransition.vue'
import LoadingSpinner from '../layout/LoadingSpinner.vue'

export default {
    components: { FormInput, StaggeredListTransition, LoadingSpinner },
    props: { date: String, equipment: String },

    mounted() {
        this.loadReservationDates();

        if (this.date) this.loadReservationTimeslots();
    },

    data () {
        return {
            dates: [],
            timeslots: [],

            loading: false
        }
    },

    watch: {
        chosenDate() {
            this.loadReservationTimeslots();
        }
    },

    computed: {
        chosenDate: {
            get () { return this.date },
            set (val) { this.$emit('update:date', val) }
        },
        chosenEquipment: {
            get () { return this.equipment },
            set (val) { this.$emit('update:equipment', val) }
        }
    },
    methods: {
        selectTimeslot (slot) {
            if (!this.chosenDate) return;

            this.$emit('select', slot)
        },

        loadReservationDates () {
            axios.get('/api/gyms/' + this.$route.params.id + '/reservations/dates').then(response => {
                this.dates = response.data;
            });
        },

        loadReservationTimeslots() {
            axios.get('/api/gyms/' + this.$route.params.id + '/reservations/timeslots?date=' + this.chosenDate).then(response => {
                this.timeslots = response.data;
            });
        }
    }
}
</script>
