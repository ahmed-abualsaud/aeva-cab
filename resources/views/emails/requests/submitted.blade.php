@component('mail::message')
# Request Submitted

{{ $message }}

@component('mail::button', ['url' => $url])
View Request
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent