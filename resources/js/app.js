import "./bootstrap";
import "../css/app.css";

import { createApp } from "vue";
import router from "./routes/route";
import App from "./Pages/App.vue";
import { createPinia } from "pinia";

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);
app.mount("#app");
