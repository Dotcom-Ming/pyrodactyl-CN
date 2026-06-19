<?php

namespace Pterodactyl\Exceptions\Dns;

use Exception;

class DnsProviderException extends Exception
{
    /**
     * Create a new DNS provider exception.
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create an exception for connection failures.
     */
    public static function connectionFailed(string $provider, string $reason = ''): self
    {
        return new self($reason
            ? trans('dns.exceptions.connection_failed_reason', [
                'provider' => self::providerName($provider),
                'reason' => $reason,
            ])
            : trans('dns.exceptions.connection_failed', ['provider' => self::providerName($provider)]));
    }

    /**
     * Create an exception for authentication failures.
     */
    public static function authenticationFailed(string $provider): self
    {
        return new self(trans('dns.exceptions.authentication_failed', ['provider' => self::providerName($provider)]));
    }

    /**
     * Create an exception for invalid configuration.
     */
    public static function invalidConfiguration(string $provider, string $field): self
    {
        return new self(trans('dns.exceptions.invalid_configuration', [
            'provider' => self::providerName($provider),
            'field' => self::fieldName($field),
        ]));
    }

    /**
     * Create an exception for record creation failures.
     */
    public static function recordCreationFailed(string $domain, string $subdomain, string $reason = ''): self
    {
        $record = "{$subdomain}.{$domain}";

        return new self($reason
            ? trans('dns.exceptions.record_creation_failed_reason', ['record' => $record, 'reason' => $reason])
            : trans('dns.exceptions.record_creation_failed', ['record' => $record]));
    }

    /**
     * Create an exception for record update failures.
     */
    public static function recordUpdateFailed(string $domain, array $recordIds, string $reason = ''): self
    {
        $recordList = implode(', ', $recordIds);

        return new self($reason
            ? trans('dns.exceptions.record_update_failed_reason', [
                'records' => $recordList,
                'domain' => $domain,
                'reason' => $reason,
            ])
            : trans('dns.exceptions.record_update_failed', [
                'records' => $recordList,
                'domain' => $domain,
            ]));
    }

    /**
     * Create an exception for record deletion failures.
     */
    public static function recordDeletionFailed(string $domain, array $recordIds, string $reason = ''): self
    {
        $recordList = implode(', ', $recordIds);

        return new self($reason
            ? trans('dns.exceptions.record_deletion_failed_reason', [
                'records' => $recordList,
                'domain' => $domain,
                'reason' => $reason,
            ])
            : trans('dns.exceptions.record_deletion_failed', [
                'records' => $recordList,
                'domain' => $domain,
            ]));
    }

    /**
     * Create an exception for unsupported record types.
     */
    public static function unsupportedRecordType(string $provider, string $recordType): self
    {
        return new self(trans('dns.exceptions.unsupported_record_type', [
            'provider' => self::providerName($provider),
            'record_type' => $recordType,
        ]));
    }

    private static function providerName(string $provider): string
    {
        $key = "dns.provider_names.{$provider}";
        $translated = trans($key);

        return $translated === $key ? $provider : $translated;
    }

    private static function fieldName(string $field): string
    {
        $key = "dns.attributes.{$field}";
        $translated = trans($key);

        return $translated === $key ? $field : $translated;
    }
}
