@extends('layouts/basic')


{{-- Page content --}}
@section('content')
{{ Form::open(['route' => 'mandatory-password-change', 'method' => 'POST', 'class' => 'form-horizontal'])}}
    {!! csrf_field() !!}
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box login-box" style="width: 100%">
                    <div class="box-header with-border">
                        <h3 class="box-title">@lang('auth/general.change_password')</h3>
                    </div>
                    <div class="login-box-body">
                        <p><strong>@lang('auth/password.required_to_change')</strong></p>
                        <p>@lang('auth/password.mandatory_rules')</p>
                        <ul>
                            @foreach($passwordRules as $rule)
                            <li>{{ $rule }}</li>
                            @endforeach
                        </ul>
                        <div class="row">
                            <!-- Notifications -->
                            @include('notifications')
                
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                {{ Form::label('password', trans('admin/users/table.password'), ['class' => 'col-md-4 control-label']) }}
                                <div class="col-md-6">
                                    {{ Form::password('password', ['class' => 'form-control']) }}
                                    {!! $errors->first('password', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                {{ Form::label('password_confirmation', trans('admin/users/table.password_confirm'), ['class' => 'col-md-4 control-label']) }}
                                <div class="col-md-6">
                                    {{ Form::password('password_confirmation', ['class' => 'form-control']) }}
                                    {!! $errors->first('password_confirmation', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-lg btn-primary btn-block">
                            {{ trans('auth/general.change_password')  }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}

@stop


