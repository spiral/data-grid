<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Value;

use Spiral\DataGrid\Exception\ValueException;
use Spiral\DataGrid\Specification\ValueInterface;

/**
 * UUID strings value with mask validation.
 * Validates UUID format and optionally enforces specific UUID versions or the special NIL UUID.
 *
 * Real-world usage examples:
 * - Database primary keys: UUIDs used as record identifiers
 * - API resource identifiers: RESTful APIs using UUID-based resource IDs
 * - Distributed systems: Unique identifiers across multiple services
 * - Session management: Session tokens in UUID format
 * - File identifiers: Unique file or document IDs
 * - Transaction tracking: Financial transaction identifiers
 * - User account IDs: User identification in microservices
 * - Message queuing: Message IDs in distributed messaging systems
 *
 * UUID versions supported:
 * - v1: Timestamp + MAC address based
 * - v2: DCE security UUIDs
 * - v3: Namespace + MD5 hash based
 * - v4: Random/pseudo-random generated (most common)
 * - v5: Namespace + SHA-1 hash based
 * - NIL: Special all-zeros UUID (00000000-0000-0000-0000-000000000000)
 * - VALID: Any valid UUID format (default)
 *
 * @example
 * // General UUID validation (any valid UUID)
 * $uuidValue = new UuidValue();
 * $recordFilter = new Equals('id', $uuidValue);
 * $result = $recordFilter->withValue('550e8400-e29b-41d4-a716-446655440000'); // Valid UUID
 * $result = $recordFilter->withValue('invalid-uuid-format');                  // Invalid format
 *
 * @example
 * // UUID v4 validation (random UUIDs)
 * $uuidV4Value = new UuidValue('v4');
 * $userFilter = new Equals('user_id', $uuidV4Value);
 * $result = $userFilter->withValue('550e8400-e29b-41d4-a716-446655440000'); // Valid v4 UUID
 * $result = $userFilter->withValue('550e8400-e29b-11d4-a716-446655440000'); // Invalid - not v4
 *
 * @example
 * // API resource ID validation
 * $apiUuidValue = new UuidValue('v4'); // Most APIs use v4
 * $apiFilter = new Equals('resource_id', $apiUuidValue);
 * // GET /api/users/550e8400-e29b-41d4-a716-446655440000
 * $result = $apiFilter->withValue($_GET['id']);
 *
 * @example
 * // Database record lookup
 * $dbUuidValue = new UuidValue();
 * $recordFilter = new InArray('record_ids', new ArrayValue($dbUuidValue));
 * $result = $recordFilter->withValue([
 *     '550e8400-e29b-41d4-a716-446655440000',
 *     '6ba7b810-9dad-11d1-80b4-00c04fd430c8'
 * ]); // Multiple UUID lookups
 *
 * @example
 * // Session ID validation
 * $sessionUuidValue = new UuidValue('v4');
 * $sessionFilter = new Equals('session_id', $sessionUuidValue);
 * $result = $sessionFilter->withValue($_COOKIE['session_id']);
 *
 * @example
 * // NIL UUID validation (special case)
 * $nilUuidValue = new UuidValue('nil');
 * $nilFilter = new Equals('null_reference', $nilUuidValue);
 * $result = $nilFilter->withValue('00000000-0000-0000-0000-000000000000'); // Valid NIL UUID
 * $result = $nilFilter->withValue('550e8400-e29b-41d4-a716-446655440000'); // Invalid - not NIL
 *
 * @example
 * // File identifier validation
 * $fileUuidValue = new UuidValue();
 * $fileFilter = new Equals('file_id', $fileUuidValue);
 * $result = $fileFilter->withValue('123e4567-e89b-12d3-a456-426614174000'); // Valid file ID
 *
 * @example
 * // Transaction ID validation (financial systems)
 * $transactionUuidValue = new UuidValue('v4');
 * $transactionFilter = new Equals('transaction_id', $transactionUuidValue);
 * $result = $transactionFilter->withValue('f47ac10b-58cc-4372-a567-0e02b2c3d479');
 *
 * @example
 * // Validation examples for different UUID versions
 * $anyUuidValue = new UuidValue('valid');          // Any valid UUID
 * $v1UuidValue = new UuidValue('v1');              // Version 1 only
 * $v4UuidValue = new UuidValue('v4');              // Version 4 only
 * $nilUuidValue = new UuidValue('nil');            // NIL UUID only
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
 *
 * @example
 * // Invalid UUID examples
 * $uuidValue = new UuidValue();
 *
 * // Invalid formats
 * $uuidValue->accepts('invalid-uuid');                    // false - wrong format
 * $uuidValue->accepts('550e8400e29b41d4a716446655440000'); // false - no dashes
 * $uuidValue->accepts('550e8400-e29b-41d4-446655440000');  // false - wrong length
 * $uuidValue->accepts('550e8400-e29b-41d4-a716-446655440000-extra'); // false - extra chars
 * $uuidValue->accepts('');                                // false - empty
 * $uuidValue->accepts(123);                               // false - number
 * $uuidValue->accepts([]);                                // false - array
 *
 * @example
 * // Conversion examples (always returns string)
 * $uuidValue = new UuidValue();
 *
 * $result = $uuidValue->convert('550e8400-e29b-41d4-a716-446655440000');
 * // Returns: '550e8400-e29b-41d4-a716-446655440000' (string)
 *
 * $result = $uuidValue->convert(550); // If numeric, converts to string first
 * // Would be validated as string '550' (invalid UUID format)
 *
 * @example
 * // Microservices user ID validation
 * $userUuidValue = new UuidValue('v4');
 * $userService = new All(
 *     new Equals('user_id', $userUuidValue),
 *     new InArray('service_ids', new ArrayValue($userUuidValue))
 * );
 * // Ensures user ID and all service IDs are valid v4 UUIDs
 *
 * @example
 * // Order management system
 * $orderUuidValue = new UuidValue();
 * $orderFilter = new Map([
 *     'order_id' => new Equals('order_id', $orderUuidValue),
 *     'customer_id' => new Equals('customer_id', $orderUuidValue),
 *     'product_ids' => new InArray('product_ids', new ArrayValue($orderUuidValue))
 * ]);
 *
 * @example
 * // Content management with UUID references
 * $contentUuidValue = new UuidValue('v4');
 * $contentFilter = new All(
 *     new Equals('article_id', $contentUuidValue),
 *     new Equals('author_id', $contentUuidValue),
 *     new InArray('category_ids', new ArrayValue($contentUuidValue))
 * );
 *
 * @example
 * // Form validation with UUID input
 * $formUuidValue = new UuidValue();
 * if ($formUuidValue->accepts($_POST['parent_id'])) {
 *     $validParentId = $formUuidValue->convert($_POST['parent_id']);
 *     // Process valid UUID parent reference
 * }
 *
 * @example
 * // API batch operations
 * $batchUuidValue = new UuidValue('v4');
 * $batchFilter = new InArray('item_ids', new ArrayValue($batchUuidValue));
 * // POST /api/items/batch-update
 * // Body: {"item_ids": ["uuid1", "uuid2", "uuid3"]}
 * if ($batchFilter->accepts($requestData['item_ids'])) {
 *     $validIds = $batchFilter->convert($requestData['item_ids']);
 *     // Process batch operation with valid UUIDs
 * }
 *
 * @example
 * // Distributed logging system
 * $logUuidValue = new UuidValue();
 * $logFilter = new Map([
 *     'request_id' => new Equals('request_id', $logUuidValue),      // Trace request
 *     'correlation_id' => new Equals('correlation_id', $logUuidValue), // Correlate logs
 *     'session_id' => new Equals('session_id', $logUuidValue)      // User session
 * ]);
 *
 * @example
 * // File upload system
 * $fileUuidValue = new UuidValue('v4');
 * $uploadFilter = new All(
 *     new Equals('file_id', $fileUuidValue),           // Unique file identifier
 *     new Equals('folder_id', $fileUuidValue)          // Parent folder reference
 * );
 *
 * @example
 * // Social media platform
 * $socialUuidValue = new UuidValue();
 * $socialFilter = new Map([
 *     'post_id' => new Equals('post_id', $socialUuidValue),
 *     'user_id' => new Equals('user_id', $socialUuidValue),
 *     'mentioned_users' => new InArray('mentions', new ArrayValue($socialUuidValue))
 * ]);
 *
 * @example
 * // E-commerce system
 * $ecommerceUuidValue = new UuidValue('v4');
 * $ecommerceFilter = new Map([
 *     'product_id' => new Equals('product_id', $ecommerceUuidValue),
 *     'vendor_id' => new Equals('vendor_id', $ecommerceUuidValue),
 *     'cart_id' => new Equals('cart_id', $ecommerceUuidValue)
 * ]);
 *
 * @example
 * // Error handling and validation
 * try {
 *     $invalidMaskValue = new UuidValue('invalid_mask');
 * } catch (ValueException $e) {
 *     // Handle invalid UUID mask error
 * }
 *
 * $uuidValue = new UuidValue('v4');
 * if (!$uuidValue->accepts($userInput)) {
 *     // Handle invalid UUID format
 *     throw new InvalidArgumentException('Invalid UUID v4 format');
 * }
 *
 * @example
 * // URN format UUID handling
 * $urnUuidValue = new UuidValue();
 * // Handles URN prefixes: 'urn:uuid:550e8400-e29b-41d4-a716-446655440000'
 * $result = $urnUuidValue->accepts('urn:uuid:550e8400-e29b-41d4-a716-446655440000');
 * // Also handles braced format: '{550e8400-e29b-41d4-a716-446655440000}'
 *
 * Available UUID masks:
 * - UuidValue::VALID (default): Any valid UUID format
 * - UuidValue::V1: Version 1 UUIDs (timestamp + MAC)
 * - UuidValue::V2: Version 2 UUIDs (DCE security)
 * - UuidValue::V3: Version 3 UUIDs (namespace + MD5)
 * - UuidValue::V4: Version 4 UUIDs (random/pseudo-random)
 * - UuidValue::V5: Version 5 UUIDs (namespace + SHA-1)
 * - UuidValue::NIL: Special NIL UUID (all zeros)
 *
 * Important notes:
 * - Accepts string and numeric input (converts to string)
 * - Always returns string after conversion
 * - Handles URN format (urn:uuid:...) and braced format ({...})
 * - Version-specific validation available for strict requirements
 * - NIL UUID is a special case (all zeros) with separate validation
 * - Most modern systems use v4 UUIDs (random generation)
 * - Case-insensitive validation (accepts both upper and lowercase)
 * - Invalid mask parameter throws ValueException during construction
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
