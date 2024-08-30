<template>
    <div class="menu-dropdown">
        <div class="menu-dropdown__toggle" @click.stop="toggle">
            <slot>Toggle</slot>
        </div>
        <transition name="notifications">
            <ul v-if="showMenu" :class="{ 'menu-dropdown__menu': true, 'menu-dropdown__menu--active': showMenu }" v-on-clickaway="close">
                <slot name="items">
                    <li v-for="item in items" :key="item.key" class="menu-dropdown__item">
                        <router-link v-if="item.link" class="menu-dropdown__link" :to="item.link">
                            <i v-if="item.icon" class="material-icons">{{ item.icon }}</i> {{ item.label }}
                        </router-link>

                        <button v-else class="menu-dropdown__link" @click="$emit('click', item.key)">
                            <i v-if="item.icon" class="material-icons">{{ item.icon }}</i> {{ item.label }}
                        </button>
                    </li>
                </slot>
            </ul>
        </transition>
    </div>
</template>

<script>
import { mixin as clickaway } from 'vue-clickaway';
export default {
    mixins: [ clickaway ],
    props: { 
        value: Boolean, 
        items: Array // { label: String, key: [String,Number], icon?: String, link?: String }[]
    },
    computed: {
        showMenu: {
            get () { return this.value },
            set (value) { this.$emit('input', value) }
        }
    },
    methods: {
        toggle () {
            this.showMenu = !this.showMenu;
        },
        close () {
            this.showMenu = false;
        }
    }
}
</script>