@extends('layouts.admin')

@section('title')
    {{ trans('strings.admin_api_credentials') }}
@endsection

@section('content-header')
    <h1>{{ trans('strings.admin_api_credentials') }}<small>{{ trans('strings.admin_api_credentials_desc') }}</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
        <li class="active">{{ trans('strings.admin_api_credentials') }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('strings.admin_credentials_list') }}</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.api.new') }}" class="btn btn-sm btn-primary">{{ trans('strings.admin_create_new') }}</a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>{{ trans('strings.admin_key') }}</th>
                            <th>{{ trans('strings.admin_memo') }}</th>
                            <th>{{ trans('strings.admin_last_used') }}</th>
                            <th>{{ trans('strings.admin_created') }}</th>
                            <th></th>
                        </tr>
                        @foreach($keys as $key)
                            <tr>
                                <td><code>{{ $key->identifier }}{{ decrypt($key->token) }}</code></td>
                                <td>{{ $key->memo }}</td>
                                <td>
                                    @if(!is_null($key->last_used_at))
                                        {{ $key->last_used_at->format('M j, Y g:i A') }}
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                    <td>{{ $key->created_at->format('M j, Y g:i A') }}</td>
                                <td>
                                    <a href="#" data-action="revoke-key" data-attr="{{ $key->identifier }}">
                                        <i class="fa fa-trash-o text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('[data-action="revoke-key"]').click(function (event) {
                var self = $(this);
                event.preventDefault();
                swal({
                    type: 'error',
                    title: @json(trans('strings.admin_revoke_api_key')),
                    text: @json(trans('strings.admin_revoke_api_key_warning')),
                    showCancelButton: true,
                    allowOutsideClick: true,
                    closeOnConfirm: false,
                    confirmButtonText: @json(trans('strings.admin_revoke')),
                    confirmButtonColor: '#d9534f',
                    showLoaderOnConfirm: true
                }, function () {
                    $.ajax({
                        method: 'DELETE',
                        url: '/admin/api/revoke/' + self.data('attr'),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).done(function () {
                    swal({
                        type: 'success',
                        title: '',
                        text: @json(trans('strings.admin_key_revoked'))
                    });
                    self.parent().parent().slideUp();
                }).fail(function (jqXHR) {
                    console.error(jqXHR);
                    swal({
                        type: 'error',
                        title: @json(trans('strings.admin_whoops')),
                        text: @json(trans('strings.admin_revoke_failed'))
                    });
                });
                });
            });
        });
    </script>
@endsection
