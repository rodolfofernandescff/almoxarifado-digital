<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    requisicoes: {
        type: Object,
        required: true,
    },
});

const statusLabel = (status) => {
    const labels = {
        pendente: 'Pendente',
        aprovado: 'Aprovado',
        recusado: 'Recusado',
        finalizado: 'Finalizado',
    };

    return labels[status] ?? status;
};

const statusClass = (status) => {
    const classes = {
        pendente: 'bg-amber-100 text-amber-700',
        aprovado: 'bg-emerald-100 text-emerald-700',
        recusado: 'bg-rose-100 text-rose-700',
        finalizado: 'bg-slate-200 text-slate-700',
    };

    return classes[status] ?? 'bg-slate-100 text-slate-700';
};
</script>

<template>
    <Head title="Requisições" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Requisições</h2>
                <p class="mt-1 text-sm text-slate-500">Painel de acompanhamento operacional de pedidos.</p>
            </div>
        </template>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Solicitante</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Setor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Total Itens</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Data</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr v-for="requisicao in requisicoes.data" :key="requisicao.id">
                            <td class="px-4 py-3 text-sm font-medium text-slate-900">#{{ requisicao.id }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ requisicao.solicitante || '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ requisicao.setor || '-' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="statusClass(requisicao.status)"
                                >
                                    {{ statusLabel(requisicao.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ requisicao.total_itens }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ requisicao.created_at }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="route('requisicoes.show', requisicao.id)"
                                    class="text-sm font-medium text-sky-800 hover:underline"
                                >
                                    Detalhes
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="requisicoes.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                Nenhuma requisição encontrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center gap-2 border-t border-slate-200 px-4 py-3">
                <Link
                    v-for="link in requisicoes.links"
                    :key="`${link.label}-${link.url}`"
                    :href="link.url || '#'"
                    class="rounded-md border px-3 py-1.5 text-sm"
                    :class="[
                        link.active ? 'border-sky-900 bg-sky-900 text-white' : 'border-slate-300 text-slate-700',
                        !link.url ? 'pointer-events-none opacity-50' : 'hover:bg-slate-50',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
