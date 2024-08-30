<template>
<div class="contact auth__wrapper">
    <div v-if="!sentMessage" class="wrapper">
        <h1>Contact us</h1>
        <div class="contact__sections">
            <form class="contact__form" @submit.prevent="sendMessage">
                <form-input v-model="email" type="email" placeholder="Email address" required />
                <form-input v-model="subject" type="select" placeholder="What can we help you with?" required>
                    <option value="general">General Inquiry</option>
                    <option value="feedback">Feedback/Comments</option>
                    <option value="help">Help</option>
                    <option value="billing">Subscriptions and Billing</option>
                    <option value="problem">Report a Problem</option>
                    <option value="other">Other</option>
                </form-input>
                <form-input
                    v-model="details"
                    type="textarea"
                    placeholder="Please provide more details..."
                    :rows="10"
                    required />
                <button class="button button--full" type="submit" :disabled="formInvalid">Submit</button>
            </form>
            <section class="contact__details">
                <p>Alternatively you can call or email us:</p>
                <div class="contact__details__item">
                    <span class="contact__details__item__icon">
                        <i class="fas fa-phone" />
                    </span>
                    <div class="contact__details__item__content">
                        <strong>Call</strong>
                        <p>0330 133 2273</p>
                    </div>
                </div>
                <div class="contact__details__item">
                    <span class="contact__details__item__icon">
                        <i class="fas fa-envelope" />
                    </span>
                    <div class="contact__details__item__content">
                        <strong>Email</strong>
                        <p>hello@Hebapilates.com</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div v-else class="wrapper">
        <h1>Thank you for contacting us</h1>

        <p class="contact__message-sent">We’ll send you a confirmation email to let you know we have received your message and one of our team will be in touch as soon as possible.</p>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import BackNavigation from './layout/BackNavigation.vue';
import FormInput from '../components/FormInput.vue';

export default {
    components: { BackNavigation, FormInput },
    data () {
        return {
            email: '',
            subject: '',
            details: '',
            sentMessage: false
        }
    },
    computed: {
        formInvalid () {
            return this.email.length == 0 || this.subject.length == 0 || this.details.length == 0;
        }
    },
    methods: {
        sendMessage () {
            if (this.formInvalid) return;

            // Call API
            axios.post('/api/contact', {
                email: this.email,
                topic: this.subject,
                message: this.details
            }).then(response => {
                this.sentMessage = true;
            });
        }
    }
}
</script>
