<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field exactly matches the given value.
 *
 * ```
 * // Find users with specific role
 * $roleFilter = new Equals('role', 'admin');
 * ```
 * ```
 * // Dynamic category filtering
 * $categoryFilter = new Equals('category', new StringValue());
 * $result = $categoryFilter->withValue('electronics');
 * ```
 * ```
 * // Order status filtering
 * $statusFilter = new Equals('status', new EnumValue(
 *     new StringValue(), 'pending', 'processing', 'shipped', 'delivered'
 * ));
 * $result = $statusFilter->withValue('shipped');
 * ```
 * ```
 * // Boolean flag filtering
 * $activeFilter = new Equals('is_active', new BoolValue());
 * $result = $activeFilter->withValue(true);
 * ```
 * ```
 * // Numeric ID filtering
 * $userFilter = new Equals('user_id', new IntValue());
 * $result = $userFilter->withValue(123);
 * ```
 */
final class Equals extends Expression {}
