@component('mail::message')
# Email Verification

Thanks for registering with our website. Please click the button below to verify your email:

@component('mail::button', ['url' => $verificationUrl])
Verify Email
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
