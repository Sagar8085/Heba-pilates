<template>
    <div class="door-access__card horizontal-card">

        <div class="horizontal-card__section horizontal-card__section--cols">
            <profile-picture
                :image="record.user ? record.user.avatar : ''"
                :firstName="record.user ? record.user.first_name : ''"
                :lastName="record.user ? record.user.last_name : ''" />

            <div class="horizontal-card__section__col">
                <label class="page-subtitle">Name</label>
                <p class="horizontal-card__section__value">{{ record.user ? record.user.name : '' }}</p>
            </div>
        </div>

        <div class="horizontal-card__section">
            <label class="page-subtitle">Member ID</label>
            <p class="horizontal-card__section__value">#{{ record.user ? record.user.id : '' }}</p>
        </div>

        <div class="horizontal-card__section">
            <label class="page-subtitle">Studio</label>
            <p class="horizontal-card__section__value">{{ record.gym !== null ? record.gym.name : '' }}</p>
        </div>

        <div class="horizontal-card__section">
            <label class="page-subtitle">Membership Type</label>
            <p class="horizontal-card__section__value">{{ record.user.subscription ? record.user.subscription.name : 'No Membership' }}</p>
        </div>

        <!-- <div class="horizontal-card__section">
            <label class="page-subtitle">Days Since Last Visit</label>
            <p class="horizontal-card__section__value">{{ record.days_since_last_visit }}</p>
        </div>

        <div class="horizontal-card__section">
            <label class="page-subtitle">Door</label>
            <p class="horizontal-card__section__value">{{ record.door }}</p>
        </div> -->

        <div class="horizontal-card__section">
            <label class="page-subtitle">Time of Visit</label>
            <p class="horizontal-card__section__value">{{ record.scanned_at_human }}</p>
        </div>

        <div class="horizontal-card__section">
            <router-link class="button" :to="memberProfileLink">View Profile</router-link>
        </div>
    </div>
</template>

<script>
import ProfilePicture from '../../components/ProfilePicture.vue';

export default {
    props: { record: Object },
    components: { ProfilePicture },
    computed: {
        memberProfileLink () {
            return `/admin/members/${this.record.user.id}`;
        }
    }
}
</script>
