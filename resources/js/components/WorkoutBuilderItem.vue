<template id="item">
  <div :class="toggled ? 'builder__item builder__item--small builder__item--small--open' : 'builder__item builder__item--small'">
      <div class="builder__item__number">
          <span>{{ number }}</span>
      </div>
        <div class="builder__item__text">
            <img :src="itemData.image" alt="workout image">
            <span>{{itemData.name}}</span>
            <button class="builder__item__toggle" @click="toggleItem">
                <i :class="`fas fa-${toggled ? 'minus' : 'plus'}`"/>
            </button>
        </div>
      <transition name="fade">
      <div class="toggle-item" v-show="toggled">
            <div v-if="duration" class="toggle-item__increment">
                Duration (secs)
                <div class="toggle-item__increment__buttons">
                    <button class="toggle-item__increment__button" @click.prevent="decrementDurationCounter()">-</button>
                        {{durationValue}}
                    <button class="toggle-item__increment__button" @click.prevent="incrementDurationCounter()">+</button>
                </div>
            </div>
            <div v-if="!duration" class="toggle-item__increment">
                <div class="toggle-item__increment__wrapper">
                    Sets
                    <div class="toggle-item__increment__buttons">
                        <button class="toggle-item__increment__button" @click.prevent="decrementSetsCounter()">-</button>
                            {{setsValue}}
                        <button class="toggle-item__increment__button" @click.prevent="incrementSetsCounter()">+</button>
                    </div>
                </div>
                <div class="toggle-item__increment__wrapper">
                    Reps
                    <div class="toggle-item__increment__buttons">
                        <button class="toggle-item__increment__button" @click.prevent="decrementRepsCounter()">-</button>
                            {{repsValue}}
                        <button class="toggle-item__increment__button" @click.prevent="incrementRepsCounter()">+</button>
                    </div>
                </div>
            </div>
            <label class="switch toggle-item__increment__switch" >
                <div class="toggle-item__increment__label">{{ duration ? 'Duration' : 'Sets and Reps' }}</div>
                <input v-model="duration" type="checkbox" v-on:change="toggleWorkoutType()">
                <span class="slider round"></span>
            </label>
            <div class="break"></div>
            <div class="toggle-item__increment" style="flex-grow:1">
                <div v-if="duration">
                    Rest (secs)
                </div>
                <div v-if="!duration">
                    Rest Per Set (secs)
                </div>
                <div class="toggle-item__increment__buttons">
                    <button class="toggle-item__increment__button" @click.prevent="decrementRestCounter()">-</button>
                        {{restValue}}
                    <button class="toggle-item__increment__button" @click.prevent="incrementRestCounter()">+</button>
                </div>
            </div>
      </div>
      </transition>
  </div>
</template>

<script>
export default {
    template: '#item',
    props: ['itemData', 'number'],
    data: function() {
    return {
        toggled: false,
        restValue: 0,
        duration: true,
        label: '',
        durationValue: 60,
        setsValue: 0,
        repsValue: 0,
        }
    },

    mounted() {
        if(this.itemData.pivot !== undefined){
            if(this.itemData.pivot.store_workout_type == "duration"){

                this.toggled = true;
                this.restValue = this.itemData.pivot.custom_rest;
                this.durationValue = this.itemData.pivot.custom_duration ? this.itemData.pivot.custom_duration : this.durationValue;
            }

            if(this.itemData.pivot.store_workout_type == "setsReps"){

                this.toggled = true;
                this.duration = false;
                this.restValue = this.itemData.pivot.custom_rest;
                this.setsValue = this.itemData.pivot.custom_sets;
                this.repsValue = this.itemData.pivot.custom_reps;
            }

        }

        this.updateWorkoutStoreType();
        this.storeCustomWorkoutValues();
    },

    methods: {
        incrementDurationCounter: function() {
            this.durationValue += 30;
            this.updateDuration();
        },
        decrementDurationCounter: function() {
            if (this.durationValue === 0) return
            this.durationValue -= 30;
            this.updateDuration();
        },
        incrementRestCounter: function() {
            this.restValue += 10;
            this.updateRest();
        },
        decrementRestCounter: function() {
            if (this.restValue === 0) return
            this.restValue -= 10;
            this.updateRest();
        },
        incrementSetsCounter: function() {
            this.setsValue += 1;
            this.updateSets();
        },
        decrementSetsCounter: function() {
            if (this.setsValue === 0) return
            this.setsValue -= 1;
            this.updateSets();
        },
        incrementRepsCounter: function() {
            this.repsValue += 1;
            this.updateReps();
        },
        decrementRepsCounter: function() {
            if (this.repsValue === 0) return
            this.repsValue -= 1;
            this.updateReps();
        },
        toggleItem: function() {
            this.toggled = !this.toggled;
            this.storeCustomWorkoutValues();
            this.updateWorkoutStoreType();
            this.updateDuration();
        },
        toggleWorkoutType: function() {
            this.updateWorkoutStoreType();
        },

        updateDuration(){
            this.$emit('updateDuration', this.durationValue);
        },
        updateRest(){
            this.$emit('updateRest', this.restValue);
        },
        updateReps(){
            this.$emit('updateReps', this.repsValue);
        },
        updateSets(){
            this.$emit('updateSets', this.setsValue);
        },
        updateWorkoutStoreType(){
            if(this.toggled){
                if (this.duration === false ){
                    this.$emit('updateWorkoutStoreType', 'setsReps');
                } else {
                    this.$emit('updateWorkoutStoreType', 'duration');
                }
            } else{
                this.$emit('updateWorkoutStoreType', null);
                this.repsValue = this.restValue = this.setsValue = 0;
                this.updateReps();
                this.updateRest();
                this.updateSets();
            }

        },
        storeCustomWorkoutValues(){
            if (this.toggled) {
                this.$emit('storeCustomWorkoutValues', true);
            } else {
                this.$emit('storeCustomWorkoutValues', false);
            }

        }
    }

}
</script>
