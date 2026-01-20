<template>
    <div class="space-y-2" :class="wrapperClass">
        <label v-if="label" :for="id" class="block font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div class="relative">
            <span 
                v-if="prefix" 
                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
            >
                {{ prefix }}
            </span>
            
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :class="[
                    'w-full px-4 py-2.5 border rounded-lg outline-none transition',
                    prefix ? 'pl-10' : '',
                    suffix ? 'pr-10' : '',
                    error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' 
                          : 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    disabled ? 'bg-gray-100 cursor-not-allowed opacity-50' : ''
                ]"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
            />
            
            <span 
                v-if="suffix" 
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
            >
                {{ suffix }}
            </span>
        </div>
        
        <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
        <p v-if="hint && !error" class="text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    type: {
        type: String,
        default: 'text'
    },
    placeholder: String,
    id: String,
    error: String,
    hint: String,
    required: Boolean,
    disabled: Boolean,
    prefix: String,
    suffix: String,
    wrapperClass: String
})

defineEmits(['update:modelValue'])
</script>