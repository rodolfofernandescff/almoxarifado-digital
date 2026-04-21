<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <transition name="fade">
        <div v-show="message" class="animate-fade-in">
            <p class="text-sm font-medium text-rose-600">
                {{ message }}
            </p>
        </div>
    </transition>
</template>
