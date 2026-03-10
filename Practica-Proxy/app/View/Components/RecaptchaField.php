<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RecaptchaField extends Component
{
    public string $fieldId;

    public function __construct(
        public string $action,
        ?string $fieldId = null,
        public string $errorBag = 'default',
    ) {
        $this->fieldId = $fieldId ?? 'recaptcha_token_' . $action;
    }

    public function shouldRender(): bool
    {
        return (bool) config('recaptcha.enabled');
    }

    public function render(): View|Closure|string
    {
        return view('components.recaptcha-field');
    }
}
