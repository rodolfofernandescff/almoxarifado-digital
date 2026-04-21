<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({
    produtos: {
        type: Array,
        required: true,
    },
    minhasRequisicoes: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);

const form = useForm({
    justificativa: '',
    itens: [],
});

const carrinho = reactive({});

const itensSelecionados = computed(() => {
    return props.produtos
        .map((produto) => {
            const quantidade = Number(carrinho[produto.id] ?? 0);

            return {
                ...produto,
                quantidade: Number.isFinite(quantidade) ? quantidade : 0,
            };
        })
        .filter((item) => item.quantidade > 0);
});

const totalItens = computed(() => {
    return itensSelecionados.value.reduce((acc, item) => acc + item.quantidade, 0);
});

const totalProdutos = computed(() => itensSelecionados.value.length);

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

const definirQuantidade = (produto, valor) => {
    let quantidade = Number.parseInt(String(valor), 10);

    if (Number.isNaN(quantidade) || quantidade < 0) {
        quantidade = 0;
    }

    if (quantidade > produto.estoque_atual) {
        quantidade = produto.estoque_atual;
    }

    if (quantidade === 0) {
        delete carrinho[produto.id];
        return;
    }

    carrinho[produto.id] = quantidade;
};

const adicionarUm = (produto) => {
    definirQuantidade(produto, Number(carrinho[produto.id] ?? 0) + 1);
};

const removerUm = (produto) => {
    definirQuantidade(produto, Number(carrinho[produto.id] ?? 0) - 1);
};

const confirmarRequisicao = () => {
    form.clearErrors();
    form.itens = itensSelecionados.value.map((item) => ({
        produto_id: item.id,
        quantidade: item.quantidade,
    }));

    form.post(route('requisicoes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('justificativa', 'itens');
            Object.keys(carrinho).forEach((key) => {
                delete carrinho[key];
            });
        },
    });
};
</script>

<template>
    <Head title="Nova Requisição" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Nova Requisição</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Monte sua solicitação em formato de carrinho e confirme com segurança.
                </p>
            </div>
        </template>

        <div class="space-y-6">
            <div
                v-if="successMessage"
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
            >
                {{ successMessage }}
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <section class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Produtos Disponíveis</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Produto</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Categoria</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Estoque</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Quantidade</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="produto in produtos" :key="produto.id">
                                    <td class="px-4 py-3 text-sm text-slate-900">
                                        <div class="font-medium">{{ produto.nome }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ produto.categoria }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ produto.estoque_atual }} {{ produto.unidade_medida }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="inline-flex items-center gap-2">
                                            <button
                                                type="button"
                                                class="rounded-md border border-slate-300 px-2 py-1 text-sm text-slate-700 transition hover:bg-slate-50"
                                                @click="removerUm(produto)"
                                            >
                                                -
                                            </button>
                                            <input
                                                :value="carrinho[produto.id] ?? 0"
                                                type="number"
                                                min="0"
                                                :max="produto.estoque_atual"
                                                class="w-20 rounded-md border border-slate-300 px-2 py-1 text-sm text-slate-900 focus:border-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-800/20"
                                                @input="definirQuantidade(produto, $event.target.value)"
                                            />
                                            <button
                                                type="button"
                                                class="rounded-md border border-slate-300 px-2 py-1 text-sm text-slate-700 transition hover:bg-slate-50"
                                                @click="adicionarUm(produto)"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="produtos.length === 0">
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                                        Nenhum produto disponível para requisição.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Resumo da Requisição</h3>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-xs text-slate-500">Itens no carrinho</p>
                            <p class="text-lg font-semibold text-slate-900">{{ totalItens }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-xs text-slate-500">Produtos distintos</p>
                            <p class="text-lg font-semibold text-slate-900">{{ totalProdutos }}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Itens selecionados</p>
                        <ul class="mt-2 max-h-48 space-y-2 overflow-y-auto pr-1">
                            <li
                                v-for="item in itensSelecionados"
                                :key="item.id"
                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                            >
                                <p class="font-medium text-slate-900">{{ item.nome }}</p>
                                <p class="text-slate-600">{{ item.quantidade }} {{ item.unidade_medida }}</p>
                            </li>
                            <li v-if="itensSelecionados.length === 0" class="text-sm text-slate-500">
                                Nenhum item adicionado.
                            </li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <label for="justificativa" class="text-xs font-semibold uppercase tracking-wide text-slate-600">
                            Justificativa (opcional)
                        </label>
                        <textarea
                            id="justificativa"
                            v-model="form.justificativa"
                            rows="4"
                            class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-800/20"
                            placeholder="Descreva o contexto da solicitação"
                        />
                        <InputError class="mt-2" :message="form.errors.justificativa" />
                        <InputError class="mt-2" :message="form.errors.itens" />
                    </div>

                    <button
                        type="button"
                        class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-sky-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-950 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="form.processing || itensSelecionados.length === 0"
                        @click="confirmarRequisicao"
                    >
                        {{ form.processing ? 'Confirmando...' : 'Confirmar Requisição' }}
                    </button>
                </aside>
            </div>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Minhas Últimas Requisições</h3>
                    <span class="text-xs text-slate-500">{{ minhasRequisicoes.length }} registro(s)</span>
                </div>

                <div class="mt-4 space-y-3">
                    <article
                        v-for="requisicao in minhasRequisicoes"
                        :key="requisicao.id"
                        class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-900">#{{ requisicao.id }}</p>
                            <span
                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="statusClass(requisicao.status)"
                            >
                                {{ statusLabel(requisicao.status) }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ requisicao.created_at }}</p>
                        <p v-if="requisicao.justificativa" class="mt-2 text-sm text-slate-700">
                            {{ requisicao.justificativa }}
                        </p>
                        <ul class="mt-2 space-y-1 text-sm text-slate-600">
                            <li v-for="item in requisicao.itens" :key="`${requisicao.id}-${item.produto}`">
                                {{ item.quantidade_pedida }} {{ item.unidade_medida }} - {{ item.produto }}
                            </li>
                        </ul>
                    </article>

                    <p v-if="minhasRequisicoes.length === 0" class="text-sm text-slate-500">
                        Você ainda não possui requisições registradas.
                    </p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
