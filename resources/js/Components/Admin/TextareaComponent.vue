<template>
    <div class="space-y-2" :class="wrapperClass">
        <label v-if="label" :for="id" class="block font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <textarea
            :id="id"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'w-full px-4 py-2.5 border rounded-lg outline-none transition resize-y',
                error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' 
                      : 'border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                disabled ? 'bg-gray-100 cursor-not-allowed opacity-50' : ''
            ]"
            :placeholder="placeholder"
            :rows="rows"
            :disabled="disabled"
            :required="required"
        ></textarea>
        
        <div class="flex justify-between">
            <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
            <p v-else-if="hint" class="text-sm text-gray-500">{{ hint }}</p>
            
            <p v-if="maxLength" class="text-sm text-gray-500 ml-auto">
                {{ modelValue?.length || 0 }}/{{ maxLength }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: String,
    label: String,
    placeholder: String,
    id: String,
    error: String,
    hint: String,
    required: Boolean,
    disabled: Boolean,
    rows: {
        type: Number,
        default: 4
    },
    maxLength: Number,
    wrapperClass: String
})

defineEmits(['update:modelValue'])
</script>