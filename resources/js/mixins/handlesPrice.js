export default {
    watch: {
        price(newPrice) {
            this.price = parseFloat(newPrice).toFixed(2);
        }
    },
}
