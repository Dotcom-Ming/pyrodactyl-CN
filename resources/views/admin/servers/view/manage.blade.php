@extends('layouts.admin')

@section('title')
    {{ $server->name }}: {{ trans('strings.admin_manage') }}
@endsection

@section('content-header')
    <h1>{{ $server->name }}<small>{{ trans('strings.admin_manage_server_actions_desc') }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
        <li><a href="{{ route('admin.servers') }}">{{ trans('strings.admin_servers') }}</a></li>
        <li><a href="{{ route('admin.servers.view', $server->id) }}">{{ $server->name }}</a></li>
        <li class="active">{{ trans('strings.admin_manage') }}</li>
    </ol>
@endsection

@section('content')
    @include('admin.servers.partials.navigation')
    <div class="row">
        <div class="col-sm-4">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('strings.admin_reinstall_server') }}</h3>
                </div>
                <div class="box-body">
                    <p>{{ trans('strings.admin_reinstall_server_desc') }}</p>
                </div>
                <div class="box-footer">
                    @if($server->isInstalled())
                        <form action="{{ route('admin.servers.view.manage.reinstall', $server->id) }}" method="POST">
                            {!! csrf_field() !!}
                            <button type="submit" class="btn btn-danger">{{ trans('strings.admin_reinstall_server') }}</button>
                        </form>
                    @else
                        <button class="btn btn-danger disabled">{{ trans('strings.admin_server_must_install_reinstall') }}</button>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('strings.admin_install_status') }}</h3>
                </div>
                <div class="box-body">
                    <p>{{ trans('strings.admin_install_status_desc') }}</p>
                </div>
                <div class="box-footer">
                    <form action="{{ route('admin.servers.view.manage.toggle', $server->id) }}" method="POST">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-primary">{{ trans('strings.admin_toggle_install_status') }}</button>
                    </form>
                </div>
            </div>
        </div>

        @if(! $server->isSuspended())
            <div class="col-sm-4">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('strings.admin_suspend_server') }}</h3>
                    </div>
                    <div class="box-body">
                        <p>{{ trans('strings.admin_suspend_server_desc') }}</p>
                    </div>
                    <div class="box-footer">
                        <form action="{{ route('admin.servers.view.manage.suspension', $server->id) }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="action" value="suspend" />
                            <button type="submit" class="btn btn-warning @if(! is_null($server->transfer)) disabled @endif">{{ trans('strings.admin_suspend_server') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="col-sm-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('strings.admin_unsuspend_server') }}</h3>
                    </div>
                    <div class="box-body">
                        <p>{{ trans('strings.admin_unsuspend_server_desc') }}</p>
                    </div>
                    <div class="box-footer">
                        <form action="{{ route('admin.servers.view.manage.suspension', $server->id) }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="action" value="unsuspend" />
                            <button type="submit" class="btn btn-success">{{ trans('strings.admin_unsuspend_server') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if(is_null($server->transfer))
            <div class="col-sm-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('strings.admin_transfer_server') }}</h3>
                    </div>
                    <div class="box-body">
                        <p>
                            {{ trans('strings.admin_transfer_server_desc') }}
                            <strong>{{ trans('strings.admin_transfer_server_warning') }}</strong>
                        </p>
                    </div>

                    <div class="box-footer">
                        @if($canTransfer)
                            <button class="btn btn-success" data-toggle="modal" data-target="#transferServerModal">{{ trans('strings.admin_transfer_server') }}</button>
                        @else
                            <button class="btn btn-success disabled">{{ trans('strings.admin_transfer_server') }}</button>
                            <p style="padding-top: 1rem;">{{ trans('strings.admin_transfer_requires_nodes') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="col-sm-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('strings.admin_transfer_server') }}</h3>
                    </div>
                    <div class="box-body">
                        <p>
                            {{ trans('strings.admin_server_transferring') }}
                            {!! trans('strings.admin_transfer_initiated_at', ['date' => $server->transfer->created_at]) !!}
                        </p>
                    </div>

                    <div class="box-footer">
                        <button class="btn btn-success disabled">{{ trans('strings.admin_transfer_server') }}</button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="transferServerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.servers.view.manage.transfer', $server->id) }}" method="POST">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('strings.admin_close') }}"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{ trans('strings.admin_transfer_server') }}</h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="pNodeId">{{ trans('strings.admin_node') }}</label>
                                <select name="node_id" id="pNodeId" class="form-control">
                                    @foreach($locations as $location)
                                        <optgroup label="{{ $location->long }} ({{ $location->short }})">
                                            @foreach($location->nodes as $node)

                                                @if($node->id != $server->node_id)
                                                    <option value="{{ $node->id }}"
                                                            @if($location->id === old('location_id')) selected @endif
                                >{{ $node->name }}</option>
                                                @endif

                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <p class="small text-muted no-margin">{{ trans('strings.admin_transfer_node_desc') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="pAllocation">{{ trans('strings.admin_default_allocation') }}</label>
                                <select name="allocation_id" id="pAllocation" class="form-control"></select>
                                <p class="small text-muted no-margin">{{ trans('strings.admin_default_allocation_desc_transfer') }}</p>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="pAllocationAdditional">{{ trans('strings.admin_additional_allocations') }}</label>
                                <select name="allocation_additional[]" id="pAllocationAdditional" class="form-control" multiple></select>
                                <p class="small text-muted no-margin">{{ trans('strings.admin_additional_allocations_desc_transfer') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        {!! csrf_field() !!}
                        <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal">{{ trans('strings.admin_cancel') }}</button>
                        <button type="submit" class="btn btn-success btn-sm">{{ trans('strings.admin_confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    {!! Theme::js('vendor/lodash/lodash.js') !!}

    @if($canTransfer)
        {!! Theme::js('js/admin/server/transfer.js') !!}
    @endif
@endsection
