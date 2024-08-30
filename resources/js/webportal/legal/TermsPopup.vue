<template>
    <Modal v-model="localValue" class="terms__modal" size="large" hideCancel closeOnBackdropClick>
        <div class="terms__modal__text">
            <terms-content ref="terms" />
        </div>
    </Modal>
</template>

<script>
import Modal from '../../components/Modal.vue';
import TermsContent from './TermsContent.vue'

export default {
    components: { Modal, TermsContent },
    props: {
        value: Boolean
    },
    computed: {
        localValue: {
            get () { return this.value },
            set (value) { this.$emit('input', value) }
        }
    },
    methods: {
        goToHealthCommitmentStatement () {
            this.$refs.terms.goToHealthCommitmentStatement();
        }
    }
};
</script>
