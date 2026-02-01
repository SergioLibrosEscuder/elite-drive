import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";

export const useCartStore = defineStore("cart", () => {
    const cartItems = ref(JSON.parse(localStorage.getItem("cartItems")) || []);

    const count = computed(() => cartItems.value.length);

    const total = computed(() =>
        cartItems.value.reduce(
            (total, item) => Number(total) + Number(item.price),
            0,
        ),
    );

    function addToCart(vehicle, startDate, endDate) {
        if (vehicle.status !== "available") {
            alert("This vehicle is not available for rent.");
            return;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);
        const timeDiff = end - start;
        const totalHours = timeDiff / (1000 * 3600);

        if (totalHours <= 0) {
            alert("End date must be after start date.");
            return;
        }

        const price = Number(totalHours * vehicle.hourly_price).toFixed(2);

        const newItem = {
            id: Date.now(),
            vehicle: vehicle,
            start: startDate,
            end: endDate,
            price: price,
        };

        cartItems.value.push(newItem);
        alert("Vehicle added to cart.");
    }

    function removeFromCart(productId) {
        cartItems.value = cartItems.value.filter(
            (item) => item.id !== productId,
        );
    }

    function clearCart() {
        cartItems.value = [];
    }

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
