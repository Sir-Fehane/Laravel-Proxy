<x-mail::message>
# Hello, {{ $name }}

Your verification code is:

<x-mail::panel>
<div style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px;">
{{ $code }}
</div>
</x-mail::panel>

This code expires in **10 minutes**. If you did not request this code, you can ignore this message.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
