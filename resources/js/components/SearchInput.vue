<template>
    <div v-if="isHeader" :class="{ header__search__input: true, 'header__search__input--active': showSearchBox }">
        <transition name="expand-search" mode="out-in">
            <input 
                v-if="showSearchBox"
                ref="search"
                v-model="searchTerm"
                :placeholder="placeholder"
                @keyup.enter="headerSearch">
        </transition>
        <i class="material-icons" @click="headerSearch">
            search
        </i>
    </div>

    <form-input 
        v-else
        v-model.trim="searchTerm"
        :placeholder="placeholder"
        rounded
        @keyup.enter="search">
        <i 
            slot="after-input" 
            class="form-input__after material-icons"
            @click.stop="search">
            search
        </i>
    </form-input>
</template>

<script>
import FormInput from './FormInput.vue';

export default {
    components: { FormInput },
    props: {
        placeholder: {
            default: 'Search'
        },
        isHeader: Boolean
    },
    data () {
        return {
            searchTerm: '',
            showSearchBox: false
        }
    },
    methods: {
        headerSearch () {   
            if (this.showSearchBox && this.searchTerm) this.search();

            this.showSearchBox = !this.showSearchBox;
            this.searchTerm = '';

            this.$nextTick(() => {
                if (this.showSearchBox) this.$refs.search.focus();
            })
        },
        search () {
            this.$emit('search', this.searchTerm)
        }
    }
}
</script>