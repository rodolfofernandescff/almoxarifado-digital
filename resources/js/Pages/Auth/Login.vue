<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    canResetPassword: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Entrar" />

    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100 p-6">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl" style="animation: fade-in 0.5s ease-out, slide-in-from-bottom 0.5s ease-out">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-500 bg-clip-text text-transparent">Almoxarifado Digital</h1>
                <p class="mt-3 text-sm text-slate-500 leading-relaxed">Use e-mail e senha para autenticar com segurança.</p>
            </div>

            <div v-if="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="email" value="E-mail" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-2 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Senha" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-2 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-sm text-slate-600 transition-colors duration-200 group-hover:text-slate-800">Lembrar de mim</span>
                </label>

                <div class="flex items-center justify-between gap-3 pt-2">
                    <Link
                        v-if="props.canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-emerald-600 font-medium underline-offset-2 transition-all duration-200 hover:text-emerald-700 hover:underline"
                    >
                        Esqueceu a senha?
                    </Link>
                    <PrimaryButton class="ms-auto" :class="{ 'opacity-50 cursor-not-allowed': form.processing }" :disabled="form.processing">
                        <span v-if="form.processing" class="inline-block animate-spin mr-2">⟳</span>
                        Entrar
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
