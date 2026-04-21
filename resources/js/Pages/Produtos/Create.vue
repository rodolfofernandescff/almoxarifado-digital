<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Form from '@/Pages/Produtos/Form.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
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
    categoria_id: '',
    nome: '',
    descricao: '',
    estoque_atual: 0,
    estoque_minimo: 0,
    unidade_medida: '',
});

const submit = () => {
    form.post(route('produtos.store'));
};
</script>

<template>
    <Head title="Cadastrar Produto" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Cadastrar Produto</h2>
                <p class="mt-1 text-sm text-slate-500">Inclua um novo item no almoxarifado.</p>
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
                submit-label="Cadastrar"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
