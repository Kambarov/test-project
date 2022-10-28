<x-mail::message>
{{ trans('app.verify_email', ['user' => $user->name,'link' => $url]) }}

<strong>{{ trans('app.link_active_time') }}</strong>

<x-mail::button :url="$url">
{{ trans('app.proceed') }}
</x-mail::button>

{{ trans('app.thanks') }},<br>
{{ config('app.name') }}
</x-mail::message>
