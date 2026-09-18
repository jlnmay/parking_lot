<script setup>
// TODO(AUTH-3): replace with actual authenticated user's role
const role = "";

const navItems = {
    attendant: [
        { to: "/", label: "Overview" },
        { to: "/shift", label: "My Shift" },
    ],
    admin: [
        { to: "/admin", label: "Overview" },
        { to: "/admin/vehicles", label: "Vehicles" },
        { to: "/admin/plans", label: "Plans" },
        { to: "/admin/reports", label: "Reports" },
        { to: "/admin/pricing", label: "Pricing" },
        { to: "/admin/users", label: "Users" },
    ],
    supervisor: [
        { to: "/admin", label: "Overview" },
        { to: "/admin/live-lot", label: "Live lot" },
        { to: "/admin/shifts", label: "Shifts" },
        { to: "/admin/alerts", label: "Alerts" },
    ],
};
</script>

<template>
    <nav>
        <router-link
            v-for="item in navItems[role]"
            :key="item.to"
            :to="item.to"
        >
            {{ item.label }}
        </router-link>
    </nav>
</template>
