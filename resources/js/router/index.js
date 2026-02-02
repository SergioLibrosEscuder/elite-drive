import { createRouter, createWebHistory } from "vue-router";
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

router.beforeEach((to, from, next) => {
    const userRole = localStorage.getItem("user_role");

    // Route /admin
    if (to.path === "/admin") {
        if (userRole === "admin") {
            next();
        } else {
            alert("Access denied: Administrator role required");
            next("/");
        }
    }

    // Route /profile
    else if (to.path === "/profile") {
        if (userRole === "customer") {
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
