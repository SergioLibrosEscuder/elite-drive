import { createRouter, createWebHistory } from "vue-router";
import { useAuth } from "../composables/useAuth";
import { useToast } from "../composables/useToast";

import Index from "../pages/Index.vue";
import Admin from "../pages/Admin.vue";
import Cars from "../pages/Cars.vue";
import Contact from "../pages/Contact.vue";
import Gallery from "../pages/Gallery.vue";
import Legal from "../pages/Legal.vue";
import Profile from "../pages/Profile.vue";
import Car from "../pages/Car.vue";
import Checkout from "../pages/Checkout.vue";

// All view routes
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

// Router set
const router = createRouter({
    history: createWebHistory(),
    routes,
    // How scroll behavies
    scrollBehavior(to, from, savePosition) {
        return { top: 0, behavior: "smooth" };
    },
});

export default router;

// Toast object
const toast = useToast();

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
            toast.error("You must be an Admin to access this page", "Security");
            next("/");
        }
    }

    // Route /profile
    else if (to.path === "/profile") {
        if (isCustomer.value) {
            next();
        } else {
            toast.error(
                "You must be an Authorized Customer to access this page",
                "Security",
            );
            next("/");
        }
    }

    // Other routes, proceed
    else {
        next();
    }
});
