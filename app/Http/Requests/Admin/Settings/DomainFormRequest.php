<?php

namespace Pterodactyl\Http\Requests\Admin\Settings;

use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Pterodactyl\Enums\Subdomain\Providers;

class DomainFormRequest extends AdminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $domainId = $this->route('domain')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/',
                $domainId ? "unique:domains,name,{$domainId}" : 'unique:domains,name',
            ],
            'dns_provider' => 'required|string|in:' . implode(',', Providers::values()),
            'dns_config' => 'required|array',
            'dns_config.api_token' => 'required_if:dns_provider,cloudflare,hetzner,dnsimple|string|min:1',
            'dns_config.access_key_id' => 'required_if:dns_provider,route53|string|min:1',
            'dns_config.secret_access_key' => 'required_if:dns_provider,route53|string|min:1',
            'dns_config.account_id' => 'sometimes|string|min:1',
            'dns_config.region' => 'sometimes|string|min:1',
            'dns_config.hosted_zone_id' => 'sometimes|string|min:1',
            'dns_config.zone_id' => 'sometimes|string|min:1',
            'is_active' => 'sometimes|boolean',
            'is_default' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => trans('dns.validation.domain_name_required'),
            'name.regex' => trans('dns.validation.domain_name_invalid'),
            'name.unique' => trans('dns.validation.domain_name_unique'),
            'dns_provider.required' => trans('dns.validation.provider_required'),
            'dns_provider.in' => trans('dns.validation.provider_unsupported'),
            'dns_config.required' => trans('dns.validation.config_required'),
            'dns_config.api_token.required_if' => trans('dns.validation.api_token_required'),
            'dns_config.access_key_id.required_if' => trans('dns.validation.access_key_required'),
            'dns_config.secret_access_key.required_if' => trans('dns.validation.secret_key_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => trans('dns.attributes.domain_name'),
            'dns_provider' => trans('dns.attributes.dns_provider'),
            'dns_config.api_token' => trans('dns.attributes.api_token'),
            'dns_config.account_id' => trans('dns.attributes.account_id'),
            'dns_config.access_key_id' => trans('dns.attributes.access_key_id'),
            'dns_config.secret_access_key' => trans('dns.attributes.secret_access_key'),
            'dns_config.region' => trans('dns.attributes.region'),
            'dns_config.hosted_zone_id' => trans('dns.attributes.hosted_zone_id'),
            'dns_config.zone_id' => trans('dns.attributes.zone_id'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize domain name to lowercase
        if ($this->has('name')) {
            $this->merge([
                'name' => strtolower(trim($this->input('name'))),
            ]);
        }

        // Ensure boolean fields are properly cast
        foreach (['is_active', 'is_default'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }
}
