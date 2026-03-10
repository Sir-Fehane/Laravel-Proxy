<x-mail::message>
# Hola, {{ $name }}

Tu código de verificación es:

<x-mail::panel>
<div style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px;">
{{ $code }}
</div>
</x-mail::panel>

Este código expira en **10 minutos**. Si no solicitaste este código, puedes ignorar este mensaje.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
