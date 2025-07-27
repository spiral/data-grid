<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Filter\Postgres;

use Spiral\DataGrid\Specification\Filter\Like;
use Spiral\DataGrid\Specification\FilterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * PostgreSQL-specific case-insensitive LIKE filter using ILIKE operator.
 * This is functionally equivalent to Like but specifically designed for PostgreSQL databases
 * that support the ILIKE operator for case-insensitive pattern matching.
 *
 * Comparison with regular Like:
 * - Like: 'iPhone' != 'iphone' != 'IPHONE' (case-sensitive, database dependent)
 * - ILike: 'iPhone' == 'iphone' == 'IPHONE' (always case-insensitive in PostgreSQL)
 *
 * ```
 * // Case-insensitive user search
 * $nameFilter = new ILike('name', new StringValue());
 * $result = $nameFilter->withValue('john'); // Matches "John", "JOHN", "john", "JoHn"
 * ```
 *
 * ```
 * // Case-insensitive email search
 * $emailFilter = new ILike('email', new StringValue(), '%%%s');
 * $result = $emailFilter->withValue('&#64;GMAIL.COM'); // Matches emails ending with &#64;gmail.com
 * ```
 * ```
 * // Product name search (case-insensitive)
 * $productFilter = new ILike('product_name', new StringValue());
 * $result = $productFilter->withValue('iphone'); // Matches "iPhone", "IPHONE", "IPhone"
 * ```
 * ```
 * // Case-insensitive starts-with search
 * $titleFilter = new ILike('title', new StringValue(), '%s%%');
 * $result = $titleFilter->withValue('how to'); // Matches "How To", "HOW TO", "how to"
 * ```
 * ```
 * // Company/brand search
 * $companyFilter = new ILike('company_name', new StringValue());
 * $result = $companyFilter->withValue('microsoft'); // Matches "Microsoft", "MICROSOFT"
 * ```
 * ```
 * // Tag-based content search
 * $tagFilter = new ILike('tags', new StringValue());
 * $result = $tagFilter->withValue('PHP'); // Matches "php", "PHP", "Php"
 * ```
 * ```
 * // Address search (case-insensitive)
 * $addressFilter = new ILike('address', new StringValue());
 * $result = $addressFilter->withValue('main street'); // Matches "Main Street", "MAIN STREET"
 * ```
 * ```
 * // Fixed pattern case-insensitive search
 * $codeFilter = new ILike('product_code', 'abc', 'SKU-%s-%%');
 * // Matches "SKU-ABC-001", "SKU-abc-premium", "SKU-Abc-special"
 *```
 *
 * Note: This filter is specifically optimized for PostgreSQL databases.
 * For other databases, consider using regular Like with appropriate database-specific
 * case-insensitive configuration or functions.
 */
final class ILike implements FilterInterface
{
    private Like $like;

    /**
     * @param string $expression The field name to filter on
     * @param mixed $value Either fixed value or ValueInterface for dynamic input (defaults to StringValue)
     * @param string $pattern The pattern template where %s is replaced with the search value
     */
    public function __construct(string $expression, mixed $value = null, string $pattern = '%%%s%%')
    {
        $this->like = new Like($expression, $value, $pattern);
    }

    public function withValue(mixed $value): ?SpecificationInterface
    {
        $filter = clone $this;
        $filter->like = $filter->like->withValue($value);

        return $filter;
    }

    public function getExpression(): string
    {
        return $this->like->getExpression();
    }

    public function getPattern(): string
    {
        return $this->like->getPattern();
    }

    public function getValue(): mixed
    {
        return $this->like->getValue();
    }
}
