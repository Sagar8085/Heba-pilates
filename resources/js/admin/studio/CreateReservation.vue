<template>
    <div>
        <PageHeader :title="'Reserve ' + machine.name" :subtitle="studio.name" icon="event" />

        <!-- <breadcrumbs :links="breadcrumbLinks" @click="goBack" /> -->

        <div class="page-content">
            <div class="row">
                <div class="columns eight-md six-xl">
                    <h3 class="page-content__subtitle">Assign a member to this time slot</h3>

                    <span class="form-element__label">Search Member Name or Email Address</span>

                    <div class="row">
                        <div class="eight columns">
                            <div class="form-element">
                                <div class="form-element__control" style="margin-top: 0;">
                                    <input type="text" v-model="memberSearch" placeholder="Enter search term">
                                </div>
                            </div>
                        </div>

                        <div class="four columns">
                            <button class="button" @click="searchMember()">Search</button>
                        </div>
                    </div>

                    <div class="form-element" v-if="members.length > 0">
                        <hr style="margin: 0 0 2.5rem; width: 100%;">
                        <span class="form-element__label">Select a Member</span>

                        <div class="form-element__control">
                            <select v-model="member" @change="loadMemberCreditPacks">
                                <option value="null" disabled selected>-- Select a Member --</option>
                                <option :value="user" v-for="(user, index) in members">{{ '#' + user.id + ' - ' + user.name + ' - ' + user.email }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-element" v-if="member != null">
                        <hr style="margin: 0 0 2.5rem; width: 100%;">
                        <span class="form-element__label">Choose a Credit Method</span>

                        <div class="form-element__control">
                            <select v-model="creditType">
                                <option value="" disabled selected>-- Select a Credit Method --</option>

                                <option :value="'membership:' + member.subscription.id" v-if="member.subscription !== null" :disabled="member.subscription.studio_credits <= 0 ? true : false">{{ member.subscription.name }} Membership - {{ member.subscription.studio_credits_human }} Studio Credits Remaining</option>
                                <option value="" v-if="member.subscription === null" :disabled="true">No Membership Option Available</option>

                                <option :value="'credit:' + creditPack.id" v-for="(creditPack, index) in creditPacks" :disabled="creditPack.studio_credits <= 0 ? true : false">Credit Pack - {{ creditPack.pack.name }} - {{ creditPack.studio_credits }} Studio Credits Remaining</option>
                            </select>
                        </div>

                        <div v-if="member.subscription === null && creditPacks.length == 0" style="margin-top: 2rem; font-size: .9rem;">
                            This member doesn't have an active subscription or any credit packs that you can deduct credits from.<br><br>
                            Members can purchase memberships and credit packs from within the mobile app, and from the web portal on their computer.<br><br>
                            You can also purchase Credit Packs and Memberships for members, from their profile.<br><br>
                            <router-link :to="'/admin/members/' + this.member.id" class="button">View Member Profile</router-link>
                        </div>
                    </div>
                </div>
                <div class="columns four-md six-xl">
                    <h3 class="page-content__subtitle">Booking Summary</h3>
                    <dl class="description-list">
                        <div class="description-list__item">
                            <dt>Time</dt>
                            <dd>{{ this.time }}</dd>
                        </div>
                        <div class="description-list__item">
                            <dt>Date</dt>
                            <dd>{{ this.$route.query.date }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="columns twelve button-list" v-if="creditType != ''">
                <button class="button button--outline" @click="goBack">
                    Cancel
                </button>
                <button class="button" @click="reserve">Confirm Booking</button>
            </div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Breadcrumbs from '../../components/Breadcrumbs.vue';
import AdminInput from '../layout/AdminInput.vue';
import PageHeader from '../layout/PageHeader.vue'

export default {
    components: { PageHeader, AdminInput, Breadcrumbs },
    data () {
        return {
            machine: {
                name: 'Test Machine'
            },
            studio: {
                name: 'Test Studio'
            },
            timeslot: {
                date_human: 'test',
                time_human: 'test1'
            },

            memberSearch: '',
            memberName: '',

            creditPacks: [],
            members: [],
            member: null,
            creditType: ''
        }
    },

    mounted() {
    },

    computed: {
        time() {
            var timekey = this.$route.query.timekey.toString();

            if (timekey.length == 3) {
                var timekey = '0' + timekey;
            }

            var timekeyFirst = timekey;
            var timekeySecond = timekey;

            var newTime = timekeyFirst.substring(0, 2) + ':' + timekeySecond.substring(2);
            return newTime;

        },

        breadcrumbLinks () {
            return [
                { title: 'Studio Reservations', link: `/admin/studio/${this.studio.id}` },
                { title: 'Nuforma Machines', link: `/admin/studio/${this.studio.id}/machines` },
                { title: this.machine.name + ' Reservations' },
                { title: 'Create Reservation' }
            ]
        }
    },
    methods: {
        reserve () {
            axios.post('/api/admin/reserve-slot', {
                member_id: this.member.id,
                creditMethod: this.creditType,
                time: this.time,
                date: this.$route.query.date,
                machine_id: this.$route.params.machine_id,
                studio_id: this.$route.params.studio_id,
            }).then(response => {
                console.log(response);
                this.$router.back();
            });
        },
        goBack () {
            // this.$emit('back')
            this.$router.back();
        },

        searchMember() {
            this.member = null;
            this.creditType = '';
            this.creditPacks = [];

            axios.post('/api/admin/member-search', {
                keyword: this.memberSearch
            }).then(response => {
                this.members = response.data;
            });
        },

        loadMemberCreditPacks() {
            this.creditType = '';
            this.creditPacks = [];

            axios.get('/api/admin/members/' + this.member.id + '/credit-packs/all').then(response => {
                this.creditPacks = response.data;
            });
        },

        loadMembers() {

        }
    }
}
</script>
