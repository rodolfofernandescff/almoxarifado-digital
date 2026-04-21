<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Usuário');
const userPerfil = computed(() => page.props.auth?.user?.perfil ?? 'Colaborador');

const greeting = computed(() => {
    const hour = new Date().getHours();

    if (hour < 12) return 'Bom dia';
    if (hour < 18) return 'Boa tarde';
    return 'Boa noite';
});

const today = computed(() => {
    return new Intl.DateTimeFormat('pt-BR', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(new Date());
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <section class="flex flex-col items-center justify-start py-8 md:py-12">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">{{ today }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl md:text-4xl">
                    {{ greeting }}, <span class="text-emerald-600">{{ userName }}</span>.
                </h1>
            </div>
        </section>
    </AuthenticatedLayout>
</template>
