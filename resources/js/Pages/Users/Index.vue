<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    users: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const successMessage = computed(() => page.props.flash?.success ?? null);
</script>

<template>
    <Head title="Usuários" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Gestão de Usuários</h2>
                    <p class="mt-1 text-sm text-slate-500">Lista de usuários cadastrados no sistema.</p>
                </div>

                <Link
                    :href="route('users.create')"
                    class="rounded-lg bg-cyan-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-800"
                >
                    Novo Usuário
                </Link>
            </div>
        </template>

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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Nível de Acesso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <tr v-for="item in users.data" :key="item.id">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ item.name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ item.email }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            {{ item.roles.length ? item.roles.join(', ') : 'Nenhum nível atribuído' }}
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-2">
            <Link
                v-for="link in users.links"
                :key="link.label"
                :href="link.url || '#'
                "
                class="rounded-md border px-3 py-1.5 text-sm"
                :class="[
                    link.active ? 'border-cyan-700 bg-cyan-700 text-white' : 'border-slate-300 text-slate-700',
                    !link.url ? 'pointer-events-none opacity-50' : 'hover:bg-slate-50',
                ]"
                v-html="link.label"
            />
        </div>

    </AuthenticatedLayout>
</template>
