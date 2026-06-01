// resources/js/Composables/useCart.js

import { ref, computed, inject, provide } from 'vue'

const CART_KEY = Symbol('cart')

export function createCart() {
    const items = ref([])

    const count = computed(() =>
        items.value.reduce((sum, item) => sum + item.quantity, 0)
    )

    const subtotal = computed(() =>
        items.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
    )

    // Strip karakter non-numerik: "$25.00" → 25, "Rp 25.000" → 25, "25.00" → 25
    const parsePrice = (val) => {
        if (val === null || val === undefined) return 0
        const cleaned = String(val).replace(/[^0-9.]/g, '')
        return parseFloat(cleaned) || 0
    }

    const add = (product) => {
        const existing = items.value.find(i => i.id === product.id)
        if (existing) {
            existing.quantity++
        } else {
            items.value.push({
                id: product.id,
                name: product.name ?? product.title ?? '',
                price: product.is_free ? 0 : parsePrice(product.final_price ?? product.price),
                is_free: product.is_free ?? false,
                thumbnail: product.thumbnail ?? product.image ?? null,
                quantity: 1,
            })
        }
    }

    const remove = (productId) => {
        items.value = items.value.filter(i => i.id !== productId)
    }

    const updateQty = (productId, qty) => {
        const item = items.value.find(i => i.id === productId)
        if (!item) return
        if (qty <= 0) remove(productId)
        else item.quantity = qty
    }

    const clear = () => {
        items.value = []
    }

    return { items, count, subtotal, add, remove, updateQty, clear }
}

export function provideCart() {
    const cart = createCart()
    provide(CART_KEY, cart)
    return cart
}

export function useCart() {
    const cart = inject(CART_KEY)
    if (!cart) throw new Error('useCart() dipanggil di luar CartProvider. Pastikan provideCart() dipanggil di root layout.')
    return cart
}
