<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        Log::info('Iniciando redirecionamento para Google OAuth', $this->googleConnectionContext());
        
        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $e) {
            Log::error('Erro ao redirecionar para Google.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'google' => $this->googleConnectionContext(),
            ]);
            
            return redirect('/login')->withErrors([
                'google' => $this->buildGoogleConnectionErrorMessage($e),
            ]);
        }
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        Log::info('=== INICIO CALLBACK GOOGLE ===', [
            'request_url' => request()->fullUrl(),
            'query' => request()->query(),
            'google' => $this->googleConnectionContext(),
        ]);
        
        try {
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Dados recebidos do Google:', [
                'id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            
        } catch (Throwable $e) {
            Log::error('Erro no callback do Socialite Google.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'google' => $this->googleConnectionContext(),
            ]);
            
            return redirect('/login')->withErrors([
                'google' => $this->buildGoogleConnectionErrorMessage($e),
            ]);
        }

        $googleEmail = $googleUser->getEmail();
        Log::info('Buscando usuario na tabela users pelo e-mail.', ['email' => $googleEmail]);

        if (! $googleEmail) {
            return redirect('/login')->withErrors([
                'google' => 'Não foi possível recuperar o e-mail da conta Google.',
            ]);
        }
        
        try {
            Log::info('Conexao com banco:', [
                'driver' => config('database.default'),
                'database' => config('database.connections.mysql.database'),
                'host' => config('database.connections.mysql.host'),
            ]);
            
            $user = User::firstOrCreate(
                ['email' => $googleEmail],
                [
                    'name' => $googleUser->getName() ?: 'Usuário Google',
                    'password' => Hash::make(str()->random(32)),
                    'is_active' => true,
                ]
            );

            $user->forceFill([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'provider_avatar' => $googleUser->getAvatar(),
            ])->save();

            Log::info('Usuário social identificado com sucesso.', [
                'id' => $user->id,
                'email' => $user->email,
                'is_active' => $user->is_active,
            ]);
            
        } catch (Throwable $e) {
            Log::error('Erro ao buscar usuario no banco.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'email_buscado' => $googleEmail,
            ]);
            
            return redirect('/login')->withErrors([
                'google' => 'Erro interno ao validar o usuario no sistema. Contate o administrador.',
            ]);
        }

        if (! $user->is_active) {
            Log::warning('Usuário Google inativo.', [
                'email' => $googleEmail,
            ]);

            return redirect('/login')->withErrors([
                'google' => 'Usuário inativo no sistema. Contate o administrador.',
            ]);
        }

        Auth::login($user);

        Log::info('Login Google realizado com sucesso.', ['user_id' => $user->id]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function googleConnectionContext(): array
    {
        $verify = config('services.google.guzzle.verify');

        return [
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'ca_bundle' => config('services.google.ca_bundle'),
            'verify' => $verify,
            'verify_is_file' => is_string($verify) ? is_file($verify) : null,
            'curl_cainfo' => ini_get('curl.cainfo') ?: null,
            'openssl_cafile' => ini_get('openssl.cafile') ?: null,
        ];
    }

    private function buildGoogleConnectionErrorMessage(Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());

        if (str_contains($message, 'curl error 60') || str_contains($message, 'ssl certificate problem')) {
            return 'Falha de SSL ao conectar com o Google. O ambiente PHP/WAMP esta usando um CA bundle que nao valida a cadeia do endpoint. O projeto foi configurado para usar um bundle explicito; se o erro persistir, atualize o arquivo cacert.pem do WAMP ou inclua o certificado raiz da sua rede.';
        }

        return 'Erro ao conectar com Google. Revise a configuracao do OAuth e a conectividade HTTPS do ambiente.';
    }
}
