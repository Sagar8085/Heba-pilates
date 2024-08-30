<template>
    <div class="auth__wrapper">
        <form class="account wrapper" @submit.prevent="update">
            <h1>Choose Studio</h1>

            <div class="account-info account-info--col">
                <label class="account-info__label">Current Studio</label>
                <span class="account-info__content">{{this.authUser.gym.name}}</span>
            </div>
                <select v-model="home_studio_id">
                    <option value="Choose studio" selected >-- Choose studio --</option>
                    <option :value="gym.id" v-for="gym in gyms">{{ gym.name }}</option>
                </select>
            <!-- <div class="auth__group">
                  <div class="dropdown">
                    <select name="Studio" id="Studio" v-model="studioName">
                    <option value="">Choose Studio</option>
                    <option value="Chelsea">Chelsea</option>
                    <option value="Clapham">Clapham</option>
                    <option value="Windsor">Windsor</option>
                    <option value="Beaconsfield">Beaconsfield</option>
                    </select>
                    </div>
            </div> -->
 <!-- <select name="customerName" id="" v-on:change="onChange($event)">
         <option value="1">Jan</option>
         <option value="2">Doe</option>
         <option value="3">Khan</option>
   </select> -->

            <div class="account-info account-info--inline">
                <router-link to="/myaccount" class="button button--outline">Cancel</router-link>
                <button class="button" :disabled="saveDisabled" @click.prevent="update">Save</button>
            </div>

        </form>
    </div>
</template>

<script>
    import FormInput from '../../components/FormInput.vue'
    import axios from 'axios';
    export default {
        props: {
            authUser: Object,
        },
        components: {
            FormInput
        },

        data () {
            return {
                gyms: [],
                studioName: '',
            };
        },

        // computed: {
        //     saveDisabled () {
        //         return this.phoneNumber.length == 0;
        //     }
        // },
         mounted() {
        this.loadGyms();
    },

        methods: {
      
       loadGyms() {
            axios.get('/api/admin/gyms').then(response => {
                console.log("gt=ymmmmm>=", response.data)
                this.gyms = response.data;
                
            })
            .catch(error => {
                console.log('Unable to load gyms at this time.')
            });
        },

        filterStudio(studio)
        {
            
        },

        update()
        {
            console.log("passssss",this.authUser.id)
            
            
            
        //       axios.post('/api/admin/members/' + this.$route.params.id, {
        //         first_name: this.first_name,
        //         last_name: this.last_name,
        //         email: this.email,
        //         phone_number: this.phone_number,
        //         new_password: this.new_password,
        //         home_studio_id: this.home_studio_id
        //     }).then(response => {
        //         alert('Member Updated');
        //         this.$emit('updated')
        //     })
        //     .catch(error => {
        //         console.log('ERROR');
        //         console.log(error);
        //     })
        //     .finally(() => this.loading = false);
        // },
        }



        //     update(e) {

        // console.log('name ',this.studioName );
        // localStorage.setItem("studioName", this.studioName);

        // this.$router.push('/myaccount')
        //         // console.log(this.authUser)


        //         // if (this.saveDisabled) return;
        //             // console.log("hello....!!!!")


        //         // axios.patch('/api/account/phone', {
        //         //     studioName: this.studioName,
        //         // }).then(response => {
        //         //     this.$router.push('/myaccount')
        //         // }).catch(error => {
        //         //     console.log('ERROR');
        //         //     this.errors = error.response.data.errors;
        //         // });
        //     }
        }
    };
</script>
