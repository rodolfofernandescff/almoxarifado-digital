<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    produto: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="`Produto: ${produto.nome}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ produto.nome }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Detalhes do produto.</p>
                </div>
                <Link
                    :href="route('produtos.index')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Voltar
                </Link>
            </div>
        </template>

        <div
            class="animate-fade-in rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            style="animation: fade-in 0.5s ease-out, slide-in-from-bottom 0.5s ease-out"
        >
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Categoria</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ produto.categoria?.nome || '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Unidade</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ produto.unidade_medida }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estoque Atual</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ produto.estoque_atual }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estoque Minimo</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ produto.estoque_minimo }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Descricao</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ produto.descricao || '-' }}</dd>
                </div>
            </dl>
        </div>
    </AuthenticatedLayout>
</template>
