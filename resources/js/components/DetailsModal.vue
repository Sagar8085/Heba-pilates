<template>
    <div>
        <div class="modal modal--active">
            <div class="modal__box modal__box--small modal__body">
                <h2 class="modal__title">Details Required</h2>

                <p>For the optimal experience using our service, please enter the following details</p>

                <div class="modal__divider"></div>

                <section class="calories__section">
                    <button-options
                        v-model="system"
                        :options="systemOptions"
                        :columns="2"
                        name="system" />
                    <div :class="{ 'form-row': true, 'form-row--cols': system == 'imperial'  }">
                        <form-input v-model="weight" type="number" :label="system == 'imperial' ? 'Weight (lb)' : 'Weight (kg)'" />
                        <div v-if="system == 'imperial'" class="form-row">
                            <form-input
                                v-model="height.imperial.feet"
                                type="number"
                                label="Height (ft)" />
                            <form-input
                                v-model="height.imperial.inches"
                                type="number"
                                label="Height (in)" />
                        </div>
                        <form-input
                            v-else
                            v-model="height.metric"
                            type="number"
                            label="Height (cm)"
                         />
                        </div>
                </section>


                <div class="modal__divider"></div>

                <button class="button button--outline" @click="$emit('close')">Skip for now</button>
                <button class="button button" @click="updateDetails()">Update</button>
            </div>
        </div>
    </div>
</template>

<script>
import ButtonOptions from '../components/ButtonOptions.vue';
import FormInput from '../components/FormInput.vue';

export default {
    components: {  ButtonOptions, FormInput },

    data() {
        return {
            metric: true,
            height: {
                metric: null,
                imperial: {
                    feet: null,
                    inches: null
                }
            },
            weight: null,
            system: 'metric',
            systemOptions: [
                {
                    label: 'Metric',
                    value: 'metric'
                },
                {
                    label: 'Imperial',
                    value: 'imperial'
                }
            ],
        }
    },
    methods: {
        updateDetails(){
            let height = 0, weight = 0;
            if (this.system == 'metric') {
                height = this.height.metric;
                weight = this.weight;

            } else {
                height = ((this.height.imperial.feet * 12) + this.height.imperial.inches) * 2.54;
                weight = this.weight * 0.453592;
            }

            axios.post('/api/user/details/heightweight', {
                weight: weight,
                height: height
            }).then(response => {
                this.$emit('complete');
            }).catch(error => {
                console.log(error);
            })
        }
    }
}
</script>
