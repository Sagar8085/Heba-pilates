<template>
    <div class="navigation">
        <button 
            @click="handleClick" 
            class="navigation__back">
            <img src="/images/icons/backblue.png" alt="Back"> &nbsp; Back
        </button>
    </div>
</template>

<script>
export default {
    methods: {
        handleClick (e) {
            this.$listeners.click ? this.$emit('click', e) : this.$router.go(-1)
        }
    }
}
</script>