@extends('layouts.admin')
@include('partials/admin.settings.nav', ['activeTab' => 'mail'])

@section('title')
  {{ trans('strings.admin_mail_settings') }}
@endsection

@section('content-header')
  <h1>{{ trans('strings.admin_mail_settings') }}<small>{{ trans('strings.admin_mail_settings_desc') }}</small></h1>
  <ol class="breadcrumb">
    <li><a href="{{ route('admin.index') }}">{{ trans('strings.admin') }}</a></li>
    <li class="active">{{ trans('strings.admin_settings') }}</li>
  </ol>
@endsection

@section('content')
  @yield('settings::nav')
  <div class="row">
    <div class="col-xs-12">
    <div class="box">
      <div class="box-header with-border">
      <h3 class="box-title">{{ trans('strings.admin_email_settings') }}</h3>
      </div>
      @if($disabled)
      <div class="box-body">
      <div class="row">
      <div class="col-xs-12">
      <div class="alert alert-info no-margin-bottom">
        {{ trans('strings.admin_smtp_only') }}
      </div>
      </div>
      </div>
      </div>
    @else
      <form>
      <div class="box-body">
      <div class="row">
      <div class="form-group col-md-6">
        <label class="control-label">{{ trans('strings.admin_smtp_host') }}</label>
        <div>
        <input required type="text" class="form-control" name="mail:mailers:smtp:host"
        value="{{ old('mail:mailers:smtp:host', config('mail.mailers.smtp.host')) }}" />
        <p class="text-muted small">{{ trans('strings.admin_smtp_host_desc') }}</p>
        </div>
      </div>
      <div class="form-group col-md-2">
        <label class="control-label">{{ trans('strings.admin_smtp_port') }}</label>
        <div>
        <input required type="number" class="form-control" name="mail:mailers:smtp:port"
        value="{{ old('mail:mailers:smtp:port', config('mail.mailers.smtp.port')) }}" />
        <p class="text-muted small">{{ trans('strings.admin_smtp_port_desc') }}</p>
        </div>
      </div>
      <div class="form-group col-md-4">
        <label class="control-label">{{ trans('strings.admin_encryption') }}</label>
        <div>
        @php
      $encryption = old('mail:mailers:smtp:encryption', config('mail.mailers.smtp.encryption'));
      @endphp
        <select name="mail:mailers:smtp:encryption" class="form-control">
        <option value="" @if($encryption === '') selected @endif>{{ trans('strings.admin_none') }}</option>
        <option value="tls" @if($encryption === 'tls') selected @endif>{{ trans('strings.admin_tls') }}</option>
        <option value="ssl" @if($encryption === 'ssl') selected @endif>{{ trans('strings.admin_ssl') }}</option>
        </select>
        <p class="text-muted small">{{ trans('strings.admin_encryption_desc') }}</p>
        </div>
      </div>
      <div class="form-group col-md-6">
        <label class="control-label">{{ trans('strings.admin_username') }} <span class="field-optional"></span></label>
        <div>
        <input type="text" class="form-control" name="mail:mailers:smtp:username"
        value="{{ old('mail:mailers:smtp:username', config('mail.mailers.smtp.username')) }}" />
        <p class="text-muted small">{{ trans('strings.admin_username_desc') }}</p>
        </div>
      </div>
      <div class="form-group col-md-6">
        <label class="control-label">{{ trans('strings.admin_password') }} <span class="field-optional"></span></label>
        <div>
        <input type="password" class="form-control" name="mail:mailers:smtp:password" />
        <p class="text-muted small">{!! trans('strings.admin_password_desc') !!}</p>
        </div>
      </div>
      </div>
      <div class="row">
      <hr />
      <div class="form-group col-md-6">
        <label class="control-label">{{ trans('strings.admin_mail_from') }}</label>
        <div>
        <input required type="email" class="form-control" name="mail:from:address"
        value="{{ old('mail:from:address', config('mail.from.address')) }}" />
        <p class="text-muted small">{{ trans('strings.admin_mail_from_desc') }}</p>
        </div>
      </div>
      <div class="form-group col-md-6">
        <label class="control-label">{{ trans('strings.admin_mail_from_name') }} <span class="field-optional"></span></label>
        <div>
        <input type="text" class="form-control" name="mail:from:name"
        value="{{ old('mail:from:name', config('mail.from.name')) }}" />
        <p class="text-muted small">{{ trans('strings.admin_mail_from_name_desc') }}</p>
        </div>
      </div>
      </div>
      </div>
      <div class="box-footer">
      {{ csrf_field() }}
      <div class="pull-right">
      <button type="button" id="testButton" class="btn btn-sm btn-success">{{ trans('strings.admin_test') }}</button>
      <button type="button" id="saveButton" class="btn btn-sm btn-primary">{{ trans('strings.admin_save') }}</button>
      </div>
      </div>
      </form>
    @endif
    </div>
    </div>
  </div>
@endsection

@section('footer-scripts')
  @parent

  <script>
    function saveSettings() {
    return $.ajax({
      method: 'PATCH',
      url: '/admin/settings/mail',
      contentType: 'application/json',
      data: JSON.stringify({
      'mail:mailers:smtp:host': $('input[name="mail:mailers:smtp:host"]').val(),
      'mail:mailers:smtp:port': $('input[name="mail:mailers:smtp:port"]').val(),
      'mail:mailers:smtp:encryption': $('select[name="mail:mailers:smtp:encryption"]').val(),
      'mail:mailers:smtp:username': $('input[name="mail:mailers:smtp:username"]').val(),
      'mail:mailers:smtp:password': $('input[name="mail:mailers:smtp:password"]').val(),
      'mail:from:address': $('input[name="mail:from:address"]').val(),
      'mail:from:name': $('input[name="mail:from:name"]').val()
      }),
      headers: { 'X-CSRF-Token': $('input[name="_token"]').val() }
    }).fail(function (jqXHR) {
      showErrorDialog(jqXHR, 'save');
    });
    }

    function testSettings() {
    swal({
      type: 'info',
      title: '{{ trans('strings.admin_test_mail') }}',
      text: '{{ trans('strings.admin_test_mail_click') }}',
      showCancelButton: true,
      confirmButtonText: '{{ trans('strings.admin_test') }}',
      closeOnConfirm: false,
      showLoaderOnConfirm: true
    }, function () {
      $.ajax({
      method: 'POST',
      url: '/admin/settings/mail/test',
      headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
      }).fail(function (jqXHR) {
      showErrorDialog(jqXHR, 'test');
      }).done(function () {
      swal({
        title: '{{ trans('strings.admin_success') }}',
        text: '{{ trans('strings.admin_test_mail_success') }}',
        type: 'success'
      });
      });
    });
    }

    function saveAndTestSettings() {
    saveSettings().done(testSettings);
    }

    function showErrorDialog(jqXHR, verb) {
    console.error(jqXHR);
    var errorText = '';
    if (!jqXHR.responseJSON) {
      errorText = jqXHR.responseText;
    } else if (jqXHR.responseJSON.error) {
      errorText = jqXHR.responseJSON.error;
    } else if (jqXHR.responseJSON.errors) {
      $.each(jqXHR.responseJSON.errors, function (i, v) {
      if (v.detail) {
        errorText += v.detail + ' ';
      }
      });
    }

    swal({
      title: '{{ trans('strings.admin_error') }}',
      text: '{{ trans('strings.admin_error_occurred') }} ' + verb + ' {{ trans('strings.admin_mail_settings') }}: ' + errorText,
      type: 'error'
    });
    }

    $(document).ready(function () {
    $('#testButton').on('click', saveAndTestSettings);
    $('#saveButton').on('click', function () {
      saveSettings().done(function () {
      swal({
        title: '{{ trans('strings.admin_success') }}',
        text: '{{ trans('strings.admin_mail_settings_updated') }}',
        type: 'success'
      });
      });
    });
    });
  </script>
@endsection