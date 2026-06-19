@extends('layouts.admin')

@section('title')
    {{ $node->name }}: {{ trans('strings.admin_configuration') }}
@endsection

@section('content-header')
    <h1>{{ $node->name }}<small>{{ trans('strings.admin_daemon_config_desc') }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
        <li><a href="{{ route('admin.nodes') }}">{{ trans('strings.admin_nodes') }}</a></li>
        <li><a href="{{ route('admin.nodes.view', $node->id) }}">{{ $node->name }}</a></li>
        <li class="active">{{ trans('strings.admin_configuration') }}</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
       <div class="nav-tabs-custom nav-tabs-floating">
            <ul class="nav nav-tabs">
                <li><a href="{{ route('admin.nodes.view', $node->id) }}">{{ trans('strings.admin_about') }}</a></li>
                <li><a href="{{ route('admin.nodes.view.settings', $node->id) }}">{{ trans('strings.admin_settings') }}</a></li>
                <li class="active"><a href="{{ route('admin.nodes.view.configuration', $node->id) }}">{{ trans('strings.admin_configuration') }}</a></li>
                <li><a href="{{ route('admin.nodes.view.allocation', $node->id) }}">{{ trans('strings.admin_allocation') }}</a></li>
                <li><a href="{{ route('admin.nodes.view.servers', $node->id) }}">{{ trans('strings.admin_servers') }}</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('strings.admin_configuration_file') }}</h3>
            </div>
            <div class="box-body">
                <pre class="no-margin">{{ $node->getYamlConfiguration() }}</pre>
            </div>
            <div class="box-footer">
                <p class="no-margin">{!! trans('strings.admin_configuration_file_desc') !!}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('strings.admin_auto_deploy') }}</h3>
            </div>
            <div class="box-body">
                <p class="text-muted small">
                    {{ trans('strings.admin_auto_deploy_desc') }}
                </p>
            </div>
            <div class="box-footer">
                <button type="button" id="configTokenBtn" class="btn btn-sm btn-default" style="width:100%;">{{ trans('strings.admin_generate_token') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
    $('#configTokenBtn').on('click', function (event) {
        $.ajax({
            method: 'POST',
            url: '{{ route('admin.nodes.view.configuration.token', $node->id) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        }).done(function (data) {

            var commandTemplate = "{!! addslashes($node->getAutoDeploy("PLACEHOLDER_TOKEN")) !!}";
            var command = commandTemplate.replace('PLACEHOLDER_TOKEN', data.token);
            swal({
                type: 'success',
                title: @json(trans('strings.admin_token_created')),
                text: "<p>" + @json(trans('strings.admin_auto_configure_command')) + "<br /><small><pre>" + command + "</pre></small></p>",
                html: true,
            })
        }).fail(function () {
            swal({
                title: @json(trans('strings.admin_error')),
                text: @json(trans('strings.admin_token_create_failed')),
                type: 'error'
            });
        });
    });
    </script>
@endsection
