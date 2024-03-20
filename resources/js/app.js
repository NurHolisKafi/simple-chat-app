import "./bootstrap";
import "../css/app.css";

import { createApp } from "vue";
import router from "./routes/route";
import App from "./Pages/App.vue";
import { createPinia } from "pinia";
import userStore from "./store/UserStore";

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
window.token = userStore().token;
app.use(router);
app.mount("#app");
