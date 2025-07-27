<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * UUID strings value with mask validation.
 * Validates UUID format and optionally enforces specific UUID versions or the special NIL UUID.
 *
 * ```
 * // General UUID validation (any valid UUID)
 * $uuidValue = new UuidValue();
 * $recordFilter = new Equals('id', $uuidValue);
 * $result = $recordFilter->withValue('550e8400-e29b-41d4-a716-446655440000'); // Valid UUID
 * $result = $recordFilter->withValue('invalid-uuid-format');                  // Invalid format
 * ```
 * ```
 * // UUID v4 validation (random UUIDs) - using static factory
 * $uuidV4Value = UuidValue::v4();
 * $userFilter = new Equals('user_id', $uuidV4Value);
 * $result = $userFilter->withValue('550e8400-e29b-41d4-a716-446655440000'); // Valid v4 UUID
 * $result = $userFilter->withValue('550e8400-e29b-11d4-a716-446655440000'); // Invalid - not v4
 * ```
 * ```
 * // API resource ID validation - using static factory
 * $apiUuidValue = UuidValue::v4(); // Most APIs use v4
 * $apiFilter = new Equals('resource_id', $apiUuidValue);
 * // GET /api/users/550e8400-e29b-41d4-a716-446655440000
 * $result = $apiFilter->withValue($_GET['id']);
 * ```
 * ```
 * // Database record lookup - using static factory
 * $dbUuidValue = UuidValue::valid(); // Any valid UUID
 * $recordFilter = new InArray('record_ids', new ArrayValue($dbUuidValue));
 * $result = $recordFilter->withValue([
 *     '550e8400-e29b-41d4-a716-446655440000',
 *     '6ba7b810-9dad-11d1-80b4-00c04fd430c8'
 * ]); // Multiple UUID lookups
 * ```
 * ```
 * // Session ID validation - using static factory
 * $sessionUuidValue = UuidValue::v4();
 * $sessionFilter = new Equals('session_id', $sessionUuidValue);
 * $result = $sessionFilter->withValue($_COOKIE['session_id']);
 * ```
 * ```
 * // NIL UUID validation (special case) - using static factory
 * $nilUuidValue = UuidValue::nil();
 * $nilFilter = new Equals('null_reference', $nilUuidValue);
 * $result = $nilFilter->withValue('00000000-0000-0000-0000-000000000000'); // Valid NIL UUID
 * $result = $nilFilter->withValue('550e8400-e29b-41d4-a716-446655440000'); // Invalid - not NIL
 * ```
 * ```
 * // File identifier validation - using static factory
 * $fileUuidValue = UuidValue::valid();
 * $fileFilter = new Equals('file_id', $fileUuidValue);
 * $result = $fileFilter->withValue('123e4567-e89b-12d3-a456-426614174000'); // Valid file ID
 * ```
 * ```
 * // Transaction ID validation (financial systems) - using static factory
 * $transactionUuidValue = UuidValue::v4();
 * $transactionFilter = new Equals('transaction_id', $transactionUuidValue);
 * $result = $transactionFilter->withValue('f47ac10b-58cc-4372-a567-0e02b2c3d479');
 * ```
 * ```
 * // Static factory methods for different UUID versions
 * $anyUuidValue = UuidValue::valid();          // Any valid UUID
 * $v1UuidValue = UuidValue::v1();              // Version 1 only
 * $v2UuidValue = UuidValue::v2();              // Version 2 only
 * $v3UuidValue = UuidValue::v3();              // Version 3 only
 * $v4UuidValue = UuidValue::v4();              // Version 4 only
 * $v5UuidValue = UuidValue::v5();              // Version 5 only
 * $nilUuidValue = UuidValue::nil();            // NIL UUID only
 *
 * // Valid UUID v4
 * $uuid_v4 = '550e8400-e29b-41d4-a716-446655440000';
 * $anyUuidValue->accepts($uuid_v4);     // true - valid format
 * $v4UuidValue->accepts($uuid_v4);      // true - correct version
 * $v1UuidValue->accepts($uuid_v4);      // false - wrong version
 *
 * // NIL UUID
 * $nil_uuid = '00000000-0000-0000-0000-000000000000';
 * $anyUuidValue->accepts($nil_uuid);    // true - valid format
 * $nilUuidValue->accepts($nil_uuid);    // true - correct NIL format
 * $v4UuidValue->accepts($nil_uuid);     // false - not v4
 * ```
 *
 * Available UUID masks (via static factories):
 * - `UuidValue::valid()`: Any valid UUID format (default)
 * - `UuidValue::v1()`: Version 1 UUIDs (timestamp + MAC)
 * - `UuidValue::v2()`: Version 2 UUIDs (DCE security)
 * - `UuidValue::v3()`: Version 3 UUIDs (namespace + MD5)
 * - `UuidValue::v4()`: Version 4 UUIDs (random/pseudo-random)
 * - `UuidValue::v5()`: Version 5 UUIDs (namespace + SHA-1)
 * - `UuidValue::nil()`: Special NIL UUID (all zeros)
 *
 * @see https://github.com/particle-php/Validator/blob/master/src/Rule/Uuid.php
 */
final class UuidValue implements ValueInterface
{
    /**
     * Compare masks.
     */
    public const VALID = 'valid';

    public const NIL = 'nil';
    public const V1 = 'v1';
    public const V2 = 'v2';
    public const V3 = 'v3';
    public const V4 = 'v4';
    public const V5 = 'v5';

    /**
     * An array of all validation regex patterns.
     */
    private const PATTERNS = [
        self::VALID => '~^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$~i',
        self::V1 => '~^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::V2 => '~^[0-9a-f]{8}-[0-9a-f]{4}-2[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::V3 => '~^[0-9a-f]{8}-[0-9a-f]{4}-3[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::V4 => '~^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
        self::V5 => '~^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$~i',
    ];

    /**
     * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.7
     */
    private const NIL_VALUE = '00000000-0000-0000-0000-000000000000';

    private readonly string $mask;
    private readonly RegexValue $regex;

    public function __construct(string $mask = self::VALID)
    {
        $this->mask = \strtolower($mask);

        if ($this->mask !== self::NIL && !isset(self::PATTERNS[$this->mask])) {
            throw new ValueException('Invalid UUID version mask given. Please choose one of the constants.');
        }

        $this->regex = new RegexValue(self::PATTERNS[$this->mask !== self::NIL ? $this->mask : self::VALID]);
    }

    /**
     * Creates a UUID validator that accepts any valid UUID format.
     *
     * This is the most permissive validator and accepts UUIDs of any version
     * as long as they follow the basic UUID structure.
     */
    public static function valid(): self
    {
        return new self(self::VALID);
    }

    /**
     * Creates a UUID validator specifically for version 1 UUIDs.
     *
     * Version 1 UUIDs are based on timestamp and MAC address.
     * They contain the time and network address of the generating machine.
     */
    public static function v1(): self
    {
        return new self(self::V1);
    }

    /**
     * Creates a UUID validator specifically for version 2 UUIDs.
     *
     * Version 2 UUIDs are DCE (Distributed Computing Environment) security UUIDs.
     * They are similar to v1 but include POSIX UID/GID information.
     */
    public static function v2(): self
    {
        return new self(self::V2);
    }

    /**
     * Creates a UUID validator specifically for version 3 UUIDs.
     *
     * Version 3 UUIDs are namespace-based using MD5 hashing.
     * They are deterministic - same namespace and name always produce the same UUID.
     */
    public static function v3(): self
    {
        return new self(self::V3);
    }

    /**
     * Creates a UUID validator specifically for version 4 UUIDs.
     *
     * Version 4 UUIDs are randomly or pseudo-randomly generated.
     * This is the most commonly used UUID version in modern applications.
     */
    public static function v4(): self
    {
        return new self(self::V4);
    }

    /**
     * Creates a UUID validator specifically for version 5 UUIDs.
     *
     * Version 5 UUIDs are namespace-based using SHA-1 hashing.
     * They are deterministic like v3 but use SHA-1 instead of MD5.
     */
    public static function v5(): self
    {
        return new self(self::V5);
    }

    /**
     * Creates a UUID validator specifically for the NIL UUID.
     *
     * The NIL UUID is a special UUID consisting of all zeros:
     * '00000000-0000-0000-0000-000000000000'
     *
     * It represents the absence of a UUID value and is useful for
     * null reference patterns in systems that require UUID format.
     */
    public static function nil(): self
    {
        return new self(self::NIL);
    }

    public function accepts(mixed $value): bool
    {
        return (\is_numeric($value) || \is_string($value)) && $this->isValid($this->convert($value));
    }

    public function convert(mixed $value): string
    {
        return (string) $value;
    }

    private function isValid(string $value): bool
    {
        $uuid = \str_replace(['urn:', 'uuid:', '{', '}'], '', $value);

        if ($this->mask === self::NIL) {
            return $value === self::NIL_VALUE;
        }

        return $this->regex->accepts($uuid);
    }
}
