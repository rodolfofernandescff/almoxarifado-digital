<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    perfis: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const form = useForm({});

const selecionarPerfil = (id) => {
    form.post(route('trocar-perfil', { id }));
};

const traduzPerfil = (tipo) => {
    const mapa = {
        'Administrador': 'Administrador',
        'Usuario':       'Chefia',
        'Chefe':         'Chefe de Centro',
        'Estagiario':    'Estagiário',
        'DRAF':          'DRAF',
        'Gerente':       'Gerente',
        'Gestor':        'Gestor',
    };
    return mapa[tipo] ?? tipo;
};

const iconesPerfil = (tipo) => {
    const mapa = {
        'Administrador': 'M12 4a4 4 0 100 8 4 4 0 000-8zM6 20a6 6 0 1112 0H6z',
        'Usuario':       'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
        'Chefe':         'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2zM9 22V12h6v10',
        'Estagiario':    'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5',
        'DRAF':          'M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z',
        'Gerente':       'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 7a4 4 0 100 8 4 4 0 000-8zM22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
        'Gestor':        'M11 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-6M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z',
    };
    return mapa[tipo] ?? 'M12 4a4 4 0 100 8 4 4 0 000-8zM6 20a6 6 0 1112 0H6z';
};

const primeiroNome = (nome) => nome ? nome.split(' ')[0] : '';
</script>

<template>
    <Head title="Escolher Perfil — Projeto Laravel" />

    <div class="pg-bg">

        <!-- Header Card -->
        <div class="perfil-container">
            <div class="header-card">
                <div class="logo-box">
                    <svg width="28" height="28" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 4L44 14V34L24 44L4 34V14L24 4Z" fill="rgba(255,255,255,0.15)" stroke="#fff" stroke-width="2"/>
                        <path d="M14 24H34M24 14V34" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1 class="sistema-titulo">PROJETO LARAVEL</h1>
                <h2 class="boas-vindas">
                    Bem-vindo,
                    <span class="nome-destaque">
                        {{ primeiroNome(perfis[0]?.nome) }}!
                    </span>
                </h2>
                <p class="subtitulo">Escolha seu perfil de acesso</p>
            </div>

            <!-- Grid de perfis -->
            <div class="perfis-grid">
                <button
                    v-for="perfil in perfis"
                    :key="perfil.id"
                    class="perfil-card"
                    :disabled="form.processing"
                    :class="{ 'is-loading': form.processing }"
                    @click="selecionarPerfil(perfil.id)"
                    :aria-label="`Entrar como ${traduzPerfil(perfil.tipo)}`"
                >
                    <div class="card-icon-wrap">
                        <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path :d="iconesPerfil(perfil.tipo)" />
                        </svg>
                    </div>

                    <h3 class="perfil-nome">{{ traduzPerfil(perfil.tipo) }}</h3>

                    <span class="btn-acessar">
                        <span v-if="!form.processing">Acessar Sistema</span>
                        <span v-else>Carregando...</span>
                    </span>
                </button>
            </div>
        </div>

        <footer class="pg-footer">
            © {{ new Date().getFullYear() }} Todos os direitos reservados — EPAMIG · ASTI
        </footer>
    </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }

.pg-bg {
    min-height: 100vh;
    background: #f4f6f5;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: clamp(16px, 3vh, 36px) clamp(12px, 3vw, 24px);
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
}

.perfil-container {
    max-width: 1000px;
    width: 100%;
    flex: 1 0 auto;
}

/* Header */
.header-card {
    background: linear-gradient(135deg, #012e1f 0%, #025C3E 60%, #047a55 100%);
    border-radius: 16px;
    padding: clamp(20px, 4vh, 40px) clamp(20px, 4vw, 40px);
    text-align: center;
    margin-bottom: clamp(16px, 2.5vh, 28px);
    box-shadow: 0 8px 32px rgba(2, 92, 62, 0.25);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.logo-box {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    border: 2px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    backdrop-filter: blur(8px);
}

.sistema-titulo {
    font-size: clamp(18px, 2.5vw, 32px);
    font-weight: 800;
    color: #fff;
    margin: 0 0 8px;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.boas-vindas {
    font-size: clamp(15px, 1.8vw, 20px);
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
    margin: 0 0 6px;
}

.nome-destaque {
    color: #7ff5c4;
}

.subtitulo {
    font-size: clamp(13px, 1.4vw, 16px);
    color: rgba(255, 255, 255, 0.65);
    margin: 0;
}

/* Grid */
.perfis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(clamp(160px, 28%, 270px), 1fr));
    gap: clamp(12px, 1.8vw, 24px);
    width: 100%;
    align-items: stretch;
}

/* Card */
.perfil-card {
    background: #fff;
    border: 2px solid transparent;
    border-radius: 14px;
    padding: clamp(20px, 3vw, 32px) clamp(16px, 2vw, 24px);
    text-align: center;
    cursor: pointer;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    outline: none;
}

.perfil-card:hover:not(:disabled) {
    border-color: #025C3E;
    box-shadow: 0 10px 36px rgba(2, 92, 62, 0.14);
    transform: translateY(-5px);
}

.perfil-card:focus-visible {
    outline: 3px solid #025C3E;
    outline-offset: 2px;
}

.perfil-card.is-loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Ícone */
.card-icon-wrap {
    width: clamp(58px, 10vw, 80px);
    height: clamp(58px, 10vw, 80px);
    border-radius: 50%;
    background: linear-gradient(135deg, #025C3E, #04a367);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(2, 92, 62, 0.25);
}

.card-icon {
    width: clamp(24px, 4vw, 34px);
    height: clamp(24px, 4vw, 34px);
    color: #fff;
}

/* Nome do perfil */
.perfil-nome {
    font-size: clamp(15px, 1.8vw, 19px);
    font-weight: 700;
    color: #025C3E;
    margin: 0;
    word-break: break-word;
    line-height: 1.2;
}

/* Botão acessar */
.btn-acessar {
    display: inline-block;
    width: 100%;
    background: #025C3E;
    color: #fff;
    font-weight: 600;
    font-size: clamp(13px, 1.4vw, 15px);
    padding: clamp(10px, 1.2vw, 14px) 16px;
    border-radius: 10px;
    text-align: center;
    transition: background 0.2s ease, transform 0.12s ease;
    margin-top: 4px;
}

.perfil-card:hover:not(:disabled) .btn-acessar {
    background: #034a32;
    transform: translateY(-1px);
}

/* Footer */
.pg-footer {
    margin-top: auto;
    padding: clamp(14px, 2.5vh, 24px) 0 8px;
    font-size: 12px;
    color: #999;
    text-align: center;
}

/* Responsividade */
@media (max-width: 900px) {
    .perfis-grid { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
}
@media (max-width: 520px) {
    .perfis-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 360px) {
    .perfis-grid { grid-template-columns: 1fr; }
}
</style>
