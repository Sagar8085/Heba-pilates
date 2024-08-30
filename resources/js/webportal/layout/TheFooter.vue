<template>
    <footer class="footer" v-if="!this.$route.path.includes('/onboard')">
        <div class="wrapper">
            <div class="footer__section">
                <router-link to="/"><img src="/images/logos/heba-logo.png" alt="Heba Pilates Logo" height="37"></router-link>
                <p>&copy; Heba Pilates 2021</p>
            </div>

            <ul class="footer__section">
                <li class="footer__section__heading">Legal</li>
                <li class="footer__section__link">
                    <button class="button button--link button--plain" @click="$emit('open-terms')">Terms of Service</button>
                </li>
                <li class="footer__section__link">
                    <router-link to="/privacy">Privacy Policy</router-link>
                </li>
            </ul>

            <ul class="footer__section">
                <li class="footer__section__heading">Site</li>
                <li class="footer__section__link">
                    <router-link to="/on-demand">On Demand</router-link>
                </li>
                <li class="footer__section__link">
                    <router-link to="/contact">Contact us</router-link>
                </li>
            </ul>

            <ul class="footer__section">
                <li class="footer__section__heading">Download the app</li>
                <li class="footer__section__link">
                    <a href="https://play.google.com/store/apps/details?id=co.uk.volution.hebepilates" target="_blank">
                        <img src="/images/landing-page/google-play.png" alt="Google Play" width="130">
                    </a>
                </li>
                <li class="footer__section__link">
                    <a href="https://apps.apple.com/gb/app/heba-pilates/id1563902238" target="_blank">
                        <img src="/images/landing-page/app-store.png" alt="App Store" width="130">
                    </a>
                </li>
            </ul>

            <ul class="footer__section">
                <!-- <li class="footer__section__heading">Social media</li>
                <li class="footer__section__link">
                    <a href="https://facebook.com" target="_blank">
                        <img class="footer__section__link__icon" src="/images/social-media/facebook-logo-alt.png" alt="Facebook">
                        <span>Facebook</span>
                    </a>
                </li>
                <li class="footer__section__link">
                    <a href="https://twitter.com" target="_blank">
                        <img class="footer__section__link__icon" src="/images/social-media/twitter-logo-alt.png" alt="Twitter">
                        <span>Twitter</span>
                    </a>
                </li>
                <li class="footer__section__link">
                    <a href="https://instagram.com" target="_blank">
                        <img class="footer__section__link__icon" src="/images/social-media/instagram-logo-alt.png" alt="Instagram">
                        <span>Instagram</span>
                    </a>
                </li> -->
                <!-- <li class="footer__section__link">
                    <a href="https://spotify.com" target="_blank">
                        <img class="footer__section__link__icon" src="/images/social-media/spotify-logo.png" alt="Spotify">
                        <span>Spotify</span>
                    </a>
                </li> -->
            </ul>
        </div>
    </footer>
</template>
