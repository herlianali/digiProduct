<!-- ComponentsV2/CartSidebar.vue -->
<template>
    <Transition name="backdrop">
        <div v-if="open" class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-[1100]" @click="$emit('close')" />
    </Transition>

    <Transition name="slide">
        <div v-if="open" class="fixed top-0 right-0 h-full w-full max-w-[480px] bg-[#0d0d0d] z-[1101] flex flex-col shadow-2xl">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-white/10 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <span class="text-white font-black text-xl tracking-tight">Your Cart</span>
                    <span v-if="cart.count.value > 0" class="bg-[#4dfa03] text-black text-xs font-black px-2 py-0.5 rounded-full">
                        {{ cart.count.value }}
                    </span>
                </div>
                <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Scrollable body -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3 custom-scrollbar">

                <!-- Empty state -->
                <div v-if="cart.items.value.length === 0" class="flex flex-col items-center justify-center h-full gap-4 py-16">
                    <div class="text-6xl opacity-30">🛒</div>
                    <p class="text-white/40 font-semibold text-center">Your cart is empty.<br>Add some products!</p>
                </div>

                <!-- Cart Items -->
                <TransitionGroup name="item" tag="div" class="space-y-3">
                    <div
                        v-for="item in cart.items.value"
                        :key="item.id"
                        class="flex items-center gap-4 bg-white/5 hover:bg-white/8 rounded-2xl p-3 transition-colors group"
                    >
                        <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 bg-white/10">
                            <img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.name" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full flex items-center justify-center text-white/20 text-xl">🎨</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-bold text-sm truncate">{{ item.name }}</p>
                            <p class="text-[#4dfa03] font-black text-sm mt-0.5">
                                {{ item.is_free ? 'Free' : formatPrice(item.price) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button @click="cart.updateQty(item.id, item.quantity - 1)" class="w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white text-sm font-bold">−</button>
                            <span class="text-white font-bold text-sm w-4 text-center">{{ item.quantity }}</span>
                            <button @click="cart.updateQty(item.id, item.quantity + 1)" class="w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white text-sm font-bold">+</button>
                        </div>
                        <button @click="cart.remove(item.id)" class="opacity-0 group-hover:opacity-100 w-6 h-6 rounded-full bg-red-500/20 hover:bg-red-500/40 flex items-center justify-center transition-all text-red-400 flex-shrink-0">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </TransitionGroup>

                <!-- Order Form -->
                <div v-if="cart.items.value.length > 0" class="mt-6 pt-6 border-t border-white/10 space-y-4">
                    <p class="text-white font-black text-sm tracking-widest uppercase">Order Info</p>

                    <!-- Name -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">Full Name <span class="text-red-400">*</span></label>
                        <input v-model="form.name" type="text" placeholder="Your name"
                            class="w-full bg-white/8 border rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors"
                            :class="errors.name ? 'border-red-400' : 'border-white/15 focus:border-[#4dfa03]/60'" />
                        <p v-if="errors.name" class="text-red-400 text-xs mt-1">{{ errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">Email <span class="text-red-400">*</span></label>
                        <input v-model="form.email" type="email" placeholder="your@email.com"
                            class="w-full bg-white/8 border rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors"
                            :class="errors.email ? 'border-red-400' : 'border-white/15 focus:border-[#4dfa03]/60'" />
                        <p v-if="errors.email" class="text-red-400 text-xs mt-1">{{ errors.email }}</p>
                    </div>

                    <!-- Phone / WhatsApp -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">
                            WhatsApp / Phone <span class="text-white/25">(optional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30 text-sm">📱</span>
                            <input v-model="form.phone" type="tel" placeholder="08123456789"
                                class="w-full bg-white/8 border border-white/15 focus:border-[#4dfa03]/60 rounded-xl pl-10 pr-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors" />
                        </div>
                    </div>

                    <!-- Company (optional) -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">
                            Company <span class="text-white/25">(optional)</span>
                        </label>
                        <input v-model="form.company" type="text" placeholder="Your company name"
                            class="w-full bg-white/8 border border-white/15 focus:border-[#4dfa03]/60 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors" />
                    </div>

                    <!-- City (optional) -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">
                            City <span class="text-white/25">(optional)</span>
                        </label>
                        <input v-model="form.city" type="text" placeholder="Surabaya"
                            class="w-full bg-white/8 border border-white/15 focus:border-[#4dfa03]/60 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">
                            Notes <span class="text-white/25">(optional)</span>
                        </label>
                        <textarea v-model="form.notes" placeholder="Any special request?" rows="2"
                            class="w-full bg-white/8 border border-white/15 focus:border-[#4dfa03]/60 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors resize-none" />
                    </div>

                    <!-- Voucher -->
                    <div>
                        <label class="text-white/50 text-xs font-semibold block mb-1.5">Voucher Code</label>
                        <div class="flex gap-2">
                            <input v-model="form.voucher_code" type="text" placeholder="SUPPLAY20"
                                class="flex-1 bg-white/8 border border-white/15 focus:border-[#4dfa03]/60 rounded-xl px-4 py-3 text-white text-sm placeholder-white/20 outline-none transition-colors uppercase" />
                            <button @click="applyVoucher" :disabled="isApplyingVoucher || !form.voucher_code"
                                class="px-4 py-3 rounded-xl text-sm font-bold transition-all disabled:opacity-40"
                                :class="voucherApplied ? 'bg-[#4dfa03] text-black' : 'bg-white/10 hover:bg-white/20 text-white'">
                                <span v-if="isApplyingVoucher">...</span>
                                <span v-else-if="voucherApplied">✓ Applied</span>
                                <span v-else>Apply</span>
                            </button>
                        </div>
                        <p v-if="voucherError" class="text-red-400 text-xs mt-1">{{ voucherError }}</p>
                        <p v-if="voucherApplied" class="text-[#4dfa03] text-xs mt-1">Discount {{ discountDisplay }} applied!</p>
                    </div>

                    <!-- General error -->
                    <p v-if="errors.general" class="text-red-400 text-xs bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3">
                        {{ errors.general }}
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div v-if="cart.items.value.length > 0" class="flex-shrink-0 px-6 py-5 border-t border-white/10 bg-[#0d0d0d] space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between text-white/60 text-sm">
                        <span>Subtotal</span>
                        <span>{{ formatPrice(cart.subtotal.value) }}</span>
                    </div>
                    <div v-if="discountAmount > 0" class="flex justify-between text-[#4dfa03] text-sm">
                        <span>Discount</span>
                        <span>− {{ formatPrice(discountAmount) }}</span>
                    </div>
                    <div class="flex justify-between text-white font-black text-lg pt-1 border-t border-white/10">
                        <span>Total</span>
                        <span class="text-[#4dfa03]">{{ formatPrice(total) }}</span>
                    </div>
                </div>

                <button @click="submitOrder" :disabled="isSubmitting"
                    class="w-full bg-[#4dfa03] hover:bg-green-300 disabled:opacity-50 disabled:cursor-not-allowed text-black font-black text-base py-4 rounded-full flex items-center justify-center gap-2 transition-all active:scale-[0.98] shadow-lg shadow-green-500/20">
                    <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span>{{ isSubmitting ? 'Processing...' : 'Place Order' }}</span>
                    <svg v-if="!isSubmitting" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>

            <!-- Success state -->
            <Transition name="success">
                <div v-if="orderSuccess" class="absolute inset-0 bg-[#0d0d0d] flex flex-col items-center justify-center gap-6 z-10 px-8">
                    <div class="w-20 h-20 rounded-full bg-[#4dfa03]/15 border-2 border-[#4dfa03] flex items-center justify-center">
                        <svg class="w-10 h-10 text-[#4dfa03]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-black text-2xl">Order Placed!</p>
                        <p class="text-white/50 text-sm mt-2">
                            Confirmation sent to<br>
                            <span class="text-[#4dfa03]">{{ form.email }}</span>
                        </p>
                        <p v-if="orderNumber" class="text-white/30 text-xs mt-2">Order #{{ orderNumber }}</p>
                    </div>

                    <!-- Tombol WA -->
                    <a
                        v-if="whatsappUrl"
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 bg-[#25D366] hover:bg-green-400 text-white font-black px-6 py-3 rounded-full transition-all active:scale-95 shadow-lg"
                    >
                        <!-- WA Icon -->
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Konfirmasi via WhatsApp
                    </a>

                    <button @click="resetAndClose" class="px-8 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition-colors">
                        Continue Shopping
                    </button>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { useCart } from '@/Composables/useCart'

const props = defineProps({
    open: { type: Boolean, default: false },
})
const emit = defineEmits(['close'])
const cart = useCart()

const form = ref({
    name:         '',
    email:        '',
    phone:        '',
    company:      '',
    city:         '',
    notes:        '',
    voucher_code: '',
})

const errors           = ref({})
const isApplyingVoucher = ref(false)
const voucherApplied   = ref(false)
const voucherError     = ref('')
const appliedVoucher   = ref(null)
const discountDisplay  = ref('')
const isSubmitting     = ref(false)
const orderSuccess     = ref(false)
const orderNumber      = ref(null)
const whatsappUrl      = ref(null)

const discountAmount = computed(() => {
    if (!voucherApplied.value || !appliedVoucher.value) return 0
    const v = appliedVoucher.value
    return v.discount_type === 'percentage'
        ? (cart.subtotal.value * v.discount_value) / 100
        : Math.min(v.discount_value, cart.subtotal.value)
})

const total = computed(() => Math.max(0, cart.subtotal.value - discountAmount.value))

const formatPrice = (val) => {
    const num = parseFloat(val) || 0
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(num)
}

const applyVoucher = async () => {
    if (!form.value.voucher_code || isApplyingVoucher.value) return
    isApplyingVoucher.value = true
    voucherError.value      = ''
    voucherApplied.value    = false

    try {
        const { data } = await axios.post('/vouchers/validate', { code: form.value.voucher_code })
        appliedVoucher.value = data.voucher
        voucherApplied.value = true
        discountDisplay.value = data.voucher.discount_type === 'percentage'
            ? `${data.voucher.discount_value}%`
            : formatPrice(data.voucher.discount_value)
    } catch (err) {
        voucherError.value = err.response?.data?.message ?? 'Invalid voucher code.'
    } finally {
        isApplyingVoucher.value = false
    }
}

const validate = () => {
    const errs = {}
    if (!form.value.name.trim())                        errs.name  = 'Name is required.'
    if (!form.value.email.trim())                       errs.email = 'Email is required.'
    else if (!/\S+@\S+\.\S+/.test(form.value.email))   errs.email = 'Enter a valid email.'
    errors.value = errs
    return Object.keys(errs).length === 0
}

const submitOrder = async () => {
    if (!validate()) return
    isSubmitting.value = true
    errors.value = {}

    const payload = {
        customer_name:     form.value.name,
        customer_email:    form.value.email,
        customer_phone:    form.value.phone    || null,
        customer_whatsapp: form.value.phone    || null,   // sama dengan phone
        customer_company:  form.value.company  || null,
        customer_city:     form.value.city     || null,
        notes:             form.value.notes    || null,
        voucher_code:      voucherApplied.value ? form.value.voucher_code : null,
        items: cart.items.value.map(i => ({
            product_id:    i.id,
            product_title: i.name,
            price:         i.price,
            quantity:      i.quantity,
        })),
    }

    try {
        const { data } = await axios.post('/orders/guest', payload)
        orderNumber.value  = data.order_number
        whatsappUrl.value  = data.whatsapp_url   // ← simpan URL WA
        orderSuccess.value = true
        cart.clear()
    } catch (err) {
        if (err.response?.data?.errors) {
            const e = err.response.data.errors
            errors.value = {
                name:    e.customer_name?.[0]  ?? null,
                email:   e.customer_email?.[0] ?? null,
                general: e.items?.[0]          ?? null,
            }
        } else {
            errors.value = { general: err.response?.data?.message ?? `Error ${err.response?.status}: ${err.message}` }
        }
    } finally {
        isSubmitting.value = false
    }
}

const resetAndClose = () => {
    orderSuccess.value   = false
    orderNumber.value    = null
    whatsappUrl.value    = null
    form.value           = { name: '', email: '', phone: '', company: '', city: '', notes: '', voucher_code: '' }
    errors.value         = {}
    voucherApplied.value = false
    appliedVoucher.value = null
    emit('close')
}
</script>

<style scoped>
.backdrop-enter-active, .backdrop-leave-active { transition: opacity 0.3s ease; }
.backdrop-enter-from, .backdrop-leave-to { opacity: 0; }

.slide-enter-active { transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1); }
.slide-leave-active { transition: transform 0.3s cubic-bezier(0.55, 0, 0.55, 0.085); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }

.item-enter-active { transition: all 0.3s ease; }
.item-leave-active { transition: all 0.25s ease; }
.item-enter-from { opacity: 0; transform: translateX(20px); }
.item-leave-to   { opacity: 0; transform: translateX(-20px); height: 0; margin: 0; padding: 0; }

.success-enter-active { transition: opacity 0.4s ease; }
.success-leave-active { transition: opacity 0.3s ease; }
.success-enter-from, .success-leave-to { opacity: 0; }

.custom-scrollbar::-webkit-scrollbar        { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track  { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb  { background: rgba(255,255,255,0.15); border-radius: 4px; }
.bg-white\/8 { background-color: rgba(255,255,255,0.08); }
</style>
