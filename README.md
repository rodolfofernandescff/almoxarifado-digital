# Almoxarifado Digital - Enterprise Architecture

[![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-React%20Vue-6B7280?style=flat-square&logo=javascript)](https://inertiajs.com)
[![Vue 3](https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-06B6D4?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php)](https://www.php.net)

---

## 📋 Visão Geral

Almoxarifado Digital é uma aplicação **enterprise-grade** de gestão de inventário e requisições, construída com as mais modernas tecnologias do ecossistema web. Implementa padrões **SOLID**, **Clean Code** e **RBAC (Role-Based Access Control)** para garantir escalabilidade, manutenibilidade e segurança.

### 🎯 Objetivos Principais

- **RBAC Robusto**: Sistema de papel e permissão granulares baseado em Spatie Permission
- **Perfis Estritos**: O campo `perfil` usa valores fixos (`Administrador`, `Almoxarife`, `Requisitante`) para controle explícito de acesso
- **Gestão de Acessos Centralizada**: Apenas o perfil `Administrador` pode acessar o CRUD de usuários e administrar permissões operacionais
- **Regra de Estoque Mínimo**: Produtos com estoque atual menor ou igual ao estoque mínimo recebem alerta visual para prevenir ruptura de estoque
- **Separação de Responsabilidades**: Arquitetura em camadas com Controllers, Services e Repositories
- **Interface Reativa**: Vue 3 com Composition API para UX interativa
- **Performance**: Lazy loading, code splitting e otimizações de cache
- **Segurança**: Autenticação via email/senha e preparação para OAuth2 (Google)

---

## 🏗️ Stack Técnica

### Backend (Core)

| Tecnologia | Versão | Propósito |
|-----------|--------|----------|
| **Laravel** | 12 | Framework PHP | Web framework principal com Eloquent ORM |
| **PHP** | 8.3+ | Runtime | Engine de execução |
| **Spatie Permission** | ^6.0 | RBAC | Sistema de roles e permissões |
| **PHPUnit** | ^11.0 | Testing | Testes unitários e de feature |

### Bridge (Inertia.js)

| Tecnologia | Versão | Propósito |
|-----------|--------|----------|
| **Inertia.js** | ^1.0 | SPA Bridge | Comunica Laravel ↔ Frontend sem API REST |

### Frontend (UI)

| Tecnologia | Versão | Propósito |
|-----------|--------|----------|
| **Vue 3** | ^3.4 | Framework JS | Componentes reativos com Composition API |
| **Tailwind CSS** | ^3.4 | Styling | Utility-first CSS framework |
| **TypeScript** | ^5.0 | Type Safety | Tipagem estática para JavaScript |

### Database

| Tecnologia | Propósito |
|-----------|----------|
| **MySQL** / **SQLite** | Persistência de dados |
| **Migrations** | Versionamento de schema |

---

## 🏛️ Arquitetura do Sistema

### RBAC (Role-Based Access Control) Simplificado

O sistema implementa um modelo de **controle de acesso baseado em papéis** que segrega responsabilidades de forma clara e escalável:

#### Fluxo de Autorização

```
1. Usuário faz login
   ↓
2. Papéis (roles) são carregados em page.props.auth.user.roles
   ↓
3. Frontend filtra menu de navegação com computed property `filteredNavLinks`
   ↓
4. Backend valida acesso via middleware `role:admin|almoxarife|requisitante`
   ↓
5. Ação autorizada é executada no controller
```

#### Papéis do Sistema

| Papel | Acesso | Responsabilidade |
|-------|--------|-----------------|
| **Administrador** | Dashboard, Usuários, Produtos | Gestão geral do sistema |
| **Almoxarife** | Dashboard, Produtos, Entregas | Gestão de inventário e requisições |
| **Requisitante** | Dashboard, Meus Pedidos | Criar e acompanhar requisições |

### Padrões de Projeto (SOLID + Clean Code)

#### Frontend: Single Responsibility Principle em Componentes Vue

**AuthenticatedLayout.vue** demonstra SRP através de separação clara:

```javascript
// ✅ Fonte unica de verdade para links
const navLinks = [
    { label: 'Dashboard', route: 'dashboard', allowedPerfis: [...] },
    { label: 'Usuários', route: 'admin.users.index', allowedPerfis: ['administrador'] },
    { label: 'Produtos', routeByPerfil: {...}, allowedPerfis: [...] },
    // ...
];

// ✅ Computed property que filtra por perfil
const filteredNavLinks = computed(() => {
    const perfil = normalizePerfil(userPerfil.value);
    return navLinks.filter(link => link.allowedPerfis.includes(perfil));
});

// ✅ Menu secundario abaixo do header com abas institucionais
const tabsMenu = computed(() => {
    const allowedTabs = ['Usuários', 'Produtos'];
    return filteredNavLinks.value.filter((link) => allowedTabs.includes(link.label));
});
```

**Benefícios desta abordagem**:
- ✅ **Lógica fora do template**: computações em Script Setup, template apenas renderiza
- ✅ **Header limpo**: primeira linha com marca/projeto/usuário, segunda linha para navegação
- ✅ **Manutenibilidade**: adicionar aba nova exige apenas ajuste na fonte de dados
- ✅ **Consistência visual**: dashboard simplificada com saudação minimalista

### Diretriz de Interface (Institucional)

- Header em duas camadas com divisor horizontal
- Navegação principal posicionada abaixo do header, alinhada à esquerda
- Abas institucionais visíveis: Usuários e Produtos (conforme permissões)
- Remoção de cards de placeholder na dashboard para reduzir ruído visual
- Alerta de estoque em vermelho para itens em nível crítico (`estoque_atual <= estoque_minimo`)

#### Backend: Segregation of Concerns (Routes)

```php
// ✅ Rotas segregadas por responsabilidade
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);      // Admin
});

Route::middleware(['auth', 'role:almoxarife'])->group(function () {
    Route::get('requisicoes', RequisicaoController::class); // Almoxarife
});

Route::middleware(['auth', 'role:requisitante'])->group(function () {
    Route::get('meus-pedidos', RequisicaoController::class); // Requisitante
});
```

### Stack Técnica Integrada

| Layer | Tecnologia | Responsabilidade |
|-------|-----------|------------------|
| **Backend** | Laravel 12 | Lógica de negócio, autenticação, RBAC |
| **Bridge** | Inertia.js | Comunica dados entre Laravel e Vue |
| **Frontend** | Vue 3 + Composition API | Renderização reativa e UX interativa |
| **Styling** | Tailwind CSS | Design system com utility classes |

### UI Responsiva Moderna (Vue)

- Navegação em duas camadas: barra institucional + barra de abas
- Menu mobile acessível com Disclosure (Headless UI)
- Ícones semânticos com Heroicons para reforço visual
- Estados ativos com destaque suave e foco em legibilidade

Bibliotecas utilizadas na interface:

- `@headlessui/vue`: componentes acessíveis e sem acoplamento visual
- `@heroicons/vue`: iconografia moderna para feedback contextual

Boas práticas aplicadas:

- SRP no frontend: lógica de navegação no Script Setup e template focado em renderização
- Fonte única de verdade para links e permissões (`navLinks`)
- Progressive enhancement: desktop com abas horizontais e mobile com painel expansível
- Consistência institucional: manutenção da paleta cinza/azul petróleo e marca FernandesLab

---

## 🏛️ Padrões de Projeto (SOLID + Clean Code)

### SOLID Principles

#### **S** - Single Responsibility Principle
- **Controllers** focados: cada controller gerencia uma entidade
- Exemplo: `UserController` → apenas lógica de usuários
- **Separação em Services**: `UserService` gerencia regras de negócio de usuários
- **Componentes Vue**: cada componente tem uma responsabilidade clara

```php
// ✅ SRP aplicado
class UserController extends Controller {
    public function __construct(private UserService $userService) {}
    
    public function store(CreateUserRequest $request) {
        $user = $this->userService->create($request->validated());
        return redirect()->route('users.show', $user);
    }
}
```

#### **O** - Open/Closed Principle
- Componentes abertos para extensão, fechados para modificação
- Middleware e middleware stack reutilizáveis
- Roles e Permissions extensíveis sem alterar código core

#### **L** - Liskov Substitution Principle
- Herança respeitada em Controllers base
- Interfaces de Repository públicas

#### **I** - Interface Segregation Principle
- Rotas segregadas por middleware (`admin`, `user`, `requisitante`)
- Grupos de rotas específicos reduzem acoplamento

```php
// ✅ Interface Segregation aplicada
Route::middleware('role:admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('products', ProdutoController::class);
});

Route::middleware('role:almoxarife')->group(function () {
    Route::get('deliveries', [DeliveryController::class, 'index']);
});
```

#### **D** - Dependency Inversion Principle
- Services injetados via Constructor Injection
- Repository Pattern abstrai acesso a dados

### Clean Code Practices

#### Nomes Semânticos
```javascript
// ✅ Vue 3 Script Setup - nomes claros
const navigationMenu = computed(() => {
    // Lógica legível
});

const userRoles = computed(() => page.props.auth?.user?.roles ?? []);
```

#### Funções Pequenas (Early Returns)
```php
// ✅ Early returns, sem arrow code
if ($user->is_active === false) {
    return response()->unauthorized();
}

if (!$user->hasPermission('create_products')) {
    return response()->forbidden();
}

// Lógica principal aqui
```

#### Componentes Reutilizáveis
- `PrimaryButton.vue` - botão primário
- `TextInput.vue` - input de texto
- `InputLabel.vue` - labels acessíveis
- `Dropdown.vue` - dropdowns reutilizáveis

---

## 🏃 Instalação & Setup

### Pré-requisitos

- **PHP 8.3+** com extensions: `php-mysql`, `php-curl`, `php-mbstring`
- **Node.js 18+** e **npm** ou **yarn**
- **MySQL 8.0+** ou **SQLite**
- **Composer 2.x**

### Passo a Passo

#### 1️⃣ Clonar Repositório
```bash
git clone <seu-repositorio>
cd almoxarifado_digital
```

#### 2️⃣ Instalar Dependências PHP
```bash
composer install
```

#### 3️⃣ Configurar Arquivo `.env`
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` com suas credenciais:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=almoxarifado_digital
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

#### 4️⃣ Migrar Database
```bash
php artisan migrate
```

#### 5️⃣ Seedar Dados (Opcional)
```bash
php artisan db:seed
```

#### 6️⃣ Instalar Dependências Node.js
```bash
npm install
```

#### 7️⃣ Compilar Assets
```bash
npm run dev    # Desenvolvimento com HMR
npm run build  # Produção otimizado
```

#### 8️⃣ (Opcional) Executar Servidor Local
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

---

## 🗂️ Estrutura de Arquitetura RBAC

### Modelos de Dados

```
┌─────────────┐
│   users     │ (usuários do sistema)
└──────┬──────┘
       │
       ├─── roles (via Spatie) ─────┐
       │   └── admin               │
       │   └── almoxarife          │
       │   └── requisitante        │
       │
       └─── permissions ────────────┐
           └── users.manage
           └── products.view
           └── products.manage
           └── requisicoes.create
```

### Fluxo de Acesso

```
1. Usuário faz login (email + senha)
   ↓
2. Middleware 'auth' valida sessão
   ↓
3. Middleware 'role:admin' verifica permissão
   ↓
4. navigationMenu (Vue) renderiza links baseado em $page.props.auth.user.roles
   ↓
5. Controller executa a ação autorizada
```

### Roles Disponíveis

| Role | Dashboard | Usuários | Produtos | Requisições |
|------|:---------:|:--------:|:--------:|:-----------:|
| **admin** | ✅ | ✅ | ✅ | ✅ |
| **almoxarife** | ✅ | ❌ | ✅ | ✅ |
| **requisitante** | ✅ | ❌ | ❌ | ✅ |

---

## 📂 Estrutura de Rotas

### Rotas Públicas
```
GET  /              → Welcome page
GET  /login         → Login form
POST /login         → Authenticate
GET  /register      → Register form
POST /register      → Create user
```

### Rotas Autenticadas (Todos)
```
GET  /dashboard     → Dashboard principal
GET  /profile       → Editar perfil
```

### Rotas Admin (`role:admin`)
```
GET    /admin/users          → Listar usuários
GET    /admin/users/create   → Formulário criar usuário
POST   /admin/users          → Storear usuário
GET    /admin/products       → Listar produtos
```

### Rotas Almoxarife (`role:almoxarife`)
```
GET  /deliveries   → Gerenciar entregas
GET  /products     → Visualizar produtos
```

### Rotas Requisitante (`role:requisitante`)
```
GET  /meus-pedidos  → Meus pedidos (requisições)
```

---

## 🧪 Testes

### Executar Testes
```bash
php artisan test
php artisan test --filter=UserControllerTest
```

### Cobertura
```bash
php artisan test --coverage
```

---

## 🔐 Segurança

- ✅ Autenticação via Laravel Auth (email verificado)
- ✅ RBAC com verificação de Roles/Permissions em Middleware
- ✅ CSRF Protection em formulários
- ✅ Password hashing com Bcrypt
- ✅ Soft deletes para usuários
- ✅ Rate limiting em rotas de autenticação

---

## 📚 Documentação Adicional

### Controllers
- `ProfileController` - Gerenciamento de perfil do usuário
- `UserController` - CRUD de usuários (admin)
- `ProdutoController` - Gestão de produtos
- `RequisicaoController` - Gestão de requisições

### Componentes Vue
- `AuthenticatedLayout.vue` - Layout principal com RBAC navigation
- `Dashboard.vue` - Dashboard baseado em role
- `Button`, `Input`, `Dropdown` - Componentes reutilizáveis

### Migrations
- `users_table` - Schema de usuários
- `roles_permissions_tables` - RBAC (Spatie)
- `produtos_table` - Produtos do almoxarifado
- `requisicoes_table` - Requisições do sistema

---

## 🚀 Próximas Melhorias

- [ ] OAuth2 com Google (completo)
- [ ] API REST com Passport
- [ ] Notificações em real-time (Laravel Echo + WebSockets)
- [ ] Dashboard analytics avançados
- [ ] Multi-tenancy
- [ ] Audit trail completo

---

## 📝 Licença

Propriedade privada. Uso restrito ao projeto Almoxarifado Digital.

---

## 👥 Contato

Para dúvidas ou sugestões, abra uma issue no repositório.

**Versão**: 1.0.0 | **Última atualização**: Abril 2026

No middleware `HandleInertiaRequests`, foram compartilhados:

- `auth.user` (id, name, email)
- `auth.roles`
- `auth.permissions`

Com isso, o layout Vue renderiza menus de forma condicional conforme nível de acesso.

## Passo a Passo de Instalação

1. Clonar projeto e acessar a pasta

```bash
cd core-base
```

2. Instalar dependências PHP

```bash
composer install
```

3. Copiar ambiente e gerar chave

```bash
cp .env.example .env
php artisan key:generate
```

No Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

4. Configurar banco no `.env`

Defina `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

5. Rodar migrations

```bash
php artisan migrate
```

6. Instalar dependências frontend

```bash
npm install
```

7. Subir ambiente local

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

## Comandos úteis

- Rodar testes:

```bash
php artisan test
```

- Limpar caches:

```bash
php artisan optimize:clear
```

- Build de produção frontend:

```bash
npm run build
```

## Fluxo recomendado para RBAC

1. Criar permissões base
2. Criar roles por domínio de negócio
3. Associar permissões às roles
4. Associar roles aos usuários
5. Proteger rotas com middleware `role`
6. Renderizar navegação no frontend com base em `auth.roles` e `auth.permissions`

## Observações finais

- O login padrão usa e-mail + senha com proteção por rate limit.
- A UI autenticada possui sidebar responsiva e itens condicionais por acesso.
- A base está preparada para evolução futura de Social Login (Google) sem ruptura do login local.
