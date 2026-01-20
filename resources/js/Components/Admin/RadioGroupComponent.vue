<template>
    <div class="space-y-2" :class="wrapperClass">
        <label v-if="label" class="block font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div class="flex flex-wrap gap-4">
            <label 
                v-for="option in options" 
                :key="option.value"
                class="inline-flex items-center cursor-pointer"
            >
                <input 
                    type="radio" 
                    :value="option.value"
                    :checked="modelValue === option.value"
                    @change="$emit('update:modelValue', option.value)"
                    :class="[
                        'form-radio h-5 w-5',
                        error ? 'text-red-500' : 'text-blue-600'
                    ]"
                    :disabled="disabled"
                />
                <span class="ml-2 text-gray-700">{{ option.label }}</span>
            </label>
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
    error: String,
    hint: String,
    required: Boolean,
    disabled: Boolean,
    wrapperClass: String
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.form-radio:checked {
    @apply ring-2 ring-offset-2 ring-blue-500;
}
</style>