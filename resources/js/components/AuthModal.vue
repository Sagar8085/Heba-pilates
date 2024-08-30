<template>
    <div>
        <div class="modal modal--active">
            <div class="modal__box modal__box--small modal__body">
                <h2 class="modal__title">Authentication Required</h2>

                <p>To make access content or make purchases you need to login to your existing account, or create a new one.</p>

                <div class="modal__buttons">
                    <router-link class="button button--with-icon" to="/login">Log in</router-link>
                    <router-link class="button button--with-icon button--outline" to="/signup">Sign up</router-link>
                </div>

                <div class="modal__divider"></div>

                <button class="button button--outline" @click="$emit('close')">Cancel</button>
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
