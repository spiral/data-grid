<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field exactly matches the given value.
 *
 * Real-world usage examples:
 * - User filtering: Find users with specific email, role, or status
 * - Product filtering: Find products with exact category, brand, or SKU
 * - Order filtering: Find orders with specific status, payment method, or customer ID
 * - Content filtering: Find articles by exact author, category, or publish status
 * - Inventory management: Find items with exact location, supplier, or condition
 * - System filtering: Find logs with specific level, module, or error code
 *
 * @example
 * // Find users with specific role
 * $roleFilter = new Equals('role', 'admin');
 *
 * @example
 * // Dynamic category filtering
 * $categoryFilter = new Equals('category', new StringValue());
 * $result = $categoryFilter->withValue('electronics');
 *
 * @example
 * // Order status filtering
 * $statusFilter = new Equals('status', new EnumValue(
 *     new StringValue(), 'pending', 'processing', 'shipped', 'delivered'
 * ));
 * $result = $statusFilter->withValue('shipped');
 *
 * @example
 * // Boolean flag filtering
 * $activeFilter = new Equals('is_active', new BoolValue());
 * $result = $activeFilter->withValue(true);
 *
 * @example
 * // Numeric ID filtering
 * $userFilter = new Equals('user_id', new IntValue());
 * $result = $userFilter->withValue(123);
 */
final class Equals extends Expression {}
