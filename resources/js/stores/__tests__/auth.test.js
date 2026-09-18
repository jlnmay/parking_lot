import { describe, it, expect, vi, beforeEach } from "vitest";
import { setActivePinia, createPinia } from "pinia";
import { useAuthStore } from "../auth";
import axios from "axios";

vi.mock("axios");

describe("auth store", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        vi.clearAllMocks();
    });

    it("starts unauthenticated", () => {
        const store = useAuthStore();
        expect(store.isAuthenticated).toBe(false);
        expect(store.initialized).toBe(false);
    });

    it("fetchUser populates the store on 200", async () => {
        axios.get.mockResolvedValueOnce({
            data: { user: { id: 1, name: "Jordan", role: "cajero" } },
        });

        const store = useAuthStore();
        await store.fetchUser();

        expect(store.isAuthenticated).toBe(true);
        expect(store.role).toBe("cajero");
        expect(store.initialized).toBe(true);
    });

    it("fetchUser leaves store empty on 401", async () => {
        axios.get.mockRejectedValueOnce({ response: { status: 401 } });

        const store = useAuthStore();
        await store.fetchUser();

        expect(store.isAuthenticated).toBe(false);
        expect(store.initialized).toBe(true);
    });

    it("fetchUser rethrows non-401 errors", async () => {
        axios.get.mockRejectedValueOnce({ response: { status: 500 } });

        const store = useAuthStore();

        await expect(store.fetchUser()).rejects.toBeTruthy();
        expect(store.initialized).toBe(true);
    });

    it("login populates the store with the returned user", async () => {
        axios.post.mockResolvedValueOnce({
            data: { user: { id: 2, name: "Maya", role: "administrador" } },
        });

        const store = useAuthStore();
        await store.login({ email: "maya@example.com", password: "secret" });

        expect(store.isAuthenticated).toBe(true);
        expect(store.user.name).toBe("Maya");
    });

    it("logout clears the store", async () => {
        axios.post.mockResolvedValueOnce({});

        const store = useAuthStore();
        store.user = { id: 1, name: "Jordan" };

        await store.logout();

        expect(store.isAuthenticated).toBe(false);
        expect(store.user).toBeNull();
    });
});
