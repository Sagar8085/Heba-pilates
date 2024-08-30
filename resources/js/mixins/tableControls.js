export default {
    data() {
        return {
            visibleControls: [],
        };
    },

    methods: {
        /**
         * @param {string} identifier
         * @returns {boolean}
         */
        controlsAreVisible(identifier) {
            return this.visibleControls.includes(identifier);
        },

        /**
         * @param {string} identifier
         */
        toggleControls(identifier) {
            if (this.controlsAreVisible(identifier)) {
                this.visibleControls = this.visibleControls.filter(id => id !== identifier);
            } else {
                this.visibleControls.push(identifier);
            }
        },
    },
}
