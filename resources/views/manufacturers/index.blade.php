@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('admin/manufacturers/table.asset_manufacturers') }} 
@parent
@stop

{{-- Page title --}}
@section('header_right')
  @can('create', \App\Models\Manufacturer::class)
    <a href="{{ route('manufacturers.create') }}" class="btn btn-primary pull-right">
    {{ trans('general.create') }}</a>
  @endcan

  @if (Input::get('deleted')=='true')
    <a class="btn btn-default pull-right" href="{{ route('manufacturers.index') }}" style="margin-right: 5px;">{{ trans('general.show_current') }}</a>
  @else
    <a class="btn btn-default pull-right" href="{{ route('manufacturers.index', ['deleted' => 'true']) }}" style="margin-right: 5px;">
      {{ trans('general.show_deleted') }}</a>
  @endif

@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">
        {{ Form::open([
          'method' => 'POST',
          'route' => ['manufacturers.bulk'],
          'class' => 'form-inline',
           'id' => 'bulkForm']) }}        
        <div class="row">
          <div class="col-md-12">        
            <div id="toolbar">
              <select name="bulk_actions" class="form-control select2">
                <option value="merge">@lang('general.merge')</option>
              </select>
              <button class="btn btn-primary" id="bulkEdit" disabled>Go</button>
            </div>        
            <div class="table-responsive">

              <table
                data-columns="{{ \App\Presenters\ManufacturerPresenter::dataTableLayout() }}"
                data-cookie-id-table="manufacturersTable"
                data-pagination="true"
                data-id-table="manufacturersTable"
                data-search="true"
                data-show-footer="true"
                data-side-pagination="server"
                data-show-columns="true"
                data-show-export="true"
                data-show-refresh="true"
                data-sort-order="asc"
                data-toolbar="#toolbar"                
                id="manufacturersTable"
                class="table table-striped snipe-table"
                data-url="{{route('api.manufacturers.index', ['deleted' => e(Input::get('deleted')) ]) }}"
                data-export-options='{
                  "fileName": "export-manufacturers-{{ date('Y-m-d') }}",
                  "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                  }'>

              </table>
            </div>
          </div>
        </div>
        {{ Form::close() }}
      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div>
</div>

@stop

@section('moar_scripts')
  @include ('partials.bootstrap-table')
@stop
