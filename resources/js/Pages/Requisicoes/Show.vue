<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    requisicao: {
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
    <Head :title="`Requisição #${props.requisicao.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Requisição #{{ props.requisicao.id }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Detalhamento completo da solicitação.</p>
                </div>
                <Link
                    :href="route('requisicoes.index')"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Voltar
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
                        <span
                            class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="statusClass(props.requisicao.status)"
                        >
                            {{ statusLabel(props.requisicao.status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Solicitante</p>
                        <p class="mt-2 text-sm font-medium text-slate-900">{{ props.requisicao.solicitante.name || '-' }}</p>
                        <p class="text-xs text-slate-500">{{ props.requisicao.solicitante.email || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Data de criação</p>
                        <p class="mt-2 text-sm text-slate-900">{{ props.requisicao.created_at || '-' }}</p>
                        <p class="text-xs text-slate-500">Setor: {{ props.requisicao.solicitante.setor || '-' }}</p>
                    </div>
                </div>

                <div v-if="props.requisicao.justificativa" class="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Justificativa</p>
                    <p class="mt-1 text-sm text-slate-700">{{ props.requisicao.justificativa }}</p>
                </div>

                <div v-if="props.requisicao.observacao_admin" class="mt-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Observação administrativa</p>
                    <p class="mt-1 text-sm text-slate-700">{{ props.requisicao.observacao_admin }}</p>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Itens da Requisição</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Produto</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Qtd. Pedida</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Qtd. Entregue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="item in props.requisicao.itens" :key="item.id">
                                <td class="px-4 py-3 text-sm text-slate-900">{{ item.produto || '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ item.quantidade_pedida }} {{ item.unidade_medida || '' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ item.quantidade_entregue ?? '-' }}
                                </td>
                            </tr>
                            <tr v-if="props.requisicao.itens.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Esta requisição não possui itens.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
