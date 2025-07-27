<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value does NOT exactly match the given value.
 *
 * ```
 * // Exclude cancelled orders
 * $statusFilter = new NotEquals('status', 'cancelled');
 * ```
 * ```
 * // Find active users (not deleted)
 * $userFilter = new NotEquals('status', new EnumValue(
 *     new StringValue(), 'deleted', 'banned', 'suspended'
 * ));
 * $result = $userFilter->withValue('deleted'); // Users who are NOT deleted
 * ```
 * ```
 * // Exclude draft content
 * $contentFilter = new NotEquals('publish_status', new StringValue());
 * $result = $contentFilter->withValue('draft'); // Published content (not draft)
 * ```
 * ```
 * // Find available products
 * $productFilter = new NotEquals('availability', 'out_of_stock');
 * ```
 * ```
 * // Exclude specific user role
 * $roleFilter = new NotEquals('role', new StringValue());
 * $result = $roleFilter->withValue('guest'); // All users except guests
 * ```
 * ```
 * // Find non-null values
 * $dataFilter = new NotEquals('email', null);
 * ```
 * ```
 * // Error/warning log filtering (exclude info)
 * $logFilter = new NotEquals('log_level', 'info');
 * ```
 * ```
 * // Exclude specific category
 * $categoryFilter = new NotEquals('category', new StringValue());
 * $result = $categoryFilter->withValue('archived'); // Non-archived items
 * ```
 */
final class NotEquals extends Expression {}
