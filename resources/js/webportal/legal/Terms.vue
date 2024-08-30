<template>
    <div>
        <div class="terms">
            <div class="wrapper">
                <div class="terms__container">
                    <terms-content />
                </div>
                <button class="button button--white terms__button" @click="$router.go(-1)">Back</button>
            </div>
        </div>
    </div>
</template>

<script>
import TermsContent from './TermsContent.vue';

export default {
    components: { TermsContent }
};
</script>
