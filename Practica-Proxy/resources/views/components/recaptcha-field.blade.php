<div>
    <input type="hidden" name="recaptcha_token" id="{{ $fieldId }}">

    @if ($errorBag === 'default')
        <x-input-error :messages="$errors->get('recaptcha_token')" class="mt-2" />
    @else
        <x-input-error :messages="$errors->{$errorBag}->get('recaptcha_token')" class="mt-2" />
    @endif

    <script>
        (function() {
            var field = document.getElementById('{{ $fieldId }}');
            if (!field) return;

            var form = field.closest('form');
            if (!form || form.dataset.recaptchaBound) return;
            form.dataset.recaptchaBound = 'true';

            form.addEventListener('submit', function(e) {
                if (field.value) return;

                e.preventDefault();
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config("recaptcha.site_key") }}', { action: '{{ $action }}' })
                        .then(function(token) {
                            field.value = token;
                            form.submit();
                        });
                });
            });
        })();
    </script>
</div>
