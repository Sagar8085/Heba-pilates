<template>
    <div>
            <div class="modal__box" v-if="!this.displaySuccessModal">
                <div class="modal__header">
                    <h3 class="modal__title">New Training Package</h3>
                </div>

                <div class="modal__body">
                    <div class="row">
                        <div class="six-sm columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Name
                                    <span v-if="this.errors['name']">{{ this.errors['name'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <input type="text" required v-model="name" placeholder="Fitness Starter">
                                </div>
                            </div>
                        </div>

                        <div class="six-sm columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Price
                                    <span v-if="this.errors['price']">{{ this.errors['price'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <select required v-model="price">
                                        <option value="">-- Select --</option>
                                        <option value="499">£4.99</option>
                                        <option value="999">£9.99</option>
                                        <option value="1499">£14.99</option>
                                        <option value="1999">£19.99</option>
                                        <option value="2499">£24.99</option>
                                        <option value="2999">£29.99</option>
                                        <option value="3999">£39.99</option>
                                        <option value="4999">£49.99</option>
                                        <option value="5999">£59.99</option>
                                        <option value="6999">£69.99</option>
                                        <option value="7999">£79.99</option>
                                        <option value="8999">£89.99</option>
                                        <option value="9999">£99.99</option>
                                        <option value="12499">£124.99</option>
                                        <option value="14999">£149.99</option>
                                        <option value="17499">£174.99</option>
                                        <option value="19999">£199.99</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="six-sm columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Credits
                                    <span v-if="this.errors['credits']">{{ this.errors['credits'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <select required v-model="credits">
                                        <option value="">-- Select --</option>
                                        <option value="1">1 Session</option>
                                        <option value="2">2 Sessions</option>
                                        <option value="3">3 Sessions</option>
                                        <option value="4">4 Sessions</option>
                                        <option value="5">5 Sessions</option>
                                        <option value="10">10 Sessions</option>
                                        <option value="15">15 Sessions</option>
                                        <option value="20">20 Sessions</option>
                                        <option value="25">25 Sessions</option>
                                        <option value="30">30 Sessions</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="six-sm columns">
                            <div class="form-element">
                                <span class="form-element__label">
                                    * Session Length
                                    <span v-if="this.errors['price']">{{ this.errors['price'][0] }}</span>
                                </span>
                                <div class="form-element__control">
                                    <select required v-model="session_length">
                                        <option value="">-- Select --</option>
                                        <option value="30">30 Minutes</option>
                                        <option value="60">1 Hour</option>
                                        <option value="120">2 Hours</option>
                                        <option value="180">2 Hours 30 Minutes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="form-element__label">
                            * Description <br><small class="extra-small">({{description.length}} / 2000)</small>
                            <span v-if="this.errors['description']">{{ this.errors['description'][0] }}</span>
                        </span>
                        <div class="form-element__control">
                            <textarea required v-model="description" rows="5" placeholder="John is a very capable personal trainer with over 5 years experience, training some of the most well known celebrities in the UK. His qualifications are..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal__footer">
                    <button type="button" class="button button--outline" @click="$emit('complete')">Cancel</button>
                    <button class="button" @click="save()" v-if="!this.uploading">Save</button>
                    <button class="button" v-else><i class="fas fa-spinner fa-spin"></i></button>
                </div>
            </div>

            <div class="modal__box" v-else>
                <div class="modal__header">
                    <h3 class="modal__title">Package Created</h3>
                </div>

                <div class="modal__body">
                    <p class="modal__text">Great! Your new training package has been created and is now visible on this trainers profile.</p>
                </div>


                <div class="modal__footer">
                    <button type="button" class="button" @click="$emit('complete')">Okay!</button>
                </div>
            </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            uploading: false,
            errors: [],

            name: '',
            price: '',
            credits: '',
            session_length: '',
            description: '',

            displaySuccessModal: false
        }
    },

    mounted() {
    },

    methods: {
        save() {
            this.uploading = true;

            axios.post('/api/admin/packages', {
                name: this.name,
                price: this.price,
                credits: this.credits,
                session_length: this.session_length,
                description: this.description,
                user_id: this.$route.params.id
            })
            .then(response => {
                this.displaySuccessModal = true;
                console.log(response);
            })
            .catch(error => {
                this.uploading = false;
                this.errors = error.response.data.errors;
            });
        }
    }
}
</script>
