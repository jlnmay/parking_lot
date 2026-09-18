// resources/js/__tests__/router.test.js
import { describe, it, expect, vi, beforeEach } from "vitest";
import { createApp } from "vue";
import { createPinia, setActivePinia } from "pinia";
import { createRouter, createMemoryHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { getLandingRouteNameForRole } from "../utils/roleRouting";

const ALL_ROLES = ["cajero", "supervisor", "administrador"];
const ADMIN_AND_SUPERVISOR = ["supervisor", "administrador"];
const ADMIN_ONLY = ["administrador"];

function buildTestRouter() {
    const router = createRouter({
        history: createMemoryHistory(),
        routes: [
            {
                path: "/login",
                name: "login",
                component: { template: "<div>Login</div>" },
                meta: { requiresAuth: false },
            },
            {
                path: "/",
                name: "attendant.overview",
                component: { template: "<div>Attendant</div>" },
                meta: { requiresAuth: true, roles: ALL_ROLES },
            },
            {
                path: "/admin",
                name: "admin.overview",
                component: { template: "<div>Admin</div>" },
                meta: { requiresAuth: true, roles: ADMIN_AND_SUPERVISOR },
            },
            {
                path: "/admin/pricing",
                name: "admin.pricing",
                component: { template: "<div>Pricing</div>" },
                meta: { requiresAuth: true, roles: ADMIN_ONLY },
            },
        ],
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

    return router;
}

describe("router guards", () => {
    let pinia;

    beforeEach(() => {
        pinia = createPinia();
        const app = createApp({});
        app.use(pinia);
        setActivePinia(pinia);
    });

    it("redirects an unauthenticated visitor to /login", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "fetchUser").mockImplementation(async () => {
            authStore.user = null;
            authStore.initialized = true;
        });

        const router = buildTestRouter();
        router.push("/");
        await router.isReady();

        expect(router.currentRoute.value.name).toBe("login");
    });

    it("allows an authenticated attendant onto the attendant route", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "fetchUser").mockImplementation(async () => {
            authStore.user = { id: 1, name: "Jordan", role: "cajero" };
            authStore.initialized = true;
        });

        const router = buildTestRouter();
        router.push("/");
        await router.isReady();

        expect(router.currentRoute.value.name).toBe("attendant.overview");
    });

    it("redirects an attendant away from an admin-only route to their own landing route", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "fetchUser").mockImplementation(async () => {
            authStore.user = { id: 1, name: "Jordan", role: "cajero" };
            authStore.initialized = true;
        });

        const router = buildTestRouter();
        router.push("/admin/pricing");
        await router.isReady();

        expect(router.currentRoute.value.name).toBe("attendant.overview");
    });

    it("allows a supervisor onto /admin but not onto pricing", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "fetchUser").mockImplementation(async () => {
            authStore.user = { id: 2, name: "Maya", role: "supervisor" };
            authStore.initialized = true;
        });

        const router = buildTestRouter();

        router.push("/admin");
        await router.isReady();
        expect(router.currentRoute.value.name).toBe("admin.overview");

        await router.push("/admin/pricing");
        expect(router.currentRoute.value.name).toBe("admin.overview"); // bounced back
    });

    it("allows an admin onto pricing", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "fetchUser").mockImplementation(async () => {
            authStore.user = { id: 3, name: "Priya", role: "administrador" };
            authStore.initialized = true;
        });

        const router = buildTestRouter();
        router.push("/admin/pricing");
        await router.isReady();

        expect(router.currentRoute.value.name).toBe("admin.pricing");
    });

    it("bounces an already-authenticated user away from /login to their landing route", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "fetchUser").mockImplementation(async () => {
            authStore.user = { id: 1, name: "Jordan", role: "cajero" };
            authStore.initialized = true;
        });

        const router = buildTestRouter();
        router.push("/login");
        await router.isReady();

        expect(router.currentRoute.value.name).toBe("attendant.overview");
    });

    it("only calls fetchUser once across multiple navigations", async () => {
        const authStore = useAuthStore();
        const fetchSpy = vi
            .spyOn(authStore, "fetchUser")
            .mockImplementation(async () => {
                authStore.user = { id: 1, name: "Jordan", role: "cajero" };
                authStore.initialized = true;
            });

        const router = buildTestRouter();
        router.push("/");
        await router.isReady();
        await router.push("/admin"); // will bounce, but shouldn't re-fetch

        expect(fetchSpy).toHaveBeenCalledTimes(1);
    });
});
