// Sergio Libros

import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";
import { useToast } from "../composables/useToast";

export const useCartStore = defineStore("cart", () => {
    // Toast object
    const toast = useToast();

    // Get items from local storage
    const cartItems = ref(JSON.parse(localStorage.getItem("cartItems")) || []);

    // Number of items in cart
    const count = computed(() => cartItems.value.length);

    // Total amount of price in cart
    const total = computed(() =>
        cartItems.value.reduce(
            (total, item) => Number(total) + Number(item.price),
            0,
        ),
    );

    // Add to cart function
    function addToCart(vehicle, startDate, endDate) {
        // If no vehicle availability, cannot add to cart
        if (vehicle.status !== "available") {
            toast.error(
                "This vehicle is not available for rent.",
                "Vehicle Info",
            );
            return;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);
        // Get difference between selected hours
        const timeDiff = end - start;
        const totalHours = timeDiff / (1000 * 3600);

        // Error if end date is before start date
        if (totalHours <= 0) {
            toast.warning(
                "End date must be after start date.",
                "Dates Validation",
            );
            return;
        }

        // Calculate price
        const price = Number(totalHours * vehicle.hourly_price).toFixed(2);

        // Template for new items
        const newItem = {
            id: Date.now(),
            vehicle: vehicle,
            start: startDate,
            end: endDate,
            price: price,
        };

        // Add new item to cart
        cartItems.value.push(newItem);
        toast.success("Vehicle added to cart.");
    }

    // Function to remove an item from cart
    function removeFromCart(productId) {
        cartItems.value = cartItems.value.filter(
            (item) => item.id !== productId,
        );
    }

    // Function to completely erase cart items
    function clearCart() {
        cartItems.value = [];
    }

    // Watcher to add new items to local storage
    watch(
        cartItems,
        (newCartItems) => {
            localStorage.setItem("cartItems", JSON.stringify(newCartItems));
        },
        { deep: true },
    );

    return {
        cartItems,
        count,
        total,
        addToCart,
        removeFromCart,
        clearCart,
    };
});
