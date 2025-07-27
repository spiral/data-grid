<?php

declare(strict_types=1);

namespace Spiral\DataGrid\Specification\Sorter;

use Spiral\DataGrid\Specification\SorterInterface;
use Spiral\DataGrid\SpecificationInterface;

/**
 * Standard sorter that allows user-controlled direction on the same set of fields.
 * This is the most commonly used sorter - it uses the same fields for both
 * ascending and descending directions, letting users control the sort direction.
 *
 * ```
 * // Product price sorting (user can choose direction)
 * $priceSort = new Sorter('price');
 * $lowToHigh = $priceSort->withDirection('asc');   // $10, $20, $30...
 * $highToLow = $priceSort->withDirection('desc');  // $30, $20, $10...
 * ```
 * ```
 * // User name sorting (both directions on same fields)
 * $nameSort = new Sorter('last_name', 'first_name');
 * $aToZ = $nameSort->withDirection('asc');   // Adams, Baker, Charlie...
 * $zToA = $nameSort->withDirection('desc');  // Zulu, Young, Xavier...
 * ```
 * ```
 * // Date sorting (user controls chronological direction)
 * $dateSort = new Sorter('created_at');
 * $oldestFirst = $dateSort->withDirection('asc');   // 2020, 2021, 2022...
 * $newestFirst = $dateSort->withDirection('desc');  // 2022, 2021, 2020...
 * ```
 * ```
 * // Multi-field sorting with user direction control
 * $userSort = new Sorter('department', 'salary', 'hire_date');
 * $ascending = $userSort->withDirection('asc');     // All fields ascending
 * $descending = $userSort->withDirection('desc');   // All fields descending
 * ```
 * ```
 * // E-commerce product sorting
 * $productSort = new Sorter('popularity_score', 'name');
 * $popular = $productSort->withDirection('desc');   // Most popular first
 * $unpopular = $productSort->withDirection('asc');  // Least popular first
 * ```
 * ```
 * // Content management sorting
 * $articleSort = new Sorter('published_at', 'title');
 * $recent = $articleSort->withDirection('desc');    // Latest articles first
 * $oldest = $articleSort->withDirection('asc');     // Oldest articles first
 * ```
 * ```
 * // Usage in grid schema
 * $schema->addSorter('price', new Sorter('price'));
 * $schema->addSorter('name', new Sorter('name'));
 * $schema->addSorter('date', new Sorter('created_at'));
 *
 * // Users can then request:
 * // ?sort[price]=asc     (cheapest first)
 * // ?sort[price]=desc    (most expensive first)
 * // ?sort[name]=asc      (A-Z)
 * // ?sort[name]=desc     (Z-A)
 * ```
 * ```
 * // API endpoint sorting
 * // GET /api/products?sort[price]=desc&sort[rating]=asc
 * $priceSort = new Sorter('price');
 * $ratingSort = new Sorter('rating');
 * ```
 * ```
 * // Table column sorting (typical web interface)
 * $columns = [
 *     'name' => new Sorter('name'),
 *     'email' => new Sorter('email'),
 *     'created' => new Sorter('created_at'),
 *     'status' => new Sorter('status')
 * ];
 * // Users click column headers to toggle sort direction
 * ```
 * ```
 * // Performance metrics sorting
 * $metricsSort = new Sorter('response_time_ms', 'throughput_rps');
 * $fastest = $metricsSort->withDirection('asc');    // Fastest response times first
 * $slowest = $metricsSort->withDirection('desc');   // Slowest response times first
 * ```
 */
final class Sorter implements SorterInterface
{
    private readonly DirectionalSorter $sorter;

    /**
     * @param string ...$expressions Field names to sort by (same fields used for both directions)
     */
    public function __construct(string ...$expressions)
    {
        $this->sorter = new DirectionalSorter(new AscSorter(...$expressions), new DescSorter(...$expressions));
    }

    public function withDirection(int|string $direction): ?SpecificationInterface
    {
        $sorter = clone $this;

        return $sorter->sorter->withDirection($direction);
    }

    public function getValue(): ?string
    {
        return $this->sorter->getValue();
    }
}
