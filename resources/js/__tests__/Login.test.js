// resources/js/__tests__/Login.test.js
import { describe, it, expect, vi, beforeEach } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import { createApp } from "vue";
import { createPinia, setActivePinia } from "pinia";
import { createRouter, createMemoryHistory } from "vue-router";
import Login from "../Login.vue";
import { useAuthStore } from "../stores/auth";

describe("Login", () => {
    let pinia;
    let router;

    beforeEach(async () => {
        pinia = createPinia();
        const app = createApp({});
        app.use(pinia);
        setActivePinia(pinia);

        router = createRouter({
            history: createMemoryHistory(),
            routes: [
                { path: "/login", name: "login", component: Login },
                {
                    path: "/",
                    name: "attendant.overview",
                    component: { template: "<div>Attendant Overview</div>" },
                },
                {
                    path: "/admin",
                    name: "admin.overview",
                    component: { template: "<div>Admin Overview</div>" },
                },
            ],
        });

        router.push("/login");
        await router.isReady();
    });

    function mountPage() {
        return mount(Login, {
            global: {
                plugins: [pinia, router],
            },
        });
    }

    it("renders email, password, and sign-in button", () => {
        const wrapper = mountPage();

        expect(wrapper.find("#email").exists()).toBe(true);
        expect(wrapper.find("#password").exists()).toBe(true);
        expect(wrapper.find(".sign-in-button").exists()).toBe(true);
    });

    it("toggles password visibility", async () => {
        const wrapper = mountPage();

        expect(wrapper.find("#password").attributes("type")).toBe("password");

        await wrapper.find(".toggle-visibility").trigger("click");

        expect(wrapper.find("#password").attributes("type")).toBe("text");
    });

    it("shows inline error and does not navigate on failed login", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "login").mockRejectedValue({
            response: {
                status: 422,
                data: { errors: { email: ["Incorrect email or password."] } },
            },
        });

        const wrapper = mountPage();
        await wrapper.find("#email").setValue("jordan@example.com");
        await wrapper.find("#password").setValue("wrong");
        await wrapper.find("form").trigger("submit.prevent");
        await flushPromises();

        expect(wrapper.find(".error-message").text()).toBe(
            "Incorrect email or password.",
        );
        expect(router.currentRoute.value.path).toBe("/login");
    });

    it("redirects an attendant to the attendant overview", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "login").mockResolvedValue({
            id: 1,
            name: "Jordan",
            role: "cajero",
        });

        const wrapper = mountPage();
        await wrapper.find("#email").setValue("jordan@example.com");
        await wrapper.find("#password").setValue("correct-password");
        await wrapper.find("form").trigger("submit.prevent");
        await flushPromises();

        expect(router.currentRoute.value.path).toBe("/");
    });

    it("redirects an admin to the admin overview", async () => {
        const authStore = useAuthStore();
        vi.spyOn(authStore, "login").mockResolvedValue({
            id: 2,
            name: "Priya",
            role: "administrador",
        });

        const wrapper = mountPage();
        await wrapper.find("#email").setValue("priya@example.com");
        await wrapper.find("#password").setValue("correct-password");
        await wrapper.find("form").trigger("submit.prevent");
        await flushPromises();

        expect(router.currentRoute.value.path).toBe("/admin");
    });
});
