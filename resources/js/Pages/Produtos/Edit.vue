<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Form from '@/Pages/Produtos/Form.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    produto: {
        type: Object,
        required: true,
    },
    categorias: {
        type: Array,
        required: true,
    },
    unidadesMedida: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    categoria_id: props.produto.categoria_id,
    nome: props.produto.nome,
    descricao: props.produto.descricao || '',
    estoque_atual: props.produto.estoque_atual,
    estoque_minimo: props.produto.estoque_minimo,
    unidade_medida: props.produto.unidade_medida,
});

const submit = () => {
    form.put(route('produtos.update', props.produto.id));
};
</script>

<template>
    <Head title="Editar Produto" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Editar Produto</h2>
                <p class="mt-1 text-sm text-slate-500">Atualize os dados do item selecionado.</p>
            </div>
        </template>

        <div
            class="animate-fade-in rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            style="animation: fade-in 0.5s ease-out, slide-in-from-bottom 0.5s ease-out"
        >
            <Form
                :form="form"
                :categorias="categorias"
                :unidades-medida="unidadesMedida"
                submit-label="Salvar Alteracoes"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
