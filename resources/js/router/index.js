import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "../composables/useAuth";

import Index from "../pages/Index.vue";
import Admin from "../pages/Admin.vue";
import Cars from "../pages/Cars.vue";
import Contact from "../pages/Contact.vue";
import Gallery from "../pages/Gallery.vue";
import Legal from "../pages/Legal.vue";
import Profile from "../pages/Profile.vue";
import Car from "../pages/Car.vue";
import Checkout from "../pages/Checkout.vue";

const routes = [
    { path: "/", component: Index },
    { path: "/admin", component: Admin },
    { path: "/cars", component: Cars },
    { path: "/contact", component: Contact },
    { path: "/gallery", component: Gallery },
    { path: "/legal", component: Legal },
    { path: "/profile", component: Profile },
    { path: "/cars/:id", component: Car, props: true },
    { path: "/checkout", component: Checkout },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

// Navigation guards

router.beforeEach(async (to, from, next) => {
    const { fetchUser, isAdmin, isCustomer, isInitialized } = useAuth();

    // Wait until verify user data
    if (!isInitialized.value) {
        await fetchUser();
    }

    // Route /admin
    if (to.path === "/admin") {
        if (isAdmin.value) {
            next();
        } else {
            alert("Access denied: Administrator role required");
            next("/");
        }
    }

    // Route /profile
    else if (to.path === "/profile") {
        if (isCustomer.value) {
            next();
        } else {
            alert("Access denied: User authentication required");
            next("/");
        }
    }

    // Other routes
    else {
        next();
    }
});
