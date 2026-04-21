<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    requisicoes: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);
const errorMessage = computed(() => page.props.flash?.error ?? null);

/* ------------------------------------------------------------------ */
/*  Modal de análise                                                   */
/* ------------------------------------------------------------------ */
const showModal = ref(false);
const selectedRequisicao = ref(null);
const modoReprovacao = ref(false);

const abrirAnalise = (requisicao) => {
    selectedRequisicao.value = requisicao;
    modoReprovacao.value = false;
    formAprovar.reset();
    formAprovar.clearErrors();
    formReprovar.reset();
    formReprovar.clearErrors();
    showModal.value = true;
};

const fecharModal = () => {
    showModal.value = false;
    selectedRequisicao.value = null;
    modoReprovacao.value = false;
};

/* ------------------------------------------------------------------ */
/*  Formulários (Inertia useForm p/ controle de processing state)      */
/* ------------------------------------------------------------------ */
const formAprovar = useForm({ observacao: '' });
const formReprovar = useForm({ observacao: '' });

const aprovar = () => {
    if (!selectedRequisicao.value) return;
    formAprovar.post(route('admin.requisicoes.aprovar', selectedRequisicao.value.id), {
        preserveScroll: true,
        onSuccess: () => fecharModal(),
    });
};

const iniciarReprovacao = () => {
    modoReprovacao.value = true;
};

const cancelarReprovacao = () => {
    modoReprovacao.value = false;
    formReprovar.reset();
    formReprovar.clearErrors();
};

const reprovar = () => {
    if (!selectedRequisicao.value) return;
    formReprovar.post(route('admin.requisicoes.reprovar', selectedRequisicao.value.id), {
        preserveScroll: true,
        onSuccess: () => fecharModal(),
    });
};

/* ------------------------------------------------------------------ */
/*  Helpers de apresentação                                            */
/* ------------------------------------------------------------------ */
const statusMap = {
    pendente:   { label: 'Pendente',   css: 'bg-amber-100 text-amber-700' },
    aprovado:   { label: 'Aprovado',   css: 'bg-emerald-100 text-emerald-700' },
    recusado:   { label: 'Recusado',   css: 'bg-rose-100 text-rose-700' },
    finalizado: { label: 'Finalizado', css: 'bg-slate-200 text-slate-700' },
};
const statusLabel = (s) => statusMap[s]?.label ?? s;
const statusClass = (s) => statusMap[s]?.css ?? 'bg-slate-100 text-slate-700';
</script>

<template>
    <Head title="Gestão de Requisições" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Gestão de Requisições</h2>
                <p class="mt-1 text-sm text-slate-500">Analise, aprove ou reprove as requisições de materiais.</p>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Flash de Sucesso -->
            <div v-if="successMessage" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ successMessage }}
                </div>
            </div>

            <!-- Flash de Erro -->
            <div v-if="errorMessage" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                    {{ errorMessage }}
                </div>
            </div>

            <!-- Tabela de Requisições -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Requisitante</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Setor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Itens</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="req in requisicoes.data" :key="req.id" class="transition hover:bg-slate-50/60">
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">#{{ req.id }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <div class="font-medium">{{ req.solicitante?.name || '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ req.solicitante?.email || '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ req.solicitante?.setor || '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ req.total_itens }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ req.created_at }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(req.status)">
                                        {{ statusLabel(req.status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold transition"
                                        :class="req.status === 'pendente' ? 'bg-sky-900 text-white hover:bg-sky-950' : 'border border-slate-300 text-slate-600 hover:bg-slate-50'"
                                        @click="abrirAnalise(req)"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ req.status === 'pendente' ? 'Analisar' : 'Detalhes' }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="requisicoes.data.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">Nenhuma requisição encontrada.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
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
        </div>

        <!-- ============================================================ -->
        <!--  Modal de Análise — layout: header / scroll-body / footer    -->
        <!-- ============================================================ -->
        <Modal :show="showModal" max-width="2xl" @close="fecharModal">
            <div v-if="selectedRequisicao" class="flex max-h-[85vh] flex-col">

                <!-- ─── HEADER (fixo) ─── -->
                <div class="shrink-0 flex items-start justify-between gap-4 border-b border-slate-200 px-6 pt-5 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Requisição #{{ selectedRequisicao.id }}</h3>
                        <p class="mt-0.5 text-sm text-slate-500">{{ selectedRequisicao.solicitante?.name }} · {{ selectedRequisicao.solicitante?.setor || '-' }}</p>
                        <p class="text-xs text-slate-400">{{ selectedRequisicao.created_at }}</p>
                    </div>
                    <span class="shrink-0 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold" :class="statusClass(selectedRequisicao.status)">
                        {{ statusLabel(selectedRequisicao.status) }}
                    </span>
                </div>

                <!-- ─── BODY (scrollável) ─── -->
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">

                    <!-- Justificativa -->
                    <div v-if="selectedRequisicao.justificativa" class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Justificativa do requisitante</p>
                        <p class="mt-1 text-sm text-slate-700">{{ selectedRequisicao.justificativa }}</p>
                    </div>

                    <!-- Observação Admin (se já processada) -->
                    <div v-if="selectedRequisicao.observacao_admin"
                        class="rounded-lg border px-4 py-3"
                        :class="selectedRequisicao.status === 'recusado' ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50'"
                    >
                        <p class="text-xs font-semibold uppercase tracking-wide" :class="selectedRequisicao.status === 'recusado' ? 'text-rose-500' : 'text-emerald-500'">
                            {{ selectedRequisicao.status === 'recusado' ? 'Motivo da reprovação' : 'Observação do gestor' }}
                        </p>
                        <p class="mt-1 text-sm" :class="selectedRequisicao.status === 'recusado' ? 'text-rose-700' : 'text-emerald-700'">
                            {{ selectedRequisicao.observacao_admin }}
                        </p>
                    </div>

                    <!-- Aprovador -->
                    <p v-if="selectedRequisicao.aprovador" class="text-xs text-slate-500">
                        Processado por: <span class="font-medium text-slate-700">{{ selectedRequisicao.aprovador }}</span>
                    </p>

                    <!-- Tabela de itens -->
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Itens solicitados</h4>
                        <div class="mt-2 overflow-x-auto rounded-lg border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Produto</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Qtd. Pedida</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Estoque</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Situação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    <tr v-for="item in selectedRequisicao.itens" :key="item.id">
                                        <td class="px-4 py-2 text-slate-900">
                                            <span class="font-medium">{{ item.produto }}</span>
                                            <span class="ml-1 text-xs text-slate-400">({{ item.unidade_medida }})</span>
                                        </td>
                                        <td class="px-4 py-2 font-medium text-slate-700">{{ item.quantidade_pedida }}</td>
                                        <td class="px-4 py-2">
                                            <span :class="item.estoque_atual < item.quantidade_pedida ? 'font-semibold text-rose-600' : 'text-slate-700'">{{ item.estoque_atual }}</span>
                                            <span v-if="item.estoque_atual < item.quantidade_pedida" class="ml-1 text-xs text-rose-500">(insuficiente)</span>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span v-if="item.quantidade_entregue !== null" class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">Entregue</span>
                                            <span v-else class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Aguardando</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Observação para aprovação (opcional) -->
                    <div v-if="selectedRequisicao.status === 'pendente' && !modoReprovacao">
                        <label for="obs-aprovar" class="text-sm font-semibold text-slate-700">
                            Observação <span class="text-xs font-normal text-slate-400">(opcional)</span>
                        </label>
                        <textarea
                            id="obs-aprovar"
                            v-model="formAprovar.observacao"
                            rows="2"
                            class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 focus:outline-none"
                            placeholder="Adicione uma observação sobre esta aprovação..."
                        />
                    </div>

                    <!-- Motivo para reprovação (obrigatório) -->
                    <div v-if="selectedRequisicao.status === 'pendente' && modoReprovacao">
                        <label for="obs-reprovar" class="text-sm font-semibold text-slate-700">
                            Motivo da reprovação <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="obs-reprovar"
                            v-model="formReprovar.observacao"
                            rows="3"
                            class="mt-1.5 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none"
                            placeholder="Descreva o motivo da reprovação..."
                        />
                        <InputError class="mt-1.5" :message="formReprovar.errors.observacao" />
                    </div>
                </div>

                <!-- ─── FOOTER (fixo, sempre visível) ─── -->
                <div class="shrink-0 border-t border-slate-200 bg-slate-50 px-6 py-3">

                    <!-- PENDENTE — modo aprovação -->
                    <div v-if="selectedRequisicao.status === 'pendente' && !modoReprovacao" class="flex items-center justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:opacity-50"
                            :disabled="formAprovar.processing || formReprovar.processing"
                            @click="iniciarReprovacao"
                        >
                            Reprovar
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-50"
                            :disabled="formAprovar.processing"
                            @click="aprovar"
                        >
                            <span v-if="formAprovar.processing" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                                Aprovando...
                            </span>
                            <span v-else>Aprovar Requisição</span>
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800"
                            @click="fecharModal"
                        >
                            Fechar
                        </button>
                    </div>

                    <!-- PENDENTE — modo reprovação -->
                    <div v-else-if="selectedRequisicao.status === 'pendente' && modoReprovacao" class="flex items-center justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-white"
                            :disabled="formReprovar.processing"
                            @click="cancelarReprovacao"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-rose-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:opacity-50"
                            :disabled="formReprovar.processing || !formReprovar.observacao.trim()"
                            @click="reprovar"
                        >
                            <span v-if="formReprovar.processing" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                                Reprovando...
                            </span>
                            <span v-else>Confirmar Reprovação</span>
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800"
                            @click="fecharModal"
                        >
                            Fechar
                        </button>
                    </div>

                    <!-- JÁ PROCESSADA -->
                    <div v-else class="flex justify-end">
                        <button
                            type="button"
                            class="rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800"
                            @click="fecharModal"
                        >
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
