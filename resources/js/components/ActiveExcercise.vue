<template>
  <div style="width: 100%;">
    <div class="start-workout wrapper">
        <div class="start-workout__col">
        <h3 class="start-workout__subtitle">{{section_title}} - Exercise {{selected_exercise_number}} of {{count}}</h3>
        <h1 class="start-workout__title">{{current_exercise_title}} <span v-if="this.selected_exercise.resting"> -- Rest Break</span>{{current_exercise_set}}</h1>
        <div v-if="this.selected_exercise.resting" class="video-container video-container--rest">
            <h2 class="video-container--rest__title">NOW REST FOR</h2>
            <span class="video-container--rest__countdown">{{prettyCountDown}}</span>
        </div>
        <div v-else class="video-container">
            <video id="video" width="650" :autoplay="autoplay" muted loop="true" :key="video" :poster="this.selected_exercise.image">
                <source :src="video" type="video/mp4">
            </video>
        </div>
        <div class="start-workout__row">
            <div class="start-workout__row__rating">
                <p class="start-workout__row__rating__title">Rate this exercise</p>
                <button
                    :class="{ 'start-workout__row__rating__thumb': true, 'start-workout__row__rating__thumb--up': this.liked }"
                    @click="rateExcercise('like')">
                    <i class="fas fa-thumbs-up"></i>
                </button>
                or
                <button
                    :class="{ 'start-workout__row__rating__thumb': true, 'start-workout__row__rating__thumb--down': this.liked == false }"
                    @click="rateExcercise('dislike')">
                    <i class="fas fa-thumbs-down"></i>
                </button>
            </div>
            <div class="start-workout__duration">{{prettyCountDown}}</div>
        </div>
    </div>
    <div class="start-workout__col start-workout__col--right">
        <div class="start-workout__tab start-workout__tab--top">
            <ul>
                <li class="active"><a>Up Next</a></li>
            </ul>
        </div>
        <div v-if="next_exercise.name == 'Complete' || next_exercise.name == 'Rest'" class="start-workout__tab__breakdown start-workout__tab__breakdown--static">
            <div class="start-workout__tab__breakdown__title">
                {{next_exercise.name}}
            </div>
            <div v-if="next_exercise.name !== 'Complete'">
                {{next_exercise.duration}} (secs)
            </div>
        </div>
        <div v-else class="start-workout__tab__breakdown" @click="changeExercise(next_exercise.number)">
            <div class="start-workout__tab__breakdown__title">
                {{next_exercise.name}}
            </div>
            <div>
                {{next_exercise.duration}} (secs)
            </div>
        </div>
        <div class="start-workout__tab">
            <ul>
                <li :class="this.tab === 'breakdown' ? 'active' : ''"><a @click="tab = 'breakdown'">Breakdown</a></li>
                <li :class="this.tab === 'guide' ? 'active' : ''"><a @click="tab = 'guide'">Guide</a></li>
            </ul>
        </div>
        <div v-if="tab === 'breakdown'">
            <div
                v-for="exercise in exercises"
                :key="exercise.number"
                :class="{ 'start-workout__tab__breakdown': true, 'start-workout__tab__breakdown--active': selected_exercise.number == exercise.number }"
                @click="changeExercise(exercise.number)">
                <div class="start-workout__tab__breakdown__title">
                    {{exercise.name}}
                </div>
                <div v-if="exercise.pivot.store_workout_type != 'setsReps'">
                    {{exercise.duration}} (secs)
                </div>
                <div v-if="exercise.pivot.store_workout_type == 'setsReps'">
                    {{exercise.pivot.custom_sets}} Sets
                    {{exercise.pivot.custom_reps}} Reps
                </div>
            </div>
        </div>
        <div v-if="tab === 'guide'">
            <div class="start-workout__tab__guide">
                <ol v-if="selected_exercise.steps.length != 0">
                    <li v-for="(step, index) in selected_exercise.steps" :key="index">
                        {{step.text}}
                    </li>
                </ol>
                <p v-else>No guide has been included for this exercise.</p>
            </div>
        </div>
    </div>
    </div>
    <div class="start-workout__button-group wrapper sticky-panel sticky-panel--bottom">
        <button @click="restart()" class="button button--icon"><i class="fas fa-undo"></i></button>
        <button @click="playPause()" class="button button--icon"><i :class="paused ? 'fas fa-play' : 'fas fa-pause'"></i></button>
        <button v-on:click="completeExercise()" class="button button--green">Excercise complete</button>
    </div>
  </div>
</template>

<script>
export default {
    props: ['itemData'],
    data: function() {
    return {
        liked: null,
        paused: false,
        tab: 'breakdown',
        count: 0,
        autoplay: false,
        countDown: null,
        prettyCountDown: null,
        current_exercise_title: null,
        current_exercise_set: null,
        exercises: [],
        section_title: 'Warming Up',
        selected_exercise_number: 1,
        selected_exercise: {},
        next_exercise: {
            name: 'Side Lunge',
            duration: 300
        },
        vid: null,
        video: null,
        }
    },

    mounted() {

        const that = this;
        this.exercises = JSON.parse(JSON.stringify(this.itemData.warmupExercises.concat(this.itemData.trainingExercises.concat(this.itemData.cooldownExercises))));
        this.count = this.exercises.length;
        this.editExercise();

        this.selected_exercise = this.exercises[0];
        this.video = this.selected_exercise.video;
        this.updateSelectedVideo();

        setTimeout(function(){
            that.changeExercise(1);
            that.playPause();
            that.autoplay = true;
        }, 5 * 1000);
    },

    methods: {
        completeExercise(){
            if (this.next_exercise.name !== 'Complete')
                return this.changeExercise(this.selected_exercise_number + 1);

            axios.post('/api/workout/save/stats', {
                workout_id: this.itemData.id,
                kcals_burnt: this.itemData.kcals_burnt,
                duration_secs: this.itemData.durationSecs,
                num_exercises: this.count,
            })
            this.$emit('complete');
        },
        rateExcercise(x) {
            if (x === 'like') {
                this.liked = 'liked';
                console.log('Added workout to favourites');
                axios.post('/api/workout/save/favourite', {
                    add_to_fav : true,
                    workout_id : this.itemData.id,
                }).then(response => {
                    console.log(response);
                }).catch(error => {
                    console.log(error);
                });
            } else if (x === 'dislike') {
                this.liked = false;
                console.log('Removed workout from favourites');
                axios.post('/api/workout/save/favourite', {
                    workout_id : this.itemData.id,
                }).then(response => {
                    console.log(response);
                }).catch(error => {
                    console.log(error);
                });
            }
        },
        editExercise(){
            this.exercises.forEach((item, i) => {
                if(item.pivot.store_workout_type == "setsReps"){
                    item.breakdown = item.pivot.custom_sets;
                }
            });

        },
        playPause() {
            this.updateSelectedVideo();
            if (this.selected_exercise.resting){
                if (this.paused) {
                    this.restPlay();
                    this.countDownTimer();
                } else {
                    this.restPause()
                }
            } else {
                if (this.vid.paused) {
                    this.play();
                }
                else {
                    this.pause();
                }
            }
        },
        restPause(){
            this.paused = true;
            this.autoplay = false;
        },
        restPlay(){
            this.paused = false;
            this.autoplay = true;
        },
        play(){
            this.vid.play();
            this.paused = false;
            this.autoplay = true;
            this.countDownTimer();
        },
        pause(){
            this.vid.pause();
            this.paused = true;
            this.autoplay = false;
        },
        restart() {
            if (!this.selected_exercise.resting) {
                this.updateSelectedVideo();
                this.pause();
                this.vid.currentTime = 0;
            }
            this.resetCountDown();
        },

        changeExercise(exercise_number) {
            this.selected_exercise_number = exercise_number;
            this.selected_exercise = JSON.parse(JSON.stringify(this.exercises[this.selected_exercise_number - 1]));
            this.current_exercise_title = this.selected_exercise.name;
            this.video = this.selected_exercise.video;
            this.updateSection();
            this.updateWorkoutProgress();
            this.beautifyCountdown();
        },

        incrementSet(){
            if(this.selected_exercise.current_set === undefined){
                this.selected_exercise.current_set = 0;
            }

            this.selected_exercise.resting = false;
            this.selected_exercise.current_set += 1;
            this.countDown = this.selected_exercise.duration_per_rep * this.selected_exercise.pivot.custom_reps;
            this.current_exercise_set = '-- Set ' + this.selected_exercise.current_set + ' of ' + this.selected_exercise.pivot.custom_sets
        },

        updateWorkoutProgress(){
            this.current_exercise_set = null;

            // check if duration or reps
            if(this.selected_exercise.pivot.store_workout_type == 'setsReps'){
                // check more sets remain in exercise
                if(this.selected_exercise.current_set < this.selected_exercise.pivot.custom_sets || this.selected_exercise.current_set === undefined){
                    // check if in the resting stage of a set
                    if(this.selected_exercise.resting){
                        this.incrementSet();
                    } else if (this.selected_exercise.complete) {
                        // check if there is a rest for the exercise, if so update to next set
                        if(this.selected_exercise.pivot.custom_rest > 0) {
                            this.selected_exercise.resting = true;
                            this.countDown = this.selected_exercise.pivot.custom_rest;
                        } else {
                            this.incrementSet();
                        }
                    } else {
                        this.incrementSet();
                        this.selected_exercise.complete = true;
                    }
                // if no sets remain in exercise
                } else {
                    // check if resting, if so change to next exercise, otherwise check rest exists and then move
                    if (this.selected_exercise.resting) {
                        this.changeExercise(this.selected_exercise_number + 1);
                    } else {
                        if(this.selected_exercise.pivot.custom_rest > 0) {
                            this.selected_exercise.resting = true;
                            this.countDown = this.selected_exercise.pivot.custom_rest;
                        } else {
                            this.changeExercise(this.selected_exercise_number + 1);
                        }
                    }
                }




            } else if (this.selected_exercise.pivot.store_workout_type == 'duration') {
                if (this.selected_exercise.resting) {
                    this.changeExercise(this.selected_exercise_number + 1);
                } else {
                    // .complete indicates whether the exercise has been watched, otherwise skips right to rest
                    if (!this.selected_exercise.complete || this.selected_exercise.complete === undefined) {
                        this.selected_exercise.resting = false;
                        this.selected_exercise.complete = true;
                        this.countDown = this.selected_exercise.pivot.custom_duration;
                    } else {
                        if(this.selected_exercise.pivot.custom_rest > 0) {
                            this.selected_exercise.resting = true;
                            this.countDown = this.selected_exercise.pivot.custom_rest;
                        } else {
                            this.changeExercise(this.selected_exercise_number + 1);
                        }
                    }

                }



            } else {
                if (this.selected_exercise.complete){
                    this.changeExercise(this.selected_exercise_number + 1);
                } else {
                    this.resetCountDown();
                    this.selected_exercise.complete = true;
                }

            }
            this.updateNext(this.selected_exercise, false);
        },

        updateNext(selected_exercise, looped){
            if (selected_exercise.pivot.store_workout_type == 'setsReps' ){
                if (selected_exercise.current_set < selected_exercise.pivot.custom_sets || selected_exercise.current_set === undefined) {
                    if (selected_exercise.pivot.custom_rest > 0 ){
                        if (selected_exercise.resting || selected_exercise.resting === undefined) {
                            if(looped){
                                selected_exercise.name = selected_exercise.name;
                                selected_exercise.duration = selected_exercise.duration_per_rep * selected_exercise.pivot.custom_reps;
                            } else {
                                this.next_exercise.name = selected_exercise.name;
                                this.next_exercise.duration = selected_exercise.duration_per_rep * selected_exercise.pivot.custom_reps;
                            }
                        } else {
                            this.next_exercise.name = 'Rest';
                            this.next_exercise.duration = selected_exercise.pivot.custom_rest;
                        }
                    } else {
                        // Parsing Json to remove reference to OG Array
                        this.next_exercise = selected_exercise;
                        this.next_exercise.duration = selected_exercise.duration_per_rep * selected_exercise.pivot.custom_reps;
                    }
                } else {
                    if (selected_exercise.pivot.custom_rest > 0 ){
                        if (selected_exercise.resting) {
                            // Parsing Json to remove reference to OG Array
                            this.next_exercise = this.updateNext(JSON.parse(JSON.stringify(this.exercises[this.selected_exercise_number])), true)
                        } else {
                            if (this.selected_exercise_number == this.count) {
                                this.next_exercise.name = "Complete";
                            } else {
                                this.next_exercise.name = 'Rest';
                                this.next_exercise.duration = selected_exercise.pivot.custom_rest;
                            }
                        }
                    } else {
                        this.next_exercise = JSON.parse(JSON.stringify(this.exercises[this.selected_exercise_number]));

                        if (this.next_exercise.pivot.store_workout_type == 'setsReps') {
                            this.next_exercise.duration = this.next_exercise.pivot.custom_reps * this.next_exercise.duration_per_rep;
                        } else if (this.next_exercise.pivot.store_workout_type == 'duration'){
                            this.next_exercise.duration = this.next_exercise.pivot.custom_duration;
                        }
                    }
                }



            } else if (selected_exercise.pivot.store_workout_type == 'duration' ) {

                if (selected_exercise.pivot.custom_rest > 0 || selected_exercise.pivot.custom_rest === undefined){
                    if (this.selected_exercise.resting || this.selected_exercise.pivot.custom_rest === 0) {
                        selected_exercise = this.next_exercise = JSON.parse(JSON.stringify(this.exercises[this.selected_exercise_number]));

                        if (selected_exercise.pivot.custom_duration) {
                            selected_exercise.duration = selected_exercise.pivot.custom_duration;
                        }

                        if (this.next_exercise.pivot.custom_duration) {
                            this.next_exercise.duration = selected_exercise.pivot.custom_duration;
                        }

                    } else {
                        if(looped){
                            selected_exercise.name = 'Rest';
                            selected_exercise.duration = selected_exercise.pivot.custom_rest;
                        } else {
                            this.next_exercise.name = 'Rest';
                            this.next_exercise.duration = selected_exercise.pivot.custom_rest;
                        }
                    }
                } else {
                    this.next_exercise = JSON.parse(JSON.stringify(this.exercises[this.selected_exercise_number]));

                    if (this.next_exercise.pivot.store_workout_type == 'setsReps') {
                        this.next_exercise.duration = this.next_exercise.pivot.custom_reps * this.next_exercise.duration_per_rep;
                    } else if (this.next_exercise.pivot.store_workout_type == 'duration'){
                        this.next_exercise.duration = this.next_exercise.pivot.custom_duration;
                    }
                }



            } else {
                if(this.exercises[this.selected_exercise_number] !== undefined) {
                    this.next_exercise = JSON.parse(JSON.stringify(this.exercises[this.selected_exercise_number]));
                } else {
                    this.next_exercise.name = "Complete";
                }

                if (this.next_exercise.pivot.store_workout_type == 'setsReps') {
                    this.next_exercise.duration = this.next_exercise.pivot.custom_reps * this.next_exercise.duration_per_rep;
                } else if (this.next_exercise.pivot.store_workout_type == 'duration'){
                    this.next_exercise.duration = this.next_exercise.pivot.custom_duration;
                }
            }
            return selected_exercise;
        },

        countDownTimer() {
            if(this.countDown >= 0){
                setTimeout(() => {
                    if(this.paused == false){
                        this.countDown -= 1;
                        this.countDownTimer()
                    }
                }, 1000)
            } else if ((this.countDown < 0 && this.selected_exercise_number < this.count) || this.selected_exercise.current_set < this.selected_exercise.pivot.custom_sets) {
                this.updateWorkoutProgress();
                this.autoplay = true;
                this.countDownTimer();

            }else if (this.selected_exercise_number == this.count) {
                this.completeExercise();
            }

            this.beautifyCountdown();
        },
        beautifyCountdown(){
            var minutes = ~~(this.countDown / 60),
                seconds = this.countDown - minutes * 60;

                this.prettyCountDown = this.str_pad_left(minutes,'0',2)+':'+this.str_pad_left(seconds,'0',2);

        },
        str_pad_left(string,pad,length) {
            return (new Array(length+1).join(pad)+string).slice(-length);
        },
        resetCountDown(){
            if (this.selected_exercise.resting) {
                this.countDown = this.selected_exercise.pivot.custom_rest;
            } else {
                if (this.selected_exercise.pivot.store_workout_type == 'setsReps'){
                    this.countDown = this.selected_exercise.pivot.custom_reps * this.selected_exercise.duration_per_rep;
                } else if (this.selected_exercise.pivot.store_workout_type == 'duration') {
                    this.countDown = this.selected_exercise.pivot.custom_duration;
                } else {
                    this.countDown = this.selected_exercise.duration;
                }
            }
            this.beautifyCountdown();
        },
        updateSelectedVideo(){
            this.vid = document.getElementById("video");
        },
        updateSection(){
            switch (this.selected_exercise.section) {
                case 1:
                    this.section_title = 'Warming Up'
                    break;
                case 2:
                    this.section_title = 'Training'
                    break;
                case 3:
                    this.section_title = 'Cooling Down'
                    break;
                default:
                    this.section_title = 'Warming Up'
            }
        },
    }

}
</script>
