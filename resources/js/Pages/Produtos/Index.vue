<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    produtos: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);
const userPerfil = computed(() => page.props.auth?.user?.perfil ?? null);
const canManageProducts = computed(() => ['Administrador', 'Almoxarife'].includes(userPerfil.value));
const produtosCreateUrl = route('produtos.create');

const isLowStock = (produto) => Number(produto.estoque_atual) <= Number(produto.estoque_minimo);

const deleteProduto = (produtoId) => {
    if (!window.confirm('Deseja realmente excluir este produto?')) {
        return;
    }

    router.delete(route('produtos.destroy', produtoId));
};
</script>

<template>
    <Head title="Produtos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Produtos</h2>
                    <p class="mt-1 text-sm text-slate-500">Controle de estoque com acompanhamento de limite mínimo.</p>
                </div>

                <Link
                    v-if="canManageProducts"
                    :href="produtosCreateUrl"
                    class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800"
                >
                    Novo Produto
                </Link>
            </div>
        </template>

        <div
            class="animate-fade-in rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            style="animation: fade-in 0.5s ease-out, slide-in-from-bottom 0.5s ease-out"
        >
            <div
                v-if="successMessage"
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
            >
                {{ successMessage }}
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Categoria</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Estoque Atual</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Alerta</th>
                            <th v-if="canManageProducts" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-600">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr v-for="produto in produtos.data" :key="produto.id">
                            <td class="px-4 py-3 text-sm text-slate-900">{{ produto.nome }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ produto.categoria?.nome || '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                {{ produto.estoque_atual }} {{ produto.unidade_medida }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="isLowStock(produto)
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-emerald-100 text-emerald-700'"
                                >
                                    {{ isLowStock(produto) ? 'Estoque baixo' : 'Normal' }}
                                </span>
                            </td>
                            <td v-if="canManageProducts" class="px-4 py-3 text-right text-sm text-slate-700">
                                <div class="inline-flex items-center gap-3">
                                    <Link :href="route('produtos.edit', produto.id)" class="text-emerald-700 hover:underline">Editar</Link>
                                    <button type="button" class="text-red-600 hover:underline" @click="deleteProduto(produto.id)">
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="produtos.data.length === 0">
                            <td :colspan="canManageProducts ? 5 : 4" class="px-4 py-8 text-center text-sm text-slate-500">
                                Nenhum produto encontrado.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-2">
                <Link
                    v-for="link in produtos.links"
                    :key="`${link.label}-${link.url}`"
                    :href="link.url || '#'
                    "
                    class="rounded-md border px-3 py-1.5 text-sm"
                    :class="[
                        link.active ? 'border-emerald-700 bg-emerald-700 text-white' : 'border-slate-300 text-slate-700',
                        !link.url ? 'pointer-events-none opacity-50' : 'hover:bg-slate-50',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
