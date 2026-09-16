<script setup>
import Sidebar from "./components/Sidebar.vue";
</script>

<template>
    <div class="layout">
        <Sidebar />
        <main>
            <router-view />
        </main>
    </div>
</template>
