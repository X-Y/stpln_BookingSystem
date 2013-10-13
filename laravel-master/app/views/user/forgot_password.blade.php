@extends('base')

@section('content')

<form method="POST" action="{{ (Confide::checkAction('UserController@do_forgot_password')) ?: URL::to('/user/forgot') }}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">

    <div class="input-append">
        <input placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">

        <input class="btn" type="submit" value="{{{ Lang::get('confide::confide.forgot.submit') }}}">
    </div>

    @if ( Session::get('form_error') )
        <div class="alert alert-error">{{{ Session::get('form_error') }}}</div>
    @endif

    @if ( Session::get('notice') )
        <div class="alert">{{{ Session::get('notice') }}}</div>
    @endif
</form>

@endsection