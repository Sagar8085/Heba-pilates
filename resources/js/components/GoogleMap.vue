<template>
    <div ref="map" class="google-map"></div>
</template>

<script>
import { Loader } from "@googlemaps/js-api-loader"
const apiKey = 'AIzaSyBRFQgkfBIJfcpYrxrIPqPbHncSF5yNg6k';

export default {
    props: {
        mapConfig: {
            type: Object,
            default: () => { 
                return { center: { lat: 0, lng: 0 }, zoom: 15 } 
            }
        },
        markers: {
            type: Array, // { lat: Number, lng: Number }[]
            default: () => []
        }
    },
    watch: {
        markers: {
            handler (newVal, oldVal) {
            if (newVal.filter((x, i) => x.lat == oldVal[i].lat && x.lng == oldVal[i].lng).length == oldVal.length)
                return;

            for (const marker of this.mapMarkers) {
                marker.setMap(null);
            }
            this.mapMarkers = [];
            this.updateMarkers();
        },
        deep: true
        } 
    },
    data () {
        return {
            map: null,
            bounds: null,
            mapMarkers: []
        }
    },
    methods: {
        addMarker (position) {
            const marker = new google.maps.Marker({
                position,
                map: this.map,
            });
            this.mapMarkers.push(marker);
            return marker;
        },
        updateMarkers () {
            if (this.markers.length == 1) {
                const m = this.addMarker(this.markers[0]);
                const pos = new google.maps.LatLng(m.position.lat(), m.position.lng());
                this.map.setZoom(15);
                this.map.setCenter(pos)

            } else {
                this.bounds = new google.maps.LatLngBounds();
    
                for (const marker of this.markers) {
                    const m = this.addMarker(marker)

                    const pos = new google.maps.LatLng(m.position.lat(), m.position.lng());
                    this.bounds.extend(pos);
                }
    
                this.map.fitBounds(this.bounds);
                this.map.panToBounds(this.bounds);
            }
        },
        initMap () {
            const mapContainer = this.$refs.map;
            const config = this.mapConfig;

            this.map = new google.maps.Map(mapContainer, config);
            this.updateMarkers();
        }
    },
    async mounted () {
        if (window.google && window.google.maps) return this.initMap();
        
        const loader = new Loader({
            apiKey,
            version: "weekly",
        });

        try {
            await loader.load()
            this.initMap();
        } catch (error) {
            console.error('Map Error', error);
        }
    }
}
</script>

<style>
.google-map {
    height: 100%;
}
</style>