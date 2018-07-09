@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/manufacturers/form.bulk_merge') }}
@parent
@stop

@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-sm btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop

@section('content')
<div class="row">
  <div class="col-md-8 col-md-offset-2">

    <p>{{ trans('admin/manufacturers/form.bulk_merge_help') }}</p>

    <div class="callout callout-warning">
      <i class="fa fa-warning"></i> {{ trans('admin/manufacturers/form.bulk_merge_warning', ['count' => $manufacturers_count]) }}
    </div>

    <form class="form-horizontal" method="post" action="{{ route('manufacturers.bulk-merge') }}" autocomplete="off" role="form">
      {{ csrf_field() }}

      <div class="box box-default">
        <div class="box-body">
          <!-- IDs -->
          @foreach ($ids as $id)
            <input type="hidden" name="ids[{{ $id }}]" value="1">
          @endforeach 

          <!-- Name -->
          <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="col-md-3 control-label">
              {{ trans('admin/manufacturers/table.name') }}
            </label>
            <div class="col-md-7 {{ (\App\Helpers\Helper::checkIfRequired(App\Models\Manufacturer::class, 'name')) ? 'required' : '' }}">
              {{ Form::select('name', $names , Input::old('name'), array('class'=>'select2', 'style' => 'width:100%')) }}
              {!! $errors->first('name', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
            </div>
          </div>

          <!-- Urls -->
          <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
            <label for="url" class="col-md-3 control-label">
              {{ trans('admin/manufacturers/table.url') }}
            </label>
            <div class="col-md-7 {{ (\App\Helpers\Helper::checkIfRequired(App\Models\Manufacturer::class, 'url')) ? 'required' : '' }}">
              {{ Form::select('url', $urls , Input::old('url'), array('class'=>'select2', 'style' => 'width:100%')) }}
              {!! $errors->first('url', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
            </div>
          </div>  

          <!-- Support Email -->
          <div class="form-group {{ $errors->has('support_url') ? ' has-error' : '' }}">
            <label for="name" class="col-md-3 control-label">
              {{ trans('admin/manufacturers/table.support_url') }}
            </label>
            <div class="col-md-7 {{ (\App\Helpers\Helper::checkIfRequired(App\Models\Manufacturer::class, 'support_url')) ? 'required' : '' }}">
              {{ Form::select('support_url', $support_urls , Input::old('support_url'), array('class'=>'select2', 'style' => 'width:100%')) }}
              {!! $errors->first('support_url', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
            </div>
          </div>  

          <!-- Support Email -->
          <div class="form-group {{ $errors->has('support_email') ? ' has-error' : '' }}">
            <label for="name" class="col-md-3 control-label">
              {{ trans('admin/manufacturers/table.support_email') }}
            </label>
            <div class="col-md-7 {{ (\App\Helpers\Helper::checkIfRequired(App\Models\Manufacturer::class, 'support_email')) ? 'required' : '' }}">
              {{ Form::select('support_email', $support_emails , Input::old('support_email'), array('class'=>'select2', 'style' => 'width:100%')) }}
              {!! $errors->first('support_email', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
            </div>
          </div>   

          <!-- Image -->
          <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
            <label for="name" class="col-md-3 control-label">
              {{ trans('general.image') }}
            </label>
            <div class="col-md-7">
              <label>
                  <input type="radio" name="image" value="">
                  {{ trans('admin/manufacturers/form.no_image') }}             
              </label>
              
              @foreach($images as $image_id => $image_file)
                <label>
                  <input type="radio" name="image" value="{{ $image_id }}">
                  <img src="{{ url('/') }}/uploads/manufacturers/{{ $image_file }}" />
                </label>
              @endforeach
              {!! $errors->first('image', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
            </div>
          </div>  
        </div> <!--/.box-body-->
        <div class="box-footer text-right">
          <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"></i> {{ trans('general.save') }}</button>
        </div>
      </div> <!--/.box.box-default-->
    </form>
  </div> <!--/.col-md-8-->
</div>
@stop