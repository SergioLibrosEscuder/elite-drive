import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./pages/App.vue";
import router from "./router";
import axios from "axios";

// Axios configuration
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// CSRF token
let token = document.head.querySelector('meta[name="csrf-token"]');

// Set CSRF token correctly
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error("CSRF token not found");
}

// Define pinia dependence
const pinia = createPinia();

// Create app with dependences
createApp(App).use(router).use(pinia).mount("#app");
