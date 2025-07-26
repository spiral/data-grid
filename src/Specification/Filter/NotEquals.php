<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter;

/**
 * Filters records where a field value does NOT exactly match the given value.
 *
 * Real-world usage examples:
 * - Exclude specific statuses: Find orders that are NOT 'cancelled'
 * - Active user filtering: Find users where status != 'deleted' or 'banned'
 * - Content filtering: Find articles that are NOT 'draft' status
 * - Product filtering: Find products that are NOT 'discontinued'
 * - Role exclusion: Find users who are NOT 'guest' or 'suspended'
 * - Data cleanup: Find records that are NOT null or empty
 * - Error filtering: Find logs that are NOT 'info' level (errors and warnings)
 * - Inventory: Find products that are NOT 'out of stock'
 *
 * @example
 * // Exclude cancelled orders
 * $statusFilter = new NotEquals('status', 'cancelled');
 *
 * @example
 * // Find active users (not deleted)
 * $userFilter = new NotEquals('status', new EnumValue(
 *     new StringValue(), 'deleted', 'banned', 'suspended'
 * ));
 * $result = $userFilter->withValue('deleted'); // Users who are NOT deleted
 *
 * @example
 * // Exclude draft content
 * $contentFilter = new NotEquals('publish_status', new StringValue());
 * $result = $contentFilter->withValue('draft'); // Published content (not draft)
 *
 * @example
 * // Find available products
 * $productFilter = new NotEquals('availability', 'out_of_stock');
 *
 * @example
 * // Exclude specific user role
 * $roleFilter = new NotEquals('role', new StringValue());
 * $result = $roleFilter->withValue('guest'); // All users except guests
 *
 * @example
 * // Find non-null values
 * $dataFilter = new NotEquals('email', null);
 *
 * @example
 * // Error/warning log filtering (exclude info)
 * $logFilter = new NotEquals('log_level', 'info');
 *
 * @example
 * // Exclude specific category
 * $categoryFilter = new NotEquals('category', new StringValue());
 * $result = $categoryFilter->withValue('archived'); // Non-archived items
 */
final class NotEquals extends Expression {}
