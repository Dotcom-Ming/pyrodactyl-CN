@extends('layouts.admin')

@section('title')
    {{ $server->name }}: {{ trans('strings.admin_build_configuration') }}
@endsection

@section('content-header')
    <h1>{{ $server->name }}<small>{{ trans('strings.admin_manage_server_build_desc') }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
        <li><a href="{{ route('admin.servers') }}">{{ trans('strings.admin_servers') }}</a></li>
        <li><a href="{{ route('admin.servers.view', $server->id) }}">{{ $server->name }}</a></li>
        <li class="active">{{ trans('strings.admin_build_configuration') }}</li>
    </ol>
@endsection

@section('content')
@include('admin.servers.partials.navigation')
<div class="row">
    <form action="{{ route('admin.servers.view.build', $server->id) }}" method="POST">
        <div class="col-sm-5">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('strings.admin_resource_management') }}</h3>
                </div>
                <div class="box-body">
                <div class="form-group">
                        <label for="cpu" class="control-label">{{ trans('strings.admin_cpu_limit') }}</label>
                        <div class="input-group">
                            <input type="text" name="cpu" class="form-control" value="{{ old('cpu', $server->cpu) }}"/>
                            <span class="input-group-addon">%</span>
                        </div>
                        <p class="text-muted small">{!! trans('strings.admin_cpu_limit_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="threads" class="control-label">{{ trans('strings.admin_cpu_pinning') }}</label>
                        <div>
                            <input type="text" name="threads" class="form-control" value="{{ old('threads', $server->threads) }}"/>
                        </div>
                        <p class="text-muted small"><strong>{{ trans('strings.admin_advanced') }}:</strong> {!! trans('strings.admin_cpu_pinning_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="memory" class="control-label">{{ trans('strings.admin_memory') }}</label>
                        <div class="input-group">
                            <input type="text" name="memory" data-multiplicator="true" class="form-control" value="{{ old('memory', $server->memory) }}"/>
                            <span class="input-group-addon">MiB</span>
                        </div>
                        <p class="text-muted small">{!! trans('strings.admin_memory_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="overhead_memory" class="control-label">{{ trans('strings.admin_overhead_memory') }}</label>
                        <div class="input-group">
                            <input type="text" name="overhead_memory" data-multiplicator="true" class="form-control" value="{{ old('overhead_memory', $server->overhead_memory) }}"/>
                            <span class="input-group-addon">MiB</span>
                        </div>
                        <p class="text-muted small">{!! trans('strings.admin_overhead_memory_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="swap" class="control-label">{{ trans('strings.admin_swap_space') }}</label>
                        <div class="input-group">
                            <input type="text" name="swap" data-multiplicator="true" class="form-control" value="{{ old('swap', $server->swap) }}"/>
                            <span class="input-group-addon">MiB</span>
                        </div>
                        <p class="text-muted small">{!! trans('strings.admin_swap_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="cpu" class="control-label">{{ trans('strings.admin_disk_space') }}</label>
                        <div class="input-group">
                            <input type="text" name="disk" class="form-control" value="{{ old('disk', $server->disk) }}"/>
                            <span class="input-group-addon">MiB</span>
                        </div>
                        <p class="text-muted small">{!! trans('strings.admin_disk_space_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="io" class="control-label">{{ trans('strings.admin_block_io_weight') }}</label>
                        <div>
                            <input type="text" name="io" class="form-control" value="{{ old('io', $server->io) }}"/>
                        </div>
                        <p class="text-muted small"><strong>{{ trans('strings.admin_advanced') }}</strong>: {!! trans('strings.admin_block_io_weight_desc') !!}</p>
                    </div>
                    <div class="form-group">
                        <label for="cpu" class="control-label">{{ trans('strings.admin_oom_killer') }}</label>
                        <div>
                            <div class="radio radio-danger radio-inline">
                                <input type="radio" id="pOomKillerEnabled" value="0" name="oom_disabled" @if(!$server->oom_disabled)checked @endif>
                                <label for="pOomKillerEnabled">{{ trans('strings.admin_enabled') }}</label>
                            </div>
                            <div class="radio radio-success radio-inline">
                                <input type="radio" id="pOomKillerDisabled" value="1" name="oom_disabled" @if($server->oom_disabled)checked @endif>
                                <label for="pOomKillerDisabled">{{ trans('strings.admin_disabled') }}</label>
                            </div>
                            <p class="text-muted small">
                                {{ trans('strings.admin_oom_killer_desc') }}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exclude_from_resource_calculation" class="control-label">{{ trans('strings.admin_resource_calculation') }}</label>
                        <div>
                            <div class="radio radio-success radio-inline">
                                <input type="radio" id="pResourceCalcIncluded" value="0" name="exclude_from_resource_calculation" @if(!$server->exclude_from_resource_calculation)checked @endif>
                                <label for="pResourceCalcIncluded">{{ trans('strings.admin_included') }}</label>
                            </div>
                            <div class="radio radio-warning radio-inline">
                                <input type="radio" id="pResourceCalcExcluded" value="1" name="exclude_from_resource_calculation" @if($server->exclude_from_resource_calculation)checked @endif>
                                <label for="pResourceCalcExcluded">{{ trans('strings.admin_excluded') }}</label>
                            </div>
                            <p class="text-muted small">
                                {{ trans('strings.admin_resource_calculation_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans('strings.admin_feature_limits') }}</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-xs-6">
                                    <label for="database_limit" class="control-label">{{ trans('strings.admin_database_limit') }}</label>
                                    <div>
                                        <input type="text" name="database_limit" class="form-control" value="{{ old('database_limit', $server->database_limit) }}"/>
                                    </div>
                                    <p class="text-muted small">{{ trans('strings.admin_database_limit_desc') }}</p>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label for="allocation_limit" class="control-label">{{ trans('strings.admin_allocation_limit') }}</label>
                                    <div>
                                        <input type="text" name="allocation_limit" class="form-control" value="{{ old('allocation_limit', $server->allocation_limit) }}"/>
                                    </div>
                                    <p class="text-muted small">{{ trans('strings.admin_allocation_limit_desc') }}</p>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label for="backup_limit" class="control-label">{{ trans('strings.admin_backup_limit') }}</label>
                                    <div>
                                        <input type="text" name="backup_limit" class="form-control" value="{{ old('backup_limit', $server->backup_limit) }}"/>
                                    </div>
                                    <p class="text-muted small">{{ trans('strings.admin_backup_limit_desc') }}</p>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label for="backup_storage_limit" class="control-label">{{ trans('strings.admin_backup_storage_limit') }}</label>
                                    <div class="input-group">
                                        <input type="text" name="backup_storage_limit" data-multiplicator="true" class="form-control" value="{{ old('backup_storage_limit', $server->backup_storage_limit) }}"/>
                                        <span class="input-group-addon">MiB</span>
                                    </div>
                                    <p class="text-muted small">{{ trans('strings.admin_backup_storage_limit_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans('strings.admin_allocation_management') }}</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="pAllocation" class="control-label">{{ trans('strings.admin_game_port') }}</label>
                                <select id="pAllocation" name="allocation_id" class="form-control">
                                    @foreach ($assigned as $assignment)
                                        <option value="{{ $assignment->id }}"
                                            @if($assignment->id === $server->allocation_id)
                                                selected="selected"
                                            @endif
                                        >{{ $assignment->alias }}:{{ $assignment->port }}</option>
                                    @endforeach
                                </select>
                                <p class="text-muted small">{{ trans('strings.admin_game_port_desc') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="pAddAllocations" class="control-label">{{ trans('strings.admin_assign_additional_ports') }}</label>
                                <div>
                                    <select name="add_allocations[]" class="form-control" multiple id="pAddAllocations">
                                        @foreach ($unassigned as $assignment)
                                            <option value="{{ $assignment->id }}">{{ $assignment->alias }}:{{ $assignment->port }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-muted small">{{ trans('strings.admin_assign_additional_ports_desc') }}</p>
                            </div>
                            <div class="form-group">
                                <label for="pRemoveAllocations" class="control-label">{{ trans('strings.admin_remove_additional_ports') }}</label>
                                <div>
                                    <select name="remove_allocations[]" class="form-control" multiple id="pRemoveAllocations">
                                        @foreach ($assigned as $assignment)
                                            <option value="{{ $assignment->id }}">{{ $assignment->alias }}:{{ $assignment->port }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-muted small">{{ trans('strings.admin_remove_additional_ports_desc') }}</p>
                            </div>
                        </div>
                        <div class="box-footer">
                            {!! csrf_field() !!}
                            <button type="submit" class="btn btn-primary pull-right">{{ trans('strings.admin_update_build_configuration') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footer-scripts')
    @parent
    <script>
    $('#pAddAllocations').select2();
    $('#pRemoveAllocations').select2();
    $('#pAllocation').select2();
    </script>
@endsection
