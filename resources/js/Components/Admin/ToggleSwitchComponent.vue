<template>
    <div class="space-y-2" :class="wrapperClass">
        <label v-if="label" class="block font-medium text-gray-700 mb-2">
            {{ label }}
        </label>
        
        <label class="flex items-center cursor-pointer">
            <div class="relative">
                <input 
                    type="checkbox" 
                    :checked="modelValue"
                    @change="$emit('update:modelValue', $event.target.checked)"
                    class="sr-only"
                    :disabled="disabled"
                />
                <div 
                    :class="[
                        'block w-14 h-8 rounded-full transition-colors',
                        modelValue ? 'bg-green-500' : 'bg-gray-200',
                        disabled ? 'opacity-50 cursor-not-allowed' : ''
                    ]"
                ></div>
                <div 
                    :class="[
                        'dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform',
                        modelValue ? 'transform translate-x-6' : ''
                    ]"
                ></div>
            </div>
            <span class="ml-3 text-gray-700">
                {{ modelValue ? activeLabel || 'Active' : inactiveLabel || 'Inactive' }}
            </span>
        </label>
        
        <p v-if="hint" class="text-sm text-gray-500 mt-1">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: Boolean,
    label: String,
    activeLabel: String,
    inactiveLabel: String,
    hint: String,
    disabled: Boolean,
    wrapperClass: String
})

defineEmits(['update:modelValue'])
</script>