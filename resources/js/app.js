import "./bootstrap";
import { createApp } from "vue";
import App from "./pages/App.vue";
import router from "./router";

createApp(App).use(router).mount("#app");

import axios from 'axios';

// Configuramos Axios para que lea el token del meta tag que pusimos en el paso 1
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}
