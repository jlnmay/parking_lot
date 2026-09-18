import { createApp } from "vue";
import { createPinia } from "pinia";
import { axiosAuthPlugin } from "./stores/plugins/axiosAuthPlugin";
import router from "./router.js";
import "./bootstrap";
import App from "./App.vue";

const pinia = createPinia().use(axiosAuthPlugin);

const app = createApp(App);
app.use(pinia);
app.use(router);

app.mount("#app");
