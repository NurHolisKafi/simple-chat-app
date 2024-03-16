import { createRouter, createWebHistory } from "vue-router";
import { CanLoggedIn, isLoggedIn } from "../middleware/auth";
import userStore from "../store/UserStore";

const routes = [
    {
        path: "/",
        name: "home",
        component: () => import("../Pages/Home.vue"),
        meta: { middleware: isLoggedIn },
    },
    {
        path: "/login",
        name: "login",
        component: () => import("../Pages/Login.vue"),
        meta: { middleware: CanLoggedIn },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const store = userStore();
    if (to.meta.middleware) {
        to.meta.middleware(next, store.auth);
    } else {
        next();
    }
});

export default router;
