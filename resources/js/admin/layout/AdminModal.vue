<template>
<div v-if="show" :class="{ modal: true, 'modal--active': show }">
    <div class="modal__box modal__box--left">
        <div class="modal__header">
            <h3 class="modal__title">{{ title }}</h3>
        </div>
        <div class="modal__body">
            <slot></slot>
        </div>
        <div class="modal__footer">
            <button v-if="!hideCancel" class="button button--outline" @click="closeModal">Cancel</button>
            <slot name="footer"></slot>
        </div>
    </div>
</div>
</template>

<script>
export default {
    model: {
        prop: 'show',
        event: 'change'
    },
    props: {
        show: Boolean,
        title: String,
        hideCancel: Boolean
    },
    watch: {
        show (showModal, oldValue) {
            this.checkShowState()
        }
    },
    methods: {
        closeModal () {
            this.$emit('change', false);
            this.$emit('close')
            document.body.classList.remove('modal-open')
        },
        checkShowState () {
            this.show ?
                document.body.classList.add('modal-open') :
                document.body.classList.remove('modal-open');
        }
    },
    mounted () {
        this.checkShowState()
    },
    beforeDestroy () {
        document.body.classList.remove('modal-open')
    }
}
</script>