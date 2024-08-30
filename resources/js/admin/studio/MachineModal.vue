<template>
    <admin-modal :show="true" :title="modalTitle" @close="$emit('close')">
            
        <div class="row row--form">
            <div class="six-sm columns">
                <admin-input v-model="machine.name" label="Name" required />
            </div>
            <div class="six-sm columns">
                <admin-input v-model="machine.location" label="Location" required />
            </div>
        </div>

        <div class="row row--form">
            <div class="six-sm columns">
                <admin-input v-model="machine.status" type="select" label="Status" required>
                    <option value="active">Active</option>
                    <option value="maintenance">Maintenance</option>
                </admin-input>
            </div>
        </div>

        <button 
            slot="footer" 
            class="button" 
            :disabled="!machine.name || !machine.location || !machine.status || loading"
            @click="save">
            Save
        </button>

    </admin-modal>
</template>

<script>
import AdminModal from '../layout/AdminModal.vue'
import AdminInput from '../layout/AdminInput.vue'

export default {
    components: { AdminModal, AdminInput },
    props: { 
        studio: Object, 
        // if editData (for an existing machine) is passed,
        // autofill fields with this data and PATCH existing machine
        // else, fields are empty and POST new machine
        editData: Object
    },
    data () {
        return {
            machine: {
                name: '',
                location: '',
                status: '',
            },
            loading: false
        }
    },
    computed: {
        modalTitle () {
            return this.editData
                ? 'Edit ' + this.editData.name
                : 'Add Nuforma';
        }
    },
    methods: {
        async save () {
            this.editData
                ? await this.edit()
                : await this.add();

            // when finished
            this.$emit('close')
        },

        async add () {
            // Call API
            console.log(this.machine)
        },

        async edit () {
            // Call API
            console.log(this.machine)

        }
    },
    mounted () {
        if (this.editData) {
            this.machine.name = this.editData.name;
            this.machine.location = this.editData.location;
            this.machine.status = this.editData.status;
        }
    }
}
</script>