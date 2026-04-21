<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);
const isFocused = ref(false);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        v-model="model"
        @focus="isFocused = true"
        @blur="isFocused = false"
        class="w-full rounded-lg border-2 border-slate-200 bg-white px-4 py-3 text-slate-900 transition-all duration-300 ease-out placeholder:text-slate-400 focus:border-emerald-500 focus:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-200"
        :class="isFocused ? 'shadow-md' : 'shadow-sm'"
    />
</template>
