<template>
    <div>
            <div class="modal__box">
                <div class="modal__header">
                    <h3 class="modal__title">Set Opening Hours for {{ this.gym.name }}</h3>
                    <p>Please leave blank if not open.</p>
                </div>

                <div class="modal__body">
                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                     Monday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="mondayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="mondayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="mondaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="mondaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="30">30</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                    Tuesday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="tuesdayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="tuesdayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="tuesdaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="tuesdaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                     Wednesday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="wednesdayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="wednesdayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="wednesdaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="wednesdaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>


                        </div>
                    </div>

                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                    Thursday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="thursdayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="thursdayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="thursdaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="thursdaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>


                        </div>
                    </div>
                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                     Friday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="fridayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="fridayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="fridaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="fridaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>

                        </div>

                    </div>
                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                     Saturday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="saturdayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="saturdayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="saturdaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="saturdaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>


                        </div>
                    </div>
                    <div class="row row--form">
                        <div class="form-element">
                            <div class="three-sm columns">
                                <span class="form-element__label">
                                     Sunday
                                </span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="sundayopenHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="sundayopenMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                             <div class="one-sm columns">
                                <span class="form-element__label">to</span>
                            </div>
                            <div class="two-sm columns">
                                <select v-model="sundaycloseHour" class="select-times">
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
                            <div class="two-sm columns">
                                  <select v-model="sundaycloseMin" class="select-times">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>


                        </div>
                    </div>

                </div>

                <div class="modal__footer">
                    <button type="button" class="button button--outline" @click="$emit('cancel')">Cancel</button>
                    <button class="button" @click="save()">Save</button>

                </div>
            </div>


    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['gym'],
    data() {
        return {
            uploading: false,
            uploadPercentage: 0,
            errors: [],
            displayProcessingModal: false,
            mondayopenHour: '07',
            mondayopenMin: '00',
            mondaycloseHour: '23',
            mondaycloseMin: '00',
            tuesdayopenHour: '07',
            tuesdaycloseHour: '23',
            tuesdayopenMin: '00',
            tuesdaycloseMin: '00',
            wednesdayopenHour: '07',
            wednesdaycloseHour: '23',
            wednesdayopenMin: '00',
            wednesdaycloseMin: '00',
            thursdayopenHour: '07',
            thursdaycloseHour: '23',
            thursdayopenMin: '00',
            thursdaycloseMin: '00',
            fridayopenHour: '07',
            fridaycloseHour: '23',
            fridayopenMin: '00',
            fridaycloseMin: '00',
            saturdayopenHour: '07',
            saturdaycloseHour: '23',
            saturdayopenMin: '00',
            saturdaycloseMin: '00',
            sundayopenHour: '07',
            sundaycloseHour: '23',
            sundayopenMin: '00',
            sundaycloseMin: '00',
            studio: {},
        }
    },
    mounted(){
        this.getOpening();
    },
    methods: {
        save() {
            this.uploading = true;

            axios.patch('/api/admin/gyms/opening-times/' + this.$route.params.id, {
                mondayopen: this.mondayopenHour+':'+this.mondayopenMin,
                mondayclose: this.mondaycloseHour+':'+this.mondaycloseMin,
                tuesdayopen: this.tuesdayopenHour+':'+this.tuesdayopenMin,
                tuesdayclose: this.tuesdaycloseHour+':'+this.tuesdaycloseMin,
                wednesdayopen: this.wednesdayopenHour+':'+this.wednesdayopenMin,
                wednesdayclose: this.wednesdaycloseHour+':'+this.wednesdaycloseMin,
                thursdayopen: this.thursdayopenHour+':'+this.thursdayopenMin,
                thursdayclose: this.thursdaycloseHour+':'+this.thursdaycloseMin,
                fridayopen: this.fridayopenHour+':'+this.fridayopenMin,
                fridayclose: this.fridaycloseHour+':'+this.fridaycloseMin,
                saturdayopen: this.saturdayopenHour+':'+this.saturdayopenMin,
                saturdayclose: this.saturdaycloseHour+':'+this.saturdaycloseMin,
                sundayopen: this.sundayopenHour+':'+this.sundayopenMin,
                sundayclose: this.sundaycloseHour+':'+this.sundaycloseMin
            })
            .then(response => {
                console.log(response);
                this.$parent.displayOpenModal = false;
            })
            .catch(error => {

                this.errors = error.response.data.errors;
            });
        },
        getOpening(){

            axios.get('/api/gyms/' + this.$route.params.id).then(response => {

                var mondayOpen = response.data.operating_hours.monday[0].split(":");
                var mondayClose = response.data.operating_hours.monday[1].split(":");

                var tuesdayOpen = response.data.operating_hours.tuesday[0].split(":");
                var tuesdayClose = response.data.operating_hours.tuesday[1].split(":");

                var wednesdayOpen = response.data.operating_hours.wednesday[0].split(":");
                var wednesdayClose = response.data.operating_hours.wednesday[1].split(":");

                var thursdayOpen = response.data.operating_hours.thursday[0].split(":");
                var thursdayClose = response.data.operating_hours.thursday[1].split(":");

                var fridayOpen = response.data.operating_hours.friday[0].split(":");
                var fridayClose = response.data.operating_hours.friday[1].split(":");

                var saturdayOpen = response.data.operating_hours.saturday[0].split(":");
                var saturdayClose = response.data.operating_hours.saturday[1].split(":");

                var sundayOpen = response.data.operating_hours.sunday[0].split(":");
                var sundayClose = response.data.operating_hours.sunday[1].split(":");

                this.mondayopenHour = mondayOpen[0];
                this.mondaycloseHour = mondayClose[0];
                this.tuesdayopenHour = tuesdayOpen[0];
                this.tuesdaycloseHour = tuesdayClose[0];
                this.wednesdayopenHour = wednesdayOpen[0];
                this.wednesdaycloseHour = wednesdayClose[0];
                this.thursdayopenHour = thursdayOpen[0];
                this.thursdaycloseHour = thursdayClose[0];
                this.fridayopenHour = fridayOpen[0];
                this.fridaycloseHour = fridayClose[0];
                this.saturdayopenHour = saturdayOpen[0];
                this.saturdaycloseHour = saturdayClose[0];
                this.sundayopenHour = sundayOpen[0];
                this.sundaycloseHour = sundayClose[0];

                this.mondayopenMin = mondayOpen[1];
                this.mondaycloseMin = mondayClose[1];
                this.tuesdayopenMin = tuesdayOpen[1];
                this.tuesdaycloseMin = tuesdayClose[1];
                this.wednesdayopenMin = wednesdayOpen[1];
                this.wednesdaycloseMin = wednesdayClose[1];
                this.thursdayopenMin = thursdayOpen[1];
                this.thursdaycloseMin = thursdayClose[1];
                this.fridayopenMin = fridayOpen[1];
                this.fridaycloseMin = fridayClose[1];
                this.saturdayopenMin = saturdayOpen[1];
                this.saturdaycloseMin = saturdayClose[1];
                this.sundayopenMin = sundayOpen[1];
                this.sundaycloseMin = sundayClose[1];


            });

        }

    }
}
</script>
