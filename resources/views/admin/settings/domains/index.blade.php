@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'domains'])

@section('title')
  {{ trans('strings.admin_domain_management') }}
@endsection

@section('content-header')
  <h1>{{ trans('strings.admin_domain_management') }}<small>{{ trans('strings.admin_domain_management_desc') }}</small></h1>
  <ol class="breadcrumb">
    <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
    <li><a href="{{ route('admin.settings') }}">{{ trans('strings.admin_settings') }}</a></li>
    <li class="active">{{ trans('strings.admin_settings_domains') }}</li>
  </ol>
@endsection

@section('content')
  @yield('settings::nav')
  <div class="row">
    <div class="col-xs-12">
        <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">{{ trans('strings.admin_configured_domains') }}</h3>
          <div class="box-tools">
            <a href="{{ route('admin.settings.domains.create') }}" class="btn btn-sm btn-primary">{{ trans('strings.admin_create_new_domain') }}</a>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          @if(count($domains) > 0)
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>{{ trans('strings.admin_domain_name') }}</th>
                  <th>{{ trans('strings.admin_dns_provider') }}</th>
                  <th>{{ trans('strings.admin_status') }}</th>
                  <th>{{ trans('strings.admin_default') }}</th>
                  <th>{{ trans('strings.admin_subdomains') }}</th>
                  <th>{{ trans('strings.admin_created') }}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($domains as $domain)
                  <tr>
                    <td><code>{{ $domain->name }}</code></td>
                    <td>
                      <span class="label label-primary">{{ ucfirst($domain->dns_provider) }}</span>
                    </td>
                    <td>
                      @if($domain->is_active)
                        <span class="label label-success">{{ trans('strings.admin_active') }}</span>
                      @else
                        <span class="label label-danger">{{ trans('strings.admin_inactive') }}</span>
                      @endif
                    </td>
                    <td>
                      @if($domain->is_default)
                        <span class="label label-info">{{ trans('strings.admin_default') }}</span>
                      @endif
                    </td>
                    <td>
                      <span class="label label-default">{{ $domain->server_subdomains_count ?? 0 }}</span>
                    </td>
                    <td>{{ $domain->created_at->diffForHumans() }}</td>
                    <td class="text-center">
                      <a href="{{ route('admin.settings.domains.edit', $domain) }}" class="btn btn-xs btn-primary">{{ trans('strings.admin_edit') }}</a>
                      @if($domain->server_subdomains_count == 0)
                        <form action="{{ route('admin.settings.domains.destroy', $domain) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ trans('strings.admin_confirm_delete_domain') }}')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-danger">{{ trans('strings.admin_delete') }}</button>
                        </form>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="text-center" style="padding: 50px;">
              <h4 class="text-muted">{{ trans('strings.admin_no_domains') }}</h4>
              <p class="text-muted">
                {{ trans('strings.admin_no_domains_desc') }}<br>
                <a href="{{ route('admin.settings.domains.create') }}" class="btn btn-primary btn-sm" style="margin-top: 10px;">{{ trans('strings.admin_create_first_domain') }}</a>
              </p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@section('footer-scripts')
  @parent
  <script>
    $(document).ready(function() {
      $('.btn-danger').click(function(e) {
        if (!confirm('{{ trans('strings.admin_confirm_delete_domain') }}')) {
          e.preventDefault();
          return false;
        }
      });
    });
  </script>
@endsection
