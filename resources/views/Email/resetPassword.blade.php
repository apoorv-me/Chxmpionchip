@component('mail::message')
# Reset Password

<!-- Reset or change your password. -->

OTP :- {{$token}}
<!-- @component('mail::button', ['url' => route('viewReset').'?token='.$token]) -->
Change Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

