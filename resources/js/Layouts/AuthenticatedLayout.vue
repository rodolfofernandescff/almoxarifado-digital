<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const userPerfil = computed(() => user.value?.perfil ?? null);
const isAdministradorExact = computed(() => userPerfil.value === 'Administrador');
const canManageProdutos = computed(() => ['Administrador', 'Almoxarife'].includes(userPerfil.value));

const normalizePerfil = (perfil) => {
    if (!perfil) return null;

    const value = String(perfil).trim().toLowerCase();

    if (value === 'administrador' || value === 'admin') return 'administrador';
    if (value === 'almoxarife') return 'almoxarife';
    if (value === 'requisitante') return 'requisitante';

    return null;
};

/**
 * Constante que define todos os links de navegação possíveis.
 * Cada item possui:
 * - label: nome do link (exibido para o usuário)
 * - route: nome da rota Inertia
 * - allowedRoles: array de roles que têm acesso a este link
 */
const navLinks = [
    {
        label: 'Dashboard',
        route: 'dashboard',
        allowedPerfis: ['administrador', 'almoxarife', 'requisitante'],
    },
    {
        label: 'Pedidos',
        route: 'requisicoes.index',
        allowedPerfis: ['almoxarife'],
    },
    {
        label: 'Meus Pedidos',
        route: 'requisicoes.meus',
        allowedPerfis: ['requisitante'],
    },
];

/**
 * Computed property que filtra navLinks baseado nas roles do usuário autenticado.
 * Implementa Clean Code: sem lógica complexa no template.
 */
const filteredNavLinks = computed(() => {
    const perfil = normalizePerfil(userPerfil.value);

    // Evita menu vazio quando o perfil ainda nao foi carregado no bootstrap.
    if (!perfil) {
        return navLinks
            .filter((link) => link.label === 'Dashboard')
            .map((link) => ({ ...link, resolvedRoute: link.route }));
    }

    return navLinks
        .filter((link) => link.allowedPerfis.includes(perfil))
        .map((link) => ({
            ...link,
            resolvedRoute: link.routeByPerfil?.[perfil] ?? link.route,
        }));
});

const tabsMenu = computed(() => {
    // Mostrar todas as abas que o usuário tem permissão (baseado em filteredNavLinks)
    // Excluir apenas Dashboard que não é uma aba, mas um link de navegação
    const excludeTabs = ['Dashboard', 'Pedidos', 'Meus Pedidos']; // Pedidos e Meus Pedidos não são abas visíveis
    
    return filteredNavLinks.value.filter((link) => !excludeTabs.includes(link.label));
});

/**
 * Verifica se uma rota está ativa no contexto atual.
 */
const isRouteActive = (routeName) => route().current(routeName);
const produtosCreateUrl = route('produtos.create', undefined, false);
const produtosIndexUrl = route('produtos.index', undefined, false);

const handleLogout = () => {
    router.post(route('logout'), {}, {
        onSuccess: () => {
            window.location.href = '/login';
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50">
        <Disclosure v-slot="{ open }" as="header" class="fixed inset-x-0 top-0 z-40 bg-white/98 backdrop-blur-md border-b border-slate-200/50 shadow-sm">
            <div>
                <div class="relative mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-6">
                        <Link :href="route('dashboard')" class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-emerald-600 to-emerald-600 bg-clip-text text-transparent hover:from-emerald-700 hover:to-emerald-700 transition-all duration-300">
                            FernandesLab
                        </Link>
                    </div>

                    <div class="pointer-events-none absolute left-1/2 hidden -translate-x-1/2 text-lg font-bold text-emerald-700 md:block">
                        Almoxarifado Digital
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-slate-700">{{ user?.name ?? 'Usuário' }}</span>
                        <button
                            @click="handleLogout"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700 active:bg-red-800"
                        >
                            Sair
                        </button>
                    </div>
                </div>
            </div>

            <div class="h-1 bg-emerald-900"></div>

            <div class="border-b border-slate-200/50 bg-white/80">
                <div class="mx-auto hidden h-12 max-w-7xl items-center px-4 sm:px-6 md:flex lg:px-8">
                    <nav class="flex items-center gap-2">
                        <NavLink
                            v-for="tab in tabsMenu"
                            :key="tab.resolvedRoute"
                            :href="route(tab.resolvedRoute)"
                            :active="isRouteActive(tab.resolvedRoute)"
                        >
                            {{ tab.label }}
                        </NavLink>

                        <Dropdown v-if="canManageProdutos" align="left" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-gray-700 focus:outline-none"
                                >
                                    Produtos
                                    <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="produtosCreateUrl">
                                    Cadastrar
                                </DropdownLink>
                                <DropdownLink :href="produtosIndexUrl">
                                    Consultar
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </nav>
                </div>
            </div>

            <DisclosurePanel class="border-b border-slate-200/50 bg-white/90 md:hidden">
                <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6">
                    <nav class="space-y-1">
                        <NavLink
                            v-for="tab in tabsMenu"
                            :key="tab.resolvedRoute"
                            :href="route(tab.resolvedRoute)"
                            :active="isRouteActive(tab.resolvedRoute)"
                        >
                            {{ tab.label }}
                        </NavLink>

                        <div v-if="isAdministradorExact" class="space-y-1 pt-2">
                            <div class="px-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Usuários</div>
                            <DropdownLink :href="route('admin.users.create')">Cadastrar</DropdownLink>
                            <DropdownLink :href="route('admin.users.index')">Consultar</DropdownLink>
                        </div>

                        <div v-if="canManageProdutos" class="space-y-1 pt-2">
                            <div class="px-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Produtos</div>
                            <DropdownLink :href="produtosCreateUrl">Cadastrar</DropdownLink>
                            <DropdownLink :href="produtosIndexUrl">Consultar</DropdownLink>
                        </div>
                    </nav>
                </div>
            </DisclosurePanel>
        </Disclosure>

        <main class="mx-auto max-w-7xl px-4 pb-8 pt-32 sm:px-6 lg:px-8">
            <header v-if="$slots.header" class="mb-4">
                <slot name="header" />
            </header>

            <slot />
        </main>
    </div>
</template>
