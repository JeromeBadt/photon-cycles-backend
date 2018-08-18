@component('mail::message')
# Reset Password Request

Click on the button below to reset your password.

@component('mail::button', ['url' => config('app.url') . '/response-password-reset?token=' . $token])
    Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
