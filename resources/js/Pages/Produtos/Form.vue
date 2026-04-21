<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    form: {
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
    submitLabel: {
        type: String,
        default: 'Salvar',
    },
    cancelRoute: {
        type: String,
        default: 'produtos.index',
    },
});

const emit = defineEmits(['submit']);
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <InputLabel for="nome" value="Nome" />
                <TextInput
                    id="nome"
                    v-model="form.nome"
                    type="text"
                    class="mt-2 block w-full"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.nome" />
            </div>

            <div>
                <InputLabel for="categoria_id" value="Categoria" />
                <select
                    id="categoria_id"
                    v-model="form.categoria_id"
                    class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/30"
                    required
                >
                    <option value="" disabled>Selecione a categoria</option>
                    <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                        {{ categoria.nome }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.categoria_id" />
            </div>

            <div>
                <InputLabel for="unidade_medida" value="Unidade de Medida" />
                <select
                    id="unidade_medida"
                    v-model="form.unidade_medida"
                    class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/30"
                    required
                >
                    <option value="" disabled>Selecione a unidade</option>
                    <option v-for="unidade in unidadesMedida" :key="unidade" :value="unidade">
                        {{ unidade }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.unidade_medida" />
            </div>

            <div>
                <InputLabel for="estoque_atual" value="Estoque Atual" />
                <TextInput
                    id="estoque_atual"
                    v-model="form.estoque_atual"
                    type="number"
                    min="0"
                    class="mt-2 block w-full"
                    required
                />
                <InputError class="mt-2" :message="form.errors.estoque_atual" />
            </div>

            <div>
                <InputLabel for="estoque_minimo" value="Estoque Minimo" />
                <TextInput
                    id="estoque_minimo"
                    v-model="form.estoque_minimo"
                    type="number"
                    min="0"
                    class="mt-2 block w-full"
                    required
                />
                <InputError class="mt-2" :message="form.errors.estoque_minimo" />
            </div>

            <div class="md:col-span-2">
                <InputLabel for="descricao" value="Descricao" />
                <textarea
                    id="descricao"
                    v-model="form.descricao"
                    rows="4"
                    class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition-all duration-300 ease-out focus:border-emerald-500 focus:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    placeholder="Informacoes complementares do produto"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.descricao" />
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <Link
                :href="route(cancelRoute)"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                Cancelar
            </Link>
            <PrimaryButton :disabled="form.processing" :class="{ 'opacity-60': form.processing }">
                {{ form.processing ? 'Processando...' : submitLabel }}
            </PrimaryButton>
        </div>
    </form>
</template>
