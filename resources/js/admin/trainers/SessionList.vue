<template>
    <div class="session-list__container">
        <h3 class="page__title--secondary page__title--red">
            <i class="material-icons">assignment</i>
            Upcoming Sessions • {{ sessions.length }}
        </h3>
        <ul class="session-list">
            <li v-for="(session, index) in sessions" :key="session.id" class="session-list__item">
                <section class="session-list__item__section session-list__item__section--header">
                    <div class="session-list__item__subsection session-list__item__subsection--row">
                        <span class="session-list__item__image">{{ getInitials(session.member) }}</span>
                        <div class="session-list__item__section__content">
                            <h4 class="session-list__item__heading">{{ session.member.name }}</h4>
                            <p class="session-list__item__text">Session ID: {{ session.id }}</p>
                        </div>
                    </div>
                    <div class="session-list__item__subsection">
                        <h4 class="session-list__item__heading">{{ session.package.name }}</h4>
                        <p class="session-list__item__text">
                            {{ `x${session.package.total} ${session.package.length} Minutes 1-2-1 ${session.package.total == '1' ? 'Session' : 'Sessions'}` }}
                        </p>
                    </div>
                    <div class="session-list__item__subsection">
                        <h4 class="session-list__item__heading">Date/Time</h4>
                        <p class="session-list__item__text session-list__item__text--blue">{{ session.time_human }}</p>
                    </div>
                </section>
                <section class="session-list__item__section session-list__item__section">
                    <template v-if="session.status == 'completed'">
                        <button class="button" @click="openModal('confirm')">Confirm this Session</button>
                        <button class="button button--grey button--outline" @click="openModal('report')">Report a Problem</button>
                    </template>
                    <template v-if="session.status == 'upcoming' || session.status == 'active'">
                        <button v-if="!session.link" class="button button--outline" v-on:click="getZoomLink(session, index)">Start Meeting</button>
                        <a v-if="session.link" class="button" :href="session.link.link" target="_blank">Join Session</a>
                        <a v-if="session.link" class="button button--outline button--red" v-on:click="getZoomLink(session, index, true)">Regenerate Meeting</a>
                    </template>
                    <template v-if="session.status == 'pending'">
                        <button class="button button--green" @click="updateSession(session, 'accept')">Accept Session</button>
                        <button class="button button--grey button--outline" @click="updateSession(session, 'decline')">Decline Session</button>
                    </template>

                    <template v-if="session.status == 'declined'">
                        You have declined this session, it will no longer take place.
                    </template>

                    <template v-if="session.status == 'cancelled'">
                        This session has been cancelled, it will no longer take place.
                    </template>
                </section>
            </li>
        </ul>
        <modal
            v-if="currentModal"
            v-model="showModal"
            :title="modalContent[currentModal].title"
            :modalClass="currentModal !== 'report' ? 'text-center' : ''"
            hideClose
            :size="modalContent[currentModal].size"
            :hideCancel="modalContent[currentModal].hideCancel">
            <template v-if="currentModal == 'report'">
                <h2 slot="header" class="modal__title">Report a problem with this session</h2>
                <div class="form-element form-element--title">
                    <label for="problem-select" class="form-element__label form-element__label--large">
                        What was the problem?
                    </label>
                    <div class="form-element__control--select">
                        <select id="problem-select" v-model="report.problem">
                            <option value="" disabled="disabled" selected="selected" hidden="hidden">Please select the problem</option>
                            <option
                                v-for="filter in report.problemOptions"
                                :key="filter.value"
                                :value="filter.value">
                                {{ filter.option }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-element form-element--title">
                    <label for="resolution-select" class="form-element__label form-element__label--large">
                        How would you like to resolve it?
                    </label>
                    <div class="form-element__control--select">
                        <select id="resolution-select" v-model="report.resolution">
                            <option value="" disabled="disabled" selected="selected" hidden="hidden">Please select a resolution</option>
                            <option
                                v-for="filter in report.resolutionOptions"
                                :key="filter.value"
                                :value="filter.value">
                                {{ filter.option }}
                            </option>
                        </select>
                    </div>
                </div>

                <div slot="footer" class="modal__footer">
                    <button class="button button--outline" @click="closeModal">Cancel</button>
                    <button class="button" :disabled="!report.problem || !report.resolution" @click="handleModalButtonClick('report')">
                        {{ modalContent.report.button }}
                    </button>
                </div>
            </template>
            <template v-else>
                {{ modalContent[currentModal].body }}

                <button slot="buttons" class="button" @click="handleModalButtonClick(currentModal)">
                    {{ modalContent[currentModal].button }}
                </button>
            </template>
        </modal>

        <div v-if="modalMessage !== ''" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Session Updated</h2>

                <p>{{ modalMessage }}</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="modalMessage = ''">Okay</button>
                </div>
            </div>
        </div>

        <div v-if="regeneratedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Meeting Created</h2>

                <p>Successfully create a new meeting, you are able to join your meeting now.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="regeneratedModal = false">Okay</button>
                </div>
            </div>
        </div>

        <div v-if="setZoomUserModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Action Required</h2>

                <p>To be able to participate in virtual training sessions you will need to provide a Zoom User ID for this trainer.</p>
                <p>Please contact your Zoom administrator to get this.</p>

                <div class="form-element">
                    <div class="form-element__control">
                        <input type="text" required v-model="zoomUserId">
                    </div>
                </div>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="setZoomUserModal = false">Skip for now</button>
                    <button class="button button--outline" @click="setZoomUserModal = false; updateZoomUserID()">Update</button>
                </div>
            </div>
        </div>

        <div v-if="ZoomUserUpdatedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Updated Successfully</h2>

                <p>Zoom User updated Successfully.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="ZoomUserUpdatedModal = false">Okay</button>
                </div>
            </div>
        </div>

        <div v-if="ZoomUserUpdateFailedModal" class="modal-alert modal-alert--active">
            <div class="modal-alert__box modal-alert__box--small modal-alert__body">
                <h2 class="modal-alert__title">Update Failed</h2>

                <p>Failed to update Zoom User - please contact your system administrator.</p>

                <div class="modal-alert__buttons">
                    <button class="button button--outline" @click="ZoomUserUpdateFailedModal = false">Okay</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Modal from '../../components/Modal.vue';
import FormInput from '../../components/FormInput.vue';


export default {
  components: { Modal, FormInput },
    props: {
        sessions: Array
    },
    data () {
        return {
            modalContent: {
                confirm: {
                    title: 'Did both parties attend this session?',
                    body: 'Please confirm that you and the member both attended this session',
                    button: 'Confirm Session'
                },
                report: {
                    button: 'Submit',
                    hideCancel: true,
                    size: 'large'
                },
                accept: {
                    title: 'This session has been added to your calendar',
                    body: 'We’ll send the member an email to let them know you’ve accepted the session',
                    button: 'Okay',
                    hideCancel: true
                },
                decline: {
                    title: 'Are you sure you want to decline this Session?',
                    body: 'We’ll send the Member an email with a link to your calendar and ask them to rebook.',
                    button: 'Decline Session'
                }
            },
            currentModal: '',
            showModal: false,
            modalMessage: '',
            report: {
                problem: '',
                problemOptions: [
                    { option: 'The member did not attend the session, without notice', value: 'member-not-attend-no-notice' },
                    { option: 'The member did not attend the session, but gave prior notice', value: 'member-not-attend-with-notice' },
                    { option: 'I did not attend the session', value: 'trainer-not-attend' },
                    { option: 'We had connection issues that meant the session could not take place', value: 'connection-issues' },
                ],
                resolution: '',
                resolutionOptions: [
                    { option: 'Return the session credit to the member', value: 'return-credit' },
                    { option: 'Take payment for this sesson and mark it as completed', value: 'complete-session' },
                    { option: 'Send the member a request to reschedule the session', value: 'reschedule-session' },
                ]
            },
            regeneratedModal: false,
            setZoomUserModal: false,
            ZoomUserUpdateFailedModal: false,
            ZoomUserUpdatedModal: false,
            zoomUserId: null
        }
    },

    mounted() {
        let that = this;
        this.sessions.forEach((session, i) => {
            that.sessions[i].link = this.getStoredZoomLink(session.id);
        });
        this.$emit('reload');
        this.checkTrainerHasZoomID();

    },
    methods: {
        updateZoomUserID(){
            axios.post("/api/zoom/user/storeid", {
                'zoom_id': this.zoomUserId,
                'trainer_id': this.$route.params.id
            }).then(response => {
                if(response.data.zoom_user_id) {
                    this.ZoomUserUpdatedModal = true;
                } else {
                    this.ZoomUserUpdateFailedModal = true;
                }
            }).catch(error => {
                console.log(error);
            })
        },
        checkTrainerHasZoomID(){
            axios.get('/api/zoom/user/'+this.$route.params.id+'/id').then(response => {
                console.log(response);
                if(!response.data.zoom_user_id) {
                    this.setZoomUserModal = true;
                }
            }).catch(error => {
                console.log(error);
            });
        },
        getInitials (member) {
            return `${member.first_name[0]}${member.last_name[0]}`;
        },
        openModal (key) {
            this.currentModal = key;
            this.showModal = true;
        },
        closeModal () {
            this.report.problem = '';
            this.report.resolution = '';
            this.currentModal = '';
            this.showModal = false;
        },
        getZoomLink(session, index, regenerate = false){
            let that = this;
            axios.post("/api/zoom/create",{
                "session_id": session.id,
                "trainer_id": this.$route.params.id,
                "duration": session.length + 30,
                "regenerate": regenerate
            }).then(response => {
                this.updateStoredSessionLink(response.data.original.link, session.id);
                this.$emit('reload');
                this.regeneratedModal = true;
            })
        },
        updateStoredSessionLink(link, session_id){
            axios.post('/api/zoom/session/storelink',{
                session_id: session_id,
                link: link
            }).then(response => {
                console.log("Stored Session link");
            }).catch(error => {
                console.log(error);
            });
        },
        getStoredZoomLink(session_id){
            axios.get("/api/zoom/session/zoomlink/"+session_id).then(response => {
                return response.data.link;
            })
        },
        handleModalButtonClick (key) {
            switch (key) {
                case 'confirm':
                case 'report':
                case 'accept':
                case 'decline':
            }

            this.closeModal();
        },

        /*
         * Update a sessions status.
         * @param {session} object
         * @param {status} string
         */
        updateSession(session, status) {
            axios.patch('/api/admin/sessions/' + session.id + '/' + status).then(response => {
                this.modalMessage = response.data.message;
                this.$emit('reload');
            })
            .catch(error => {
                console.log('ERROR');
                console.log(error)
            })
            .finally(() => this.loading = false);
        }
    }
}
</script>
