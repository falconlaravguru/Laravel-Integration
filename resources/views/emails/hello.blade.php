@component('mail::message')
# Introduction

Hi, {{ $Receiver }}

    Mr {{ $From }} just joined with us.
    And He is inviting you into our site.
    Please join us and enjoy.

@component('mail::button', ['url' => 'http://www.expertphp.in/article/laravel-5-login-with-google-oauth-apiclient-example?utm_source=learninglaravel.net'])
    Our Recommendation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
