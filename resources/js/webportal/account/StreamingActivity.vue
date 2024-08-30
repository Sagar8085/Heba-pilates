<template>
<div class="account account--stream-activity wrapper">
    <h1>Recent Streaming Activity</h1>

    <p v-if="deviceActivity.length == 0">You have no recent streaming activity.</p>

    <p v-else>Most recent activity on your account is displayed below.</p>

    <section v-for="device in deviceActivity" :key="device.name" class="account__section">
        <h3>{{ device.name }}</h3>

        <dl>
            <div v-for="(activity, i) in device.activity" :key="i" class="account__section__list-item">
                <dt class="page-subtitle">{{ activity.location }}</dt>
                <dd>Last used: {{ activity.datetime_human }}</dd>
            </div>
        </dl>
    </section>

    <div class="account-info account-info--inline">
        <router-link to="/myaccount" class="button button--outline">Go Back</router-link>
    </div>
</div>
</template>

<script>
export default {
    data () {
        return {
            deviceActivity: [
                {
                    name: 'Web Browser',
                    activity: [
                        {
                            location: 'Manchester (GB) - 169.7.203.207',
                            datetime_human: '23/04/2021, 07:02:23 GMT'
                        },
                        {
                            location: 'Manchester (GB) - 169.7.203.207',
                            datetime_human: '23/04/2021, 07:02:23 GMT'
                        },
                        {
                            location: 'Manchester (GB) - 169.7.203.207',
                            datetime_human: '23/04/2021, 07:02:23 GMT'
                        }
                    ]
                },
                {
                    name: 'Apple iPhone 11',
                    activity: [
                        {
                            location: 'Manchester (GB) - 169.7.203.207',
                            datetime_human: '23/04/2021, 07:02:23 GMT'
                        },
                        {
                            location: 'Manchester (GB) - 169.7.203.207',
                            datetime_human: '23/04/2021, 07:02:23 GMT'
                        },
                        {
                            location: 'Manchester (GB) - 169.7.203.207',
                            datetime_human: '23/04/2021, 07:02:23 GMT'
                        }
                    ]
                }
            ]
        }
    }
}
</script>