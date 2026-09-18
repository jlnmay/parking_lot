<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "./stores/auth";
import { getLandingRouteNameForRole } from "./utils/roleRouting";

const router = useRouter();
const authStore = useAuthStore();

const email = ref("");
const password = ref("");
const showPassword = ref(false);
const errorMessage = ref("");
const isSubmitting = ref(false);

async function handleSubmit() {
    errorMessage.value = "";
    isSubmitting.value = true;

    try {
        const user = await authStore.login({
            email: email.value,
            password: password.value,
        });

        router.push({ name: getLandingRouteNameForRole(user.role) });
    } catch (error) {
        if (error.response?.status === 422) {
            errorMessage.value =
                error.response.data.errors?.email?.[0] ??
                "Ocurrió un error. Inténtalo de nuevo.";
        } else {
            errorMessage.value = "Ocurrió un error. Inténtalo de nuevo.";
        }
    } finally {
        isSubmitting.value = false;
    }
}
</script>
<template>
    <div
        class="min-h-screen flex items-center justify-center bg-[#faf9f6] px-4"
    >
        <div
            class="w-full max-w-md bg-white border border-gray-200 rounded-sm p-12"
        >
            <div class="flex flex-col items-center mb-10">
                <div class="relative w-14 h-14 mb-4">
                    <div
                        class="absolute inset-0 border-2 border-gray-800 rounded-sm"
                    ></div>
                    <div
                        class="absolute top-0 right-0 w-3 h-3 bg-yellow-400"
                    ></div>
                    <div
                        class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-gray-800"
                    >
                        P
                    </div>
                </div>

                <h1
                    class="text-xl font-bold tracking-[0.3em] text-gray-900 uppercase"
                >
                    Parking Lot
                </h1>

                <div class="flex items-center gap-3 mt-2">
                    <span class="w-4 h-px bg-yellow-400"></span>
                    <p
                        class="text-xs tracking-[0.25em] text-gray-500 uppercase"
                    >
                        Sistema de Administración
                    </p>
                    <span class="w-4 h-px bg-yellow-400"></span>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div>
                    <label
                        for="email"
                        class="block text-sm font-semibold text-gray-900 mb-2"
                    >
                        Usuario
                    </label>
                    <input
                        id="email"
                        v-model="email"
                        type="email"
                        placeholder="Ingresa tu usuario"
                        required
                        autocomplete="username"
                        class="w-full border border-gray-300 rounded-sm px-4 py-3 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                    />
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-sm font-semibold text-gray-900 mb-2"
                    >
                        Contraseña
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            v-model="password"
                            :key="showPassword ? 'text' : 'password'"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Ingresa tu contraseña"
                            required
                            autocomplete="current-password"
                            class="w-full border border-gray-300 rounded-sm px-4 py-3 pr-12 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        />
                        <button
                            type="button"
                            class="toggle-visibility absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                            @click="showPassword = !showPassword"
                            :aria-label="
                                showPassword
                                    ? 'Ocultar contraseña'
                                    : 'Mostrar contraseña'
                            "
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <p
                    v-if="errorMessage"
                    class="error-message text-sm text-red-600"
                >
                    {{ errorMessage }}
                </p>

                <button
                    type="submit"
                    class="sign-in-button w-full bg-yellow-400 hover:bg-yellow-500 disabled:opacity-60 disabled:cursor-not-allowed text-gray-900 font-bold py-3.5 rounded-sm transition-colors"
                    :disabled="isSubmitting"
                >
                    {{
                        isSubmitting ? "Iniciando sesión..." : "Iniciar sesión"
                    }}
                </button>
            </form>
        </div>
    </div>
</template>
