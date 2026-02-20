import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./pages/App.vue";
import router from "./router";

// Define pinia dependence
const pinia = createPinia();

// Create app with dependences
createApp(App).use(router).use(pinia).mount("#app");
