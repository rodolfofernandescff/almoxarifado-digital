<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ClockIcon, UserIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    logs: {
        type: Object,
        required: true,
    }
});

const formatClass = (description) => {
    if (description.includes('created') || description.includes('Criado')) {
        return 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
    }
    if (description.includes('updated') || description.includes('Atualizado')) {
        return 'bg-amber-50 text-amber-700 ring-amber-600/20';
    }
    if (description.includes('deleted') || description.includes('Excluído')) {
        return 'bg-red-50 text-red-700 ring-red-600/20';
    }
    return 'bg-slate-50 text-slate-700 ring-slate-600/20';
};
</script>

<template>
    <Head title="Logs do Sistema" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Trilha de Auditoria
                    </h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Acompanhe todas as ações realizadas no sistema.
                    </p>
                </div>
            </div>
        </template>

        <div class="mt-8 flex flex-col">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-slate-300 md:rounded-lg">
                        <table class="min-w-full divide-y divide-slate-300">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Ação</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Módulo</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Usuário Responsável</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Data/Hora</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="log in logs.data" :key="log.id" class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-6">
                                        <span :class="[formatClass(log.description), 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset']">
                                            {{ log.description }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                        {{ log.subject_type }} <span v-if="log.subject_id">#{{ log.subject_id }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                        <div class="flex items-center">
                                            <UserIcon class="mr-1.5 h-4 w-4 flex-shrink-0 text-slate-400" aria-hidden="true" />
                                            {{ log.causer_name }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                                        <div class="flex items-center">
                                            <ClockIcon class="mr-1.5 h-4 w-4 flex-shrink-0 text-slate-400" aria-hidden="true" />
                                            {{ log.created_at }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="logs.data.length === 0">
                                    <td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">
                                        Nenhum log registrado ainda.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Paginação -->
            <div v-if="logs.data.length > 0" class="mt-4 flex items-center justify-between border-t border-slate-200 bg-white px-4 py-3 sm:px-6 shadow sm:rounded-lg">
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Mostrando de <span class="font-medium">{{ logs.from }}</span> a <span class="font-medium">{{ logs.to }}</span> de <span class="font-medium">{{ logs.total }}</span> resultados
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <Link v-for="(link, i) in logs.links" :key="i" :href="link.url || '#'"
                                :class="[link.active ? 'z-10 bg-emerald-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600' : 'text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-offset-0', 'relative inline-flex items-center px-4 py-2 text-sm font-semibold', !link.url && 'pointer-events-none opacity-50']"
                                v-html="link.label">
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
