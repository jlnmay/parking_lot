import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        initialized: false,
    }),

    getters: {
        isAuthenticated: (state) => state.user !== null,
        role: (state) => state.user?.role ?? null,
    },

    actions: {
        async fetchUser() {
            try {
                const response = await axios.get("/me");
                this.user = response.data.user;
            } catch (error) {
                if (error.response?.status === 401) {
                    this.user = null;
                } else {
                    throw error;
                }
            } finally {
                this.initialized = true;
            }
        },

        async login(credentials) {
            const response = await axios.post("/login", credentials);
            this.user = response.data.user;
            return this.user;
        },

        async logout() {
            await axios.post("/logout");
            this.user = null;
        },
    },
});
