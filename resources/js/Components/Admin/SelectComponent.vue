<template>
    <div class="space-y-2" :class="wrapperClass">
        <label v-if="label" :for="id" class="block font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <select
            :id="id"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :class="[
                'w-full px-4 py-2.5 border rounded-lg outline-none transition appearance-none bg-white',
                error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' 
                      : 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                disabled ? 'bg-gray-100 cursor-not-allowed opacity-50' : ''
            ]"
            :disabled="disabled"
            :required="required"
        >
            <option value="" disabled v-if="placeholder">{{ placeholder }}</option>
            <option 
                v-for="option in options" 
                :key="option.value" 
                :value="option.value"
                :selected="option.value === modelValue"
            >
                {{ option.label }}
            </option>
        </select>
        
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
            </svg>
        </div>
        
        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
        <p v-if="hint && !error" class="text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    options: {
        type: Array,
        default: () => []
    },
    placeholder: String,
    id: String,
    error: String,
    hint: String,
    required: Boolean,
    disabled: Boolean,
    wrapperClass: String
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
</style>