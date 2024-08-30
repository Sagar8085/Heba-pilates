<template>
    <div>
        <PageHeader title="Nuforma Machines" :subtitle="studio.name" icon="event">
            <button class="button" @click="showAddMachineModal = true">Add Nuforma</button>
        </PageHeader>
        <tab-list active="machines" :tabs="tabs" />

        <div class="page-content">
            <data-table
                title="Nuforma Machines"
                :cols="machineHeadings"
                :rows="studio.reformers">

                <template v-slot:cell-name="{ item, cell }">
                    <span v-if="item.status == 'maintenance'">{{ cell }}</span>
                    <router-link v-else :to="machineLink(item)">
                        {{ cell }}
                    </router-link>
                </template>

                <template v-slot:cell-status="{ cell }">
                    <span :class="'status status--' + cell">
                        {{ cell }}
                    </span>
                </template>

                <template v-slot:cell-actions="{ item }">
                    <span v-if="item.status == 'maintenance'"></span>
                    <router-link
                        v-else
                        class="button button--outline"
                        :to="machineLink(item)">
                        View Bookings
                    </router-link>
                </template>

            </data-table>
        </div>

        <MachineModal
            v-if="showAddMachineModal"
            :studio="studio"
            @close="showAddMachineModal = false" />
    </div>
</template>

<script>
import PageHeader from '../layout/PageHeader.vue'
import TabList from '../layout/TabList.vue'
import DataTable from '../layout/DataTable.vue'
import MachineModal from './MachineModal.vue'
import axios from 'axios';

export default {
    components: { PageHeader, TabList, DataTable, MachineModal },
    data () {
        return {
            tabs: [
                {
                    key: 'upcoming', name: 'All Upcoming Reservations',
                    link: `/admin/studio/${this.$route.params.studio_id}`
                },
                {
                    key: 'machines', name: 'Nurforma Machines',
                    link: `/admin/studio/${this.$route.params.studio_id}/machines`
                }
            ],
            studio: {},
            machineHeadings: {
                name: 'Name',
                status: 'Status',
                actions: ''
            },

            showAddMachineModal: false,
            newMachine: {
                name: '',
                location: '',
                status: ''
            }
        }
    },
    watch: {
        '$route.params' () {
            this.getStudio();
        }
    },
    methods: {
        machineLink ({ id }) {
            return {
                name: 'StudioMachineSingle',
                params: {
                    studio_id: this.$route.params.studio_id,
                    machine_id: id
                }
            };
        },
        getStudio () {
            axios.get('/api/admin/gyms/' + this.$route.params.studio_id).then(response => {
                this.studio = response.data;
            });
        }
    },
    mounted () {
        this.getStudio();
    }
}
</script>
