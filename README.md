# Data Grid specification builder

[![PHP Version Require](https://poser.pugx.org/spiral/data-grid/require/php)](https://packagist.org/packages/spiral/data-grid)
[![Latest Stable Version](https://poser.pugx.org/spiral/data-grid/v/stable)](https://packagist.org/packages/spiral/data-grid)
[![phpunit](https://github.com/spiral/data-grid/actions/workflows/phpunit.yml/badge.svg)](https://github.com/spiral/data-grid/actions)
[![psalm](https://github.com/spiral/data-grid/actions/workflows/static-analysis.yml/badge.svg)](https://github.com/spiral/data-grid/actions)
[![Codecov](https://codecov.io/gh/spiral/data-grid/branch/master/graph/badge.svg)](https://codecov.io/gh/spiral/data-grid)
[![Total Downloads](https://poser.pugx.org/spiral/data-grid/downloads)](https://packagist.org/packages/spiral/data-grid)
[![type-coverage](https://shepherd.dev/github/spiral/data-grid/coverage.svg)](https://shepherd.dev/github/spiral/data-grid)
[![psalm-level](https://shepherd.dev/github/spiral/data-grid/level.svg)](https://shepherd.dev/github/spiral/data-grid)

<b>[Documentation](https://spiral.dev/docs/component-data-grid)</b> | [Framework Bundle](https://github.com/spiral/framework)

## What is Data Grid?

**Data Grid** is a PHP component that acts as an intelligent data query abstraction layer. It
automatically converts user interface specifications (filters, sorting, pagination) into database queries or
data transformations

Think of it as a smart translator that sits between your user interface and your data sources. You define once
what operations users are allowed to perform on your data (what fields they can filter, how they can sort, pagination
limits), and the component handles all the complex logic of:

- Input validation and security
- Query optimization
- Multi-source compatibility - Working with databases, arrays, APIs, or custom data sources
- Result formatting - Providing consistent pagination and metadata.

Let's imagine you're building an **E-commerce website** where customers need to find products among thousands of items

**Your users want to:**

- **Filter products** by price range ($50-$200), category (Electronics), brand (Apple)
- **Sort results** by popularity, price (low to high), or newest arrivals
- **Navigate pages** - show 20 products per page instead of overwhelming them with 10,000 items at once

```
GET: /api/products?min_price=50&max_price=200&category=Electronics&sort_by=popularity&sort_direction=desc&page=2&limit=20
```

**Without Data Grid**, you'd need to write repetitive code for every single page AND manually process all user input in
your controllers:

```php
// Products Controller - Manual input processing nightmare
public function products(Request $request) 
{
    // 1. Manual input validation and sanitization
    $filters = [];
    $sorts = [];
    $page = 1;
    $limit = 20;
    
    // Process price filter
    if ($request->has('min_price')) {
        $minPrice = $request->get('min_price');
        if (!is_numeric($minPrice) || $minPrice < 0) {
            throw new ValidationException('Invalid minimum price');
        }
        $filters['min_price'] = (float)$minPrice;
    }
    
    if ($request->has('max_price')) {
        $maxPrice = $request->get('max_price');
        if (!is_numeric($maxPrice) || $maxPrice < 0) {
            throw new ValidationException('Invalid maximum price');
        }
        $filters['max_price'] = (float)$maxPrice;
    }
    
    // Process category filter
    if ($request->has('category')) {
        $category = trim($request->get('category'));
        $allowedCategories = ['Electronics', 'Clothing', 'Books', 'Sports'];
        if (!in_array($category, $allowedCategories)) {
            throw new ValidationException('Invalid category');
        }
        $filters['category'] = $category;
    }
    
    // Process name search
    if ($request->has('search')) {
        $search = trim($request->get('search'));
        if (strlen($search) < 2) {
            throw new ValidationException('Search term too short');
        }
        $filters['search'] = $search;
    }
    
    // Process sorting
    if ($request->has('sort_by')) {
        $sortBy = $request->get('sort_by');
        $allowedSorts = ['price', 'name', 'created_at', 'popularity_score'];
        if (!in_array($sortBy, $allowedSorts)) {
            throw new ValidationException('Invalid sort field');
        }
        $sorts['field'] = $sortBy;
        
        $sortDirection = $request->get('sort_direction', 'asc');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            throw new ValidationException('Invalid sort direction');
        }
        $sorts['direction'] = $sortDirection;
    }
    
    // Process pagination
    if ($request->has('page')) {
        $page = (int)$request->get('page');
        if ($page < 1) {$page = 1;}
    }
    
    if ($request->has('limit')) {
        $limit = (int)$request->get('limit');
        $allowedLimits = [10, 20, 50, 100];
        if (!in_array($limit, $allowedLimits)) {
            $limit = 20; // default
        }
    }
    
    // 2. Manual query building with Laravel Query Builder
    $query = Product::query();
    
    // Apply price filters
    if (isset($filters['min_price'])) {
        $query->where('price', '>=', $filters['min_price']);
    }
    
    if (isset($filters['max_price'])) {
        $query->where('price', '<=', $filters['max_price']);
    }
    
    // Apply category filter
    if (isset($filters['category'])) {
        $query->where('category', $filters['category']);
    }
    
    // Apply search filter
    if (isset($filters['search'])) {
        $query->where('name', 'LIKE', '%' . $filters['search'] . '%');
    }
    
    // Get total count BEFORE applying pagination (separate query)
    $total = $query->count();
    
    // Apply sorting
    if (!empty($sorts)) {
        $query->orderBy($sorts['field'], $sorts['direction']);
    }
    
    // Apply pagination
    $offset = ($page - 1) * $limit;
    $query->skip($offset)->take($limit);
    
    // Execute query
    $products = $query->get();
    
    return [
        'products' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => ceil($total / $limit),
        ],
        'applied_filters' => $filters,
        'applied_sorts' => $sorts,
    ];
}
```

## How Data Grid Solves This

It acts as a **smart translator** between user requests and your data sources. Instead of writing
repetitive query code, you define **what's allowed once** and the component handles the rest automatically.

### Schema-Driven Approach

```
GET /api/products?filter[price]=100,500&filter[category]=Smartphones&sort[rating]=desc&paginate[page]=2&paginate[limit]=20
```

```php
// Define ONCE what users can do with product data
class ProductSchema extends GridSchema 
{
    public function __construct() 
    {
        // Allow filtering by these fields
        $this->addFilter('price', new Between('price', new NumericValue()));
        $this->addFilter('category', new Equals('category', new StringValue()));
        $this->addFilter('name', new Like('name', new StringValue()));
        
        // Allow sorting by these fields  
        $this->addSorter('price', new Sorter('price'));
        $this->addSorter('popularity', new Sorter('popularity_score'));
        
        // Set pagination rules
        $this->setPaginator(new PagePaginator(20, [10, 20, 50, 100]));
    }
}
```

**The schema serves as:**

- Security gateway - Only defined operations are allowed
- Validation rules - Input types and ranges are enforced
- Business logic - Encodes what users can do with your data
- Configuration - Reusable across multiple interfaces

Now **any interface** (web page, mobile app, API) can use this schema:

```php
// Controller - same code works for web, mobile, API
public function products(ProductSchema $schema, GridFactoryInterface $factory, ProductRepository $products): array 
{
    // User input: ?filter[price]=50,200&sort[popularity]=desc&paginate[page]=2
    $grid = $factory->create($products->select(), $schema);
    
    return [
        'products' => iterator_to_array($grid),      // [Product objects]
        'total' => $grid->getOption(GridInterface::COUNT),     // 1,247 total items
        'page' => $grid->getOption(GridInterface::PAGINATOR),  // Current page info
        'filters' => $grid->getOption(GridInterface::FILTERS), // Applied filters
    ];
}
```

## How Data Grid Works

1. User makes a request with filters, sorting, or pagination parameters
2. Grid Schema validates the request against predefined rules
3. Input Processor sanitizes and converts user input safely
4. Compiler Engine determines the best way to fulfill the request
5. Writer generates the appropriate query (SQL, API call, array operation)
6. Data Source executes the operation and returns raw results
7. Grid View formats results with pagination metadata and applied filters

## Getting Started

1. **Install the component:**

```bash
composer require spiral/data-grid-bridge spiral/cycle-bridge
```

2. **Define your first schema:**

```php
class UserSchema extends GridSchema {
    public function __construct() {
        $this->addFilter('name', new Like('name', new StringValue()));
        $this->addSorter('created_at', new Sorter('created_at'));
        $this->setPaginator(new PagePaginator(25));
    }
}
```

3. **Use in your controller:**

```php
public function users(UserSchema $schema, GridFactoryInterface $factory, UserRepository $users) {
    $grid = $factory->create($users->select(), $schema);
    return ['users' => iterator_to_array($grid)];
}
```

4. **Frontend integration:**

```html
<!-- User can now filter and sort -->
<form>
    <input name="filter[name]" placeholder="Search users...">
    <select name="sort[created_at]">
        <option value="desc">Newest first</option>
        <option value="asc">Oldest first</option>
    </select>
</form>
```

## License:

MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).