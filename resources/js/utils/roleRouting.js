// resources/js/utils/roleRouting.js

const ROLE_LANDING_ROUTE_NAMES = {
    cajero: "attendant.overview",
    supervisor: "admin.overview", // no distinct Supervisor route yet — revisit when that page exists
    administrador: "admin.overview",
};

export function getLandingRouteNameForRole(role) {
    return ROLE_LANDING_ROUTE_NAMES[role] ?? "attendant.overview";
}
