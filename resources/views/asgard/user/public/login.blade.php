@extends('layouts.master')


@section('content')

<!-- Login form -->
@include('flash::message')

{!! Form::open(['route' => 'login.post', 'class' => 'form-login form-wrapper form-narrow']) !!}
<h3 class="title-divider">
    <span>{{ trans('user::auth.login') }}</span>
    <small>¿No registrado? <a href="{{URL::route('register')}}" class="text-center">{{ trans('user::auth.register')}}</a>.</small>
</h3>
<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <label class="sr-only" for="login-email-page">{{ trans('user::auth.email') }}</label>
    <input type="email" id="login-email-page" name="email" class="form-control email"
           placeholder="{{ trans('user::auth.email') }}" value="{{ Input::old('email')}}" autofocus />
    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
</div>
<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    <label class="sr-only" for="login-password-page">{{ trans('user::auth.password') }}</label>
    <input type="password" name="password" id="login-password-page"
           class="form-control password" placeholder="{{ trans('user::auth.password') }}"
           value="{{ Input::old('password')}}"/>
    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
</div>
<button type="submit" class="btn btn-primary">{{ trans('user::auth.login') }}</button>
| <a href="{{URL::route('reset')}}">{{ trans('user::auth.forgot password') }}</a>
</form>
@stop