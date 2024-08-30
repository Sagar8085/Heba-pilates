<template>
    <section class="tab">
        <ul>
            <li     
                v-for="(tab, index) in tabs" 
                :key="index"
                :class="{ active: getTabKey(tab) == active }"
                @click="onTabClick(tab)">
                <slot :tab="tab">
                    <router-link v-if="typeof tab == 'object' && tab.link" :to="tab.link">{{ tab.name }}</router-link>
                    <span v-else class="tab__link">{{ tab.name ? tab.name : tab }}</span>
                </slot>
            </li>
        </ul>
    </section>
</template>

<script>
export default {
    model: {
        prop: 'active',
        event: 'change'
    },
    props: {
        active: String, // Active tab
        tabs: Array // Either [ 'Tab Name' ] or [ { name: 'Tab Name', link: '/', key: 'tab-name' }]
    },
    methods: {
        getTabKey (tab) {
            return tab.key ? tab.key : tab.name ? tab.name : tab;
        },
        onTabClick (tab) {
            const value = this.getTabKey(tab);
            if (value !== this.active)
                this.$emit('change', value);
        }
    }
}
</script>