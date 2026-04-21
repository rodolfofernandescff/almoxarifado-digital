<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

const submit = () => {
    form.post(route('users.store'), {
        preserveScroll: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Novo Usuário" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Cadastrar Usuário</h2>
                    <p class="mt-1 text-sm text-slate-500">Preencha os dados para criar um novo usuário no sistema.</p>
                </div>
                <Link
                    :href="route('users.index')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Voltar
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="mx-auto max-w-3xl space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <InputLabel for="name" value="Nome" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-2 block w-full rounded-lg border-slate-300 focus:border-cyan-600 focus:ring-cyan-600"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="md:col-span-2">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-2 block w-full rounded-lg border-slate-300 focus:border-cyan-600 focus:ring-cyan-600"
                        v-model="form.email"
                        required
                        autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Senha" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-2 block w-full rounded-lg border-slate-300 focus:border-cyan-600 focus:ring-cyan-600"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirmação de Senha" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-2 block w-full rounded-lg border-slate-300 focus:border-cyan-600 focus:ring-cyan-600"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="md:col-span-2">
                    <InputLabel for="role" value="Nível de Acesso" />
                    <select
                        id="role"
                        v-model="form.role"
                        class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/30"
                        required
                    >
                        <option value="" disabled>Selecione um nível de acesso</option>
                        <option v-for="role in props.roles" :key="role.id" :value="role.name">
                            {{ role.display_name }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <Link
                    :href="route('users.index')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Cancelar
                </Link>
                <PrimaryButton :disabled="form.processing" :class="{ 'opacity-60': form.processing }">
                    {{ form.processing ? 'Salvando...' : 'Salvar' }}
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
