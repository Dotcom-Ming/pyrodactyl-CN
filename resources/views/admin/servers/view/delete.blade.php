@extends('layouts.admin')

@section('title')
    {{ $server->name }}: {{ trans('strings.admin_delete_server') }}
@endsection

@section('content-header')
    <h1>{{ $server->name }}<small>{{ trans('strings.admin_delete_server_desc') }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
        <li><a href="{{ route('admin.servers') }}">{{ trans('strings.admin_servers') }}</a></li>
        <li><a href="{{ route('admin.servers.view', $server->id) }}">{{ $server->name }}</a></li>
        <li class="active">{{ trans('strings.admin_delete_server') }}</li>
    </ol>
@endsection

@section('content')
@include('admin.servers.partials.navigation')
<div class="row">
    <div class="col-md-6">
            <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('strings.admin_safely_delete_server') }}</h3>
            </div>
            <div class="box-body">
                <p>{{ trans('strings.admin_safe_delete_desc') }}</p>
                <p class="text-danger small">{!! trans('strings.admin_delete_server_warning') !!}</p>
            </div>
            <div class="box-footer">
                <form id="deleteform" action="{{ route('admin.servers.view.delete', $server->id) }}" method="POST">
                    {!! csrf_field() !!}
                    <button id="deletebtn" class="btn btn-danger">{{ trans('strings.admin_safely_delete_this_server') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
            <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('strings.admin_force_delete_server') }}</h3>
            </div>
            <div class="box-body">
                <p>{{ trans('strings.admin_force_delete_desc') }}</p>
                <p class="text-danger small">{!! trans('strings.admin_force_delete_warning') !!}</p>
            </div>
            <div class="box-footer">
                <form id="forcedeleteform" action="{{ route('admin.servers.view.delete', $server->id) }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="force_delete" value="1" />
                    <button id="forcedeletebtn" class="btn btn-danger">{{ trans('strings.admin_forcibly_delete_this_server') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    @php
        $deleteTranslations = [
            'confirm' => trans('strings.admin_delete_server_confirm'),
            'delete' => trans('strings.admin_delete'),
        ];
    @endphp
    <script>
    const deleteTranslations = @json($deleteTranslations);
    </script>
    <script>
    $('#deletebtn').click(function (event) {
        event.preventDefault();
        swal({
            title: '',
            type: 'warning',
            text: deleteTranslations.confirm,
            showCancelButton: true,
            confirmButtonText: deleteTranslations.delete,
            confirmButtonColor: '#d9534f',
            closeOnConfirm: false
        }, function () {
            $('#deleteform').submit()
        });
    });

    $('#forcedeletebtn').click(function (event) {
        event.preventDefault();
        swal({
            title: '',
            type: 'warning',
            text: deleteTranslations.confirm,
            showCancelButton: true,
            confirmButtonText: deleteTranslations.delete,
            confirmButtonColor: '#d9534f',
            closeOnConfirm: false
        }, function () {
            $('#forcedeleteform').submit()
        });
    });
    </script>
@endsection
