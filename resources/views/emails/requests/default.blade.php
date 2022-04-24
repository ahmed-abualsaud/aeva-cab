@component('mail::message')
# Hello

{{ $message }}

@component('mail::button', ['url' => $url])
View Request
@endcomponent

Regards,<br>
Aeva Mobility Team
@endcomponent