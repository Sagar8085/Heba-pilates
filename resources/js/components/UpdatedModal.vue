<template>
    <div>
        <div class="modal modal--active">
            <div class="modal__box modal__box--small modal__body">
                <h2 class="modal__title">Update Successful</h2>

                <p>Thank you for providing those details and using our service. Our workouts will now be tailored to you!</p>

                <div class="modal__divider"></div>

                <button class="button button--green" @click="$emit('close')">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    }
}
</script>
