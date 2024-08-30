export default {
    methods: {
        endTabletSession() {
            localStorage.removeItem('tablet-token');
            sessionStorage.removeItem('tablet-token');
            window.location.href = '/tablet';
        }
    },
}
