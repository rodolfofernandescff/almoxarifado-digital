<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref, onMounted } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { 
    CubeIcon, 
    ClipboardDocumentListIcon, 
    ExclamationTriangleIcon,
    CheckCircleIcon,
    ClockIcon,
    ShoppingBagIcon
} from '@heroicons/vue/24/outline';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    }
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Usuário');

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Bom dia';
    if (hour < 18) return 'Boa tarde';
    return 'Boa noite';
});

const today = computed(() => {
    return new Intl.DateTimeFormat('pt-BR', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(new Date());
});

const isGestor = computed(() => props.stats.is_gestor);

// Chart Options
const chartOptions = computed(() => {
    return {
        chart: {
            type: 'bar',
            fontFamily: 'inherit',
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: props.stats.produtos_mais_requisitados?.map(i => i.nome) || [],
        },
        colors: ['#059669'],
    };
});

const chartSeries = computed(() => {
    return [{
        name: 'Quantidade Solicitada',
        data: props.stats.produtos_mais_requisitados?.map(i => i.total) || []
    }];
});

// Animation state
const showContent = ref(false);
onMounted(() => {
    setTimeout(() => { showContent.value = true; }, 100);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-end justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-600">{{ today }}</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        {{ greeting }}, <span class="text-emerald-600">{{ userName }}</span>.
                    </h1>
                </div>
            </div>
        </template>

        <div :class="['transition-all duration-700 ease-in-out transform', showContent ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4']">
            <!-- GESTOR DASHBOARD -->
            <div v-if="isGestor" class="space-y-6">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Total Produtos -->
                    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-emerald-50 p-3">
                                <CubeIcon class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-slate-500">Total de Produtos</p>
                                <p class="text-2xl font-bold text-slate-900">{{ stats.total_produtos }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Requisições Pendentes -->
                    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-amber-50 p-3">
                                <ClipboardDocumentListIcon class="h-6 w-6 text-amber-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-slate-500">Requisições Pendentes</p>
                                <p class="text-2xl font-bold text-slate-900">{{ stats.requisicoes_pendentes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estoque Crítico -->
                    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-red-50 p-3">
                                <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-slate-500">Estoque Crítico</p>
                                <p class="text-2xl font-bold text-red-600">{{ stats.itens_estoque_critico }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Gráfico -->
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4">Top 5 Produtos Requisitados</h3>
                        <div v-if="stats.produtos_mais_requisitados?.length > 0">
                            <VueApexCharts
                                type="bar"
                                height="350"
                                :options="chartOptions"
                                :series="chartSeries"
                            ></VueApexCharts>
                        </div>
                        <div v-else class="flex h-64 items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50">
                            <p class="text-sm text-slate-500">Sem dados suficientes para o gráfico.</p>
                        </div>
                    </div>

                    <!-- Alerta Estoque Crítico -->
                    <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900">Alerta de Estoque Crítico</h3>
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Ação Necessária</span>
                        </div>
                        
                        <div v-if="stats.top_estoque_critico?.length > 0" class="overflow-hidden rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900">Produto</th>
                                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-slate-900">Atual</th>
                                        <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-slate-900">Mínimo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    <tr v-for="produto in stats.top_estoque_critico" :key="produto.id" class="hover:bg-slate-50">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900">
                                            {{ produto.nome }}
                                            <span class="block text-xs text-slate-500">{{ produto.unidade_medida }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-right text-sm text-red-600 font-bold">
                                            {{ produto.estoque_atual }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-right text-sm text-slate-500">
                                            {{ produto.estoque_minimo }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="flex h-64 items-center justify-center rounded-lg border border-dashed border-emerald-200 bg-emerald-50">
                            <div class="text-center">
                                <CheckCircleIcon class="mx-auto h-8 w-8 text-emerald-500" />
                                <p class="mt-2 text-sm font-medium text-emerald-800">Estoque Saudável</p>
                                <p class="text-xs text-emerald-600">Nenhum produto em nível crítico no momento.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REQUISITANTE DASHBOARD -->
            <div v-else class="space-y-6">
                <!-- KPI Cards Requisitante -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <!-- Minhas Pendentes -->
                    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-amber-50 p-3">
                                <ClockIcon class="h-6 w-6 text-amber-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-slate-500">Meus Pedidos Pendentes</p>
                                <p class="text-2xl font-bold text-slate-900">{{ stats.total_pendentes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Minhas Aprovadas -->
                    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition hover:shadow-md">
                        <div class="flex items-center">
                            <div class="rounded-lg bg-emerald-50 p-3">
                                <CheckCircleIcon class="h-6 w-6 text-emerald-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-slate-500">Meus Pedidos Aprovados</p>
                                <p class="text-2xl font-bold text-slate-900">{{ stats.total_aprovadas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Pedidos Recentes -->
                <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-slate-900">Meus Pedidos Recentes</h3>
                        <Link :href="route('requisicoes.meus')" class="text-sm font-medium text-emerald-600 hover:text-emerald-500">Ver todos &rarr;</Link>
                    </div>
                    
                    <div v-if="stats.minhas_requisicoes?.length > 0" class="overflow-hidden rounded-xl border border-slate-200">
                        <ul role="list" class="divide-y divide-slate-200">
                            <li v-for="req in stats.minhas_requisicoes" :key="req.id" class="px-4 py-4 sm:px-6 hover:bg-slate-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <p class="text-sm font-medium text-emerald-600 truncate">Pedido #{{ req.id }}</p>
                                        <p class="mt-1 text-xs text-slate-500 flex items-center gap-1">
                                            <ShoppingBagIcon class="h-3.5 w-3.5" />
                                            {{ req.itens?.length || 0 }} itens
                                        </p>
                                    </div>
                                    <div class="ml-2 flex flex-shrink-0">
                                        <span :class="[
                                            req.status === 'aprovado' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 
                                            req.status === 'recusado' ? 'bg-red-50 text-red-700 ring-red-600/10' : 
                                            'bg-amber-50 text-amber-700 ring-amber-600/20',
                                            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset capitalize'
                                        ]">
                                            {{ req.status }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else class="flex h-32 items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50">
                        <p class="text-sm text-slate-500">Você ainda não fez nenhum pedido.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
