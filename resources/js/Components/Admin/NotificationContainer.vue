<template>
    <div class="fixed top-4 right-4 z-50 space-y-4">
        <TransitionGroup name="notification-list">
            <component
                v-for="notification in notifications"
                :key="notification.id"
                :is="getComponent(notification.type)"
                v-bind="notification.props"
                @close="removeNotification(notification.id)"
            />
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import SuccessNotification from '@/Components/Admin/SuccessNotification.vue'
import WarningNotification from '@/Components/Admin/WarningNotification.vue'
import ErrorNotification from '@/Components/Admin/ErrorNotification.vue'
import InfoNotification from '@/Components/Admin/InfoNotification.vue'

const props = defineProps({
    limit: {
        type: Number,
        default: 5
    }
})

const notifications = ref([])

const components = {
    success: SuccessNotification,
    warning: WarningNotification,
    error: ErrorNotification,
    info: InfoNotification
}

const getComponent = (type) => {
    return components[type] || components.info
}

const showNotification = (type, props) => {
    const id = Date.now() + Math.random()
    
    const notification = {
        id,
        type,
        props
    }
    
    // Add to beginning of array (newest on top)
    notifications.value.unshift(notification)
    
    // Limit number of notifications
    if (notifications.value.length > props.limit) {
        notifications.value = notifications.value.slice(0, props.limit)
    }
    
    return id
}

const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index !== -1) {
        notifications.value.splice(index, 1)
    }
}

// Helper methods for each notification type
const success = (message, title = 'Success!', options = {}) => {
    return showNotification('success', { 
        title, 
        message, 
        ...options 
    })
}

const warning = (message, title = 'Warning!', options = {}) => {
    return showNotification('warning', { 
        title, 
        message, 
        ...options 
    })
}

const error = (message, title = 'Error!', options = {}) => {
    return showNotification('error', { 
        title, 
        message, 
        ...options 
    })
}

const info = (message, title = 'Info', options = {}) => {
    return showNotification('info', { 
        title, 
        message, 
        ...options 
    })
}

// Expose methods
defineExpose({
    success,
    warning,
    error,
    info,
    removeNotification,
    clearAll: () => { notifications.value = [] }
})
</script>

<style scoped>
.notification-list-enter-active,
.notification-list-leave-active {
    transition: all 0.3s ease;
}

.notification-list-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.notification-list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.notification-list-move {
    transition: transform 0.3s;
}
</style>