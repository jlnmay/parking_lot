import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { createApp } from "vue";
import { createPinia, setActivePinia } from "pinia";
import axios from "axios";
import { axiosAuthPlugin } from "../axiosAuthPlugin";
import { useAuthStore } from "../../auth";

describe("axiosAuthPlugin", () => {
    let useSpy;
    let pinia;

    beforeEach(() => {
        useSpy = vi.spyOn(axios.interceptors.response, "use");

        pinia = createPinia().use(axiosAuthPlugin);
        const app = createApp({});
        app.use(pinia); // flushes toBeInstalled → _p, matches real app.js behavior
        setActivePinia(pinia);
    });

    afterEach(() => {
        useSpy.mockRestore();
    });

    it("registers exactly one interceptor when the auth store is created", () => {
        useAuthStore(pinia);

        expect(useSpy).toHaveBeenCalledTimes(1);
    });

    it("clears user on a 401 response error", async () => {
        const store = useAuthStore(pinia);
        store.user = { id: 1, name: "Jordan" };

        const [, errorHandler] = useSpy.mock.calls[0];

        await errorHandler({ response: { status: 401 } }).catch(() => {});

        expect(store.user).toBeNull();
    });

    it("leaves user untouched on a non-401 error", async () => {
        const store = useAuthStore(pinia);
        store.user = { id: 1, name: "Jordan" };

        const [, errorHandler] = useSpy.mock.calls[0];

        await errorHandler({ response: { status: 500 } }).catch(() => {});

        expect(store.user).not.toBeNull();
    });
});
