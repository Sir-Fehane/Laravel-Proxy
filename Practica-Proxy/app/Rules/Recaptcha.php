<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    public function __construct(
        protected string $expectedAction = ''
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! config('recaptcha.enabled')) {
            return;
        }

        if (empty($value)) {
            $fail(__('La verificación reCAPTCHA es requerida.'));
            return;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(config('recaptcha.verify_url'), [
                'secret' => config('recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $body = $response->json();

            if (! ($body['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'error-codes' => $body['error-codes'] ?? [],
                ]);
                $fail(__('La verificación reCAPTCHA falló. Intenta de nuevo.'));
                return;
            }

            if (($body['score'] ?? 0) < config('recaptcha.score_threshold')) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $body['score'] ?? 0,
                    'threshold' => config('recaptcha.score_threshold'),
                ]);
                $fail(__('La verificación reCAPTCHA falló. Intenta de nuevo.'));
                return;
            }

            if ($this->expectedAction && ($body['action'] ?? '') !== $this->expectedAction) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $this->expectedAction,
                    'actual' => $body['action'] ?? '',
                ]);
                $fail(__('La verificación reCAPTCHA falló. Intenta de nuevo.'));
            }
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', ['message' => $e->getMessage()]);
            $fail(__('No se pudo verificar reCAPTCHA. Intenta de nuevo.'));
        }
    }
}
