<template>
    <li class="header__dropdown">
        <div class="header__dropdown__toggle" @click.stop="toggleDropdown"><slot></slot></div>
        <transition name="notifications">
            <ul v-if="show" :class="{ header__dropdown__items: true, 'header__dropdown__items--active': show }" v-on-clickaway="close">
                <li v-for="(item, index) in dropdownItems" :key="index" class="header__dropdown__item">
                    <router-link v-if="item.link" class="header__dropdown__item__link" :to="item.link">
                        <i v-if="item.icon" class="material-icons">{{ item.icon }}</i>
                        {{ item.title }}
                    </router-link>
                    
                    <button v-else class="header__dropdown__item__link" @click="$emit('click', item.title)">
                        <i v-if="item.icon" class="material-icons">{{ item.icon }}</i>
                        {{ item.title }}
                    </button>
                </li>
            </ul>
        </transition>
        <ul class="header__dropdown__items header__dropdown__items--mobile">
            <li v-for="(item, index) in dropdownItems" :key="index" class="header__dropdown__item">
                <router-link v-if="item.link" class="header__dropdown__item__link" :to="item.link">
                    {{ item.title }}
                </router-link>
                
                <button v-else class="header__dropdown__item__link" @click="$emit('click', item.title)">
                    {{ item.title }}
                </button>
            </li>
        </ul>
    </li>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';
export default {
    mixins: [ clickaway ],
    props: {
        dropdownItems: Array
    },
    data () {
        return {
            show: false
        }
    },
    methods: {
        toggleDropdown () {
            this.show = !this.show;

            if (this.show) this.$emit('show');
        },
        close () {
            this.show = false;
        }
    }
}
</script>
