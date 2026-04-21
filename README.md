# Almoxarifado Digital - Enterprise System

Sistema para gestao de estoque e requisicoes, com controle de acesso baseado em perfis (RBAC).

## Visao Geral

O Almoxarifado Digital e uma aplicacao corporativa para operacao de almoxarifado com foco em seguranca, rastreabilidade e organizacao de fluxo. A plataforma centraliza cadastro de usuarios, produtos e rotinas de requisicao com regras de acesso por perfil.

## Destaques de Engenharia

- Aplicacao explicita de **SOLID Principles** na organizacao de responsabilidades entre camadas.
- Praticas de **Clean Code** para legibilidade, manutencao e evolucao segura.
- Separacao clara de responsabilidades com **Laravel 12** no backend e **Inertia.js + Vue 3** no frontend.
- Modelo de autorizacao **RBAC** para garantir isolamento de permissoes por tipo de usuario.

## Funcionalidades

- Login institucional clean.
- Gerenciamento de Usuarios (CRUD).
- Controle de acesso por niveis de permissao:
- `Administrador`
- `Almoxarife`
- `Requisitante`

## Stack Tecnica

- `PHP 8.2+`
- `Laravel 12`
- `Vue 3 (Composition API)`
- `Inertia.js`
- `Vite`
- `Tailwind CSS`
- `MySQL`

## Instalacao

### 1) Clonar o repositorio

```bash
git clone <URL_DO_REPOSITORIO>
cd almoxarifado_digital
```

### 2) Instalar dependencias PHP

```bash
composer install
```

### 3) Instalar dependencias frontend

```bash
npm install
```

### 4) Configurar o arquivo `.env`

```bash
cp .env.example .env
```

No Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5) Gerar a chave da aplicacao

```bash
php artisan key:generate
```

### 6) Criar estrutura do banco e popular dados iniciais

```bash
php artisan migrate --seed
```

### 7) Subir frontend em desenvolvimento

```bash
npm run dev
```

## Licenca

Uso interno do projeto Almoxarifado Digital.
