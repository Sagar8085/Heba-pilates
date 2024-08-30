<template type="text/x-template" id="modal-template">
    <transition name="modal">
        <div class="modal-mask" >
            <div class="modal-wrapper">
                <div class="modal-wrapper__header">
                    <slot name="header">Header</slot>
                </div>
                <div class="modal-container">

                    <div class="modal-wrapper__icon">
                        <slot name="icon">Icon</slot>
                    </div>

                    <slot name="body">Body</slot>

                    <div class="booking-buttons">
                        <a v-on:click="$emit('close')" class="booking-button booking-button--white">Go Back</a>
                        <a v-on:click="$emit('book')" class="booking-button booking-button--blue"><slot name="button">Button</slot></a>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

