import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "./stores/auth";
import { getLandingRouteNameForRole } from "./utils/roleRouting";

const ALL_ROLES = ["cajero", "supervisor", "administrador"];
const ADMIN_AND_SUPERVISOR = ["supervisor", "administrador"];
const ADMIN_ONLY = ["administrador"];

const routes = [
    {
        path: "/login",
        name: "login",
        component: () => import("./Login.vue"),
        meta: { requiresAuth: false },
    },
    {
        path: "/",
        name: "attendant.overview",
        component: () => import("./pages/attendant/Overview.vue"),
        meta: { requiresAuth: true, roles: ALL_ROLES },
    },
    {
        path: "/shift",
        name: "attendant.shift",
        component: () => import("./pages/attendant/MyShift.vue"),
        meta: { requiresAuth: true, roles: ALL_ROLES },
    },
    {
        path: "/admin",
        name: "admin.overview",
        component: () => import("./pages/admin/Overview.vue"),
        meta: { requiresAuth: true, roles: ADMIN_AND_SUPERVISOR },
    },
    {
        path: "/admin/pricing",
        name: "admin.pricing",
        component: () => import("./pages/admin/Pricing.vue"),
        meta: { requiresAuth: true, roles: ADMIN_ONLY },
    },
    {
        path: "/admin/users",
        name: "admin.users",
        component: () => import("./pages/admin/Users.vue"),
        meta: { requiresAuth: true, roles: ADMIN_ONLY },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();

    if (!authStore.initialized) {
        await authStore.fetchUser();
    }

    const requiresAuth = to.meta.requiresAuth !== false;

    if (requiresAuth && !authStore.isAuthenticated) {
        return { name: "login" };
    }

    if (!requiresAuth && authStore.isAuthenticated) {
        return { name: getLandingRouteNameForRole(authStore.role) };
    }

    if (
        requiresAuth &&
        to.meta.roles &&
        !to.meta.roles.includes(authStore.role)
    ) {
        return { name: getLandingRouteNameForRole(authStore.role) };
    }

    return true;
});

export default router;
