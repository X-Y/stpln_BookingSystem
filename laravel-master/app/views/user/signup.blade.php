@extends('base')

@section('content')
<p>
<strong>Note:</strong> Newly registered users don't have permission to book time. Please contact workshop manager to link your credits and get permission!
</p>
<form method="POST" class="register" action="{{{ (Confide::checkAction('UserController@store')) ?: URL::to('user')  }}}" accept-charset="UTF-8">
    <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
    <fieldset>
        <input placeholder="{{{ Lang::get('confide::confide.username') }}}" type="text" name="username" id="username" value="{{{ Input::old('username') }}}">

        <input placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">

        <input placeholder="phone number" type="text" name="phone" id="phone" value="{{{ Input::old('phone') }}}">

        <input placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password">

        <input placeholder="{{{ Lang::get('confide::confide.password_confirmation') }}}" type="password" name="password_confirmation" id="password_confirmation">

        @if ( Session::get('form_error') )
            <div class="alert alert-error">
                @if ( is_array(Session::get('form_error')) )
                    {{ head(Session::get('form_error')) }}
                @endif
            </div>
        @endif

        @if ( Session::get('notice') )
            <div class="alert">{{ Session::get('notice') }}</div>
        @endif

		<button type="submit" class="btn btn-primary">{{{ Lang::get('confide::confide.signup.submit') }}}</button>

    </fieldset>
</form>
@endsection