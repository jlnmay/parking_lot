import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: "/login",
        name: "login",
        component: () => import("./Login.vue"),
    },
    {
        path: "/",
        name: "attendant.overview",
        component: () => import("./pages/attendant/Overview.vue"),
    },
    {
        path: "/shift",
        name: "attendant.shift",
        component: () => import("./pages/attendant/MyShift.vue"),
    },
    {
        path: "/admin",
        name: "admin.overview",
        component: () => import("./pages/admin/Overview.vue"),
    },
    {
        path: "/admin/pricing",
        name: "admin.pricing",
        component: () => import("./pages/admin/Pricing.vue"),
    },
    {
        path: "/admin/users",
        name: "admin.users",
        component: () => import("./pages/admin/Users.vue"),
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
