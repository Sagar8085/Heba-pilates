<template>
    <div>
            <div class="modal__box">
                <div class="modal__header">
                    <h3 class="modal__title">Set Custom Opening Hours for {{ this.gym.name }}</h3>

                </div>

                <div class="modal__body">
                    <div class="row row--form">
                        <div class="form-element">
                            <div class="four-sm columns">
                                <span class="form-element__label">
                                     Select a Date
                                </span>
                            </div>
                            <div class="eight-sm columns">
                                <datepicker
                                        :value="new Date(date)"
                                        v-model="date"
                                        :open-date="new Date()"
                                        :disabledDates="disabledDates"
                                        placeholder="Select a date"
                                        input-class="lead-management__profile__details__about__box__input"
                                        style="width: 100%; box-sizing: border-box;"
                                    />
                            </div>

                        </div>
                    </div>
                    <div class="row row--form">
                        <div class="form-element">
                            <div class="four-sm columns">
                                <span class="form-element__label">
                                     Are you open on this date?
                                </span>
                            </div>
                            <div class="eight-sm columns">
                                <select v-model="openStatus">
                                    <option value=""> -- Please select -- </option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="row row--form" v-if="openStatus === 'yes'">
                        <div class="form-element">
                            <div class="four-sm columns">
                                <span class="form-element__label">
                                     Opening Times
                                </span>
                            </div>
                            <div class="four-sm columns">
                                <select v-model="openHour">
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="00">00</option>
                                </select>
                            </div>
                            <div class="four-sm columns">
                                <select v-model="openMin">
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-element">
                            <div class="four-sm columns">
                                <span class="form-element__label">
                                     Closing Times
                                </span>
                            </div>
                            <div class="four-sm columns">
                                 <select v-model="closeHour">
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="00">00</option>
                                </select>
                            </div>
                            <div class="four-sm columns">
                                <select v-model="closeMin">
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                     <table class="table-list__table">
                        <thead>
                            <tr>

                                <th>Date</th>
                                <th>Opening</th>
                                <th>Closing</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item, index) in this.opening" :key="index">
                                <td>{{ formatDate(item.date) }}</td>
                                <td>
                                    <span v-if="item.opening_time">{{ item.opening_time }}</span>
                                    <span v-if="!item.opening_time">Closed</span>
                                </td>
                                <td><span v-if="item.closing_time">{{ item.closing_time }}</span>
                                    <span v-if="!item.closing_time">Closed</span>
                                </td>
                                <td align="right"><i class="fas fa-trash-alt" @click="deleteCustom(item.id)"></i></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>



                </div>


                <div class="modal__footer">
                    <button type="button" class="button button--outline" @click="$parent.displayCustomModal = false;">Close</button>
                    <button class="button" @click="save()">Save</button>

                </div>
            </div>


    </div>
</template>

<script>
import axios from 'axios';
import Datepicker from 'vuejs-datepicker';
import moment from 'moment'

export default {
    props: ['gym'],
    components: { Datepicker },
    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],
            displayProcessingModal: false,
            date: '',
            openStatus: '',
            openHour: '07',
            openMin: '00',
            closeHour: '23',
            closeMin: '00',
            opening: {},
            studio: {},
            disabledDates: {
              to: new Date(Date.now() - 8640000)
            }
        }
    },
    mounted(){
        this.getOpening();

    },
    methods: {
        formatDate (dateString) {
            return moment(String(dateString)).format('Do MMM')
        },
        save() {
            this.uploading = true;

                let year = this.date.getFullYear();
                let month = this.date.getMonth() + 1;
                let day = this.date.getDate();

                if(month < 10) {
                    month = `0${month}`;
                }

                if(day < 10) {
                    day = `0${day}`;
                }

                this.date = `${year}-${month}-${day}`;

                if(this.openStatus === 'yes'){
                    var opening_time = this.openHour+':'+this.openMin;
                    var closing_time = this.closeHour+':'+this.closeMin;
                }else{
                    var opening_time = '';
                    var closing_time = '';
                }

            axios.patch('/api/admin/gyms/' + this.$route.params.id+'/custom-opening-times/save', {
                opening_time: opening_time,
                closing_time: closing_time,
                date: this.date,
                openStatus: this.openStatus

            })
            .then(response => {
                alert('New custom opening time has been saved');
                this.getOpening();
                //this.$parent.displayCustomModal = false;
                this.openHour = '';
                this.closeHour = '';
                this.openMin = '';
                this.closeMin = '';
                this.date = '';
            })
            .catch(error => {

                this.errors = error.response.data.errors;
            });
        },
        deleteCustom(id){
            axios.patch('/api/admin/gyms/' + this.$route.params.id+'/custom-opening-times/delete', {
                id: id,

            })
            .then(response => {
                console.log(response);
                 this.getOpening();
            })
            .catch(error => {

                this.errors = error.response.data.errors;
            });

        },
        getOpening(){

            axios.get('/api/gyms/' + this.$route.params.id+'/custom-opening-times').then(response => {
               this.opening = response.data.opening;
            });

        }

    }
}
</script>
