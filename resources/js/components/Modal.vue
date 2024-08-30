<template>
    <transition :name="transitionName">
        <div v-if="show" :class="{ modal: true, 'modal--active': show, 'modal--bottom': align == 'bottom' }" @click="onBackdropClick">
            <div :class="`modal__box modal__box--${size} ${modalClass}`" @click.stop>
                <div v-if="!!$slots.header" class="modal__header">
                    <slot name="header"></slot>
                </div>
                <div class="modal__body">
                    <button v-if="!hideClose" class="modal__close button button--icon" @click="closeModal">
                        <i class="material-icons">close</i>
                    </button>
                    <div class="modal__top">
                        <slot name="heading">
                            <h2 v-if="title" class="modal__title">{{ title }}</h2>
                        </slot>
                    </div>
                    <p><slot></slot></p>
                    <div v-if="!hideCancel || !!$slots.buttons || !!$scopedSlots.buttons" class="modal__buttons">
                        <button v-if="!hideCancel" class="button button--outline" @click="closeModal">Cancel</button>
                        <slot name="buttons" :close="closeModal"></slot>
                    </div>
                </div>
                <div v-if="!!$slots.footer" class="modal__bottom">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </transition>
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
        size: {
            type: String,
            default: 'small' // small, large, wide
        },
        hideCancel: Boolean,
        hideClose: Boolean,
        align: {
            type: String,
            default: 'center'
        },
        modalClass: {
            type: String,
            default: ''
        },
        closeOnBackdropClick: Boolean
    },
    watch: {
        show (showModal, oldValue) {
            this.checkShowState()
        }
    },
    data () {
        return { 
            transitionName: 'modal-fade'
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
        },
        onBackdropClick () {
            if (!this.closeOnBackdropClick) return;

            this.closeModal();
        }
    },
    mounted () {
        this.checkShowState()
    },
    beforeDestroy () {
        this.transitionName = '';
        document.body.classList.remove('modal-open')
    }
}
</script>