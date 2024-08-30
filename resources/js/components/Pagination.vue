<template>

    <div class="pagination-container" v-if="this.pagination.total > 0">
        <div class="pagination-container__col">
            <button class="previous-button button" v-if="this.pagination.current_page > 1" @click="this.previous">
                <span v-if="this.loading"><i class="fas fa-circle-notch fa-spin"></i></span>
                <span v-else><i class="fas fa-chevron-left"></i><span>&nbsp;&nbsp;Previous</span></span>
            </button>
        </div>

        <div class="pagination-container__col">
            <p>{{ this.pagination.total.toLocaleString(0) }} Records · Page {{ this.pagination.current_page }} of {{ this.pagination.last_page }}</p>
        </div>

        <div class="pagination-container__col">
            <button class="next-button button" v-if="this.pagination.last_page > 1 && this.pagination.current_page < this.pagination.last_page" @click="this.next">
                <span v-if="this.loading"><i class="fas fa-circle-notch fa-spin"></i></span>
                <span v-else><span>Next&nbsp;&nbsp;</span><i class="fas fa-chevron-right"></i></span>
            </button>
        </div>
    </div>

</template>

<script>

import moment from 'moment';

export default
{
    change_page()
    {
        this.$emit('change_page');
    },

    props: {
        pagination: Object,
        loading: Boolean
    },

    data()
    {
        return {
            // loading: false,
            // pagination: {}
        }
    },

    mounted()
    {
    },

    methods: {

        previous: function(event)
        {
            if (!this.loading) {
                this.$emit('change_page', 'previous');
            }
        },

        next: function(event)
        {
            if (!this.loading) {
                this.$emit('change_page', 'next');
            }
        }

    }
}

</script>
