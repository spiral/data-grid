<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Tests\DataGrid;

use PHPUnit\Framework\TestCase;
use Spiral\Database\Database;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Spiral\DataGrid\Compiler;
use Spiral\DataGrid\GridGenerator;
use Spiral\DataGrid\GridSchema;
use Spiral\DataGrid\GridView;
use Spiral\DataGrid\Input\ArrayInput;
use Spiral\DataGrid\Specification\Pagination\PagePaginator;
use Spiral\DataGrid\Specification\Sorter\Sorter;
use Spiral\DataGrid\Writer\QueryWriter;

class GeneratorTest extends TestCase
{
    /** @var Database */
    private $db;

    public function setUp(): void
    {
        parent::setUp();

        $this->db = $this->initDB();

        $users = $this->db->table('users')->getSchema();
        $users->primary('id');
        $users->enum('status', ['active', 'disabled']);
        $users->string('name');
        $users->save();

        $this->db->table('users')->insertMultiple(['status', 'name'], [
            ['active', 'Antony'],
            ['active', 'John'],
            ['disabled', 'Bob'],
        ]);
    }

    public function testSelect(): void
    {
        $this->assertCount(3, $this->db->table('users'));
    }

    public function testBasePaginate(): void
    {
        $schema = new GridSchema();
        $schema->setPaginator(new PagePaginator(1));

        $view = $this->initGenerator()
            ->withInput(new ArrayInput([]))
            ->generate(
                $this->db->table('users')->select('*'),
                $schema
            );

        $this->assertEquals([
            [
                'id'     => 1,
                'status' => 'active',
                'name'   => 'Antony'
            ]
        ], iterator_to_array($view));

        $this->assertNull($view->getOption(GridView::COUNT));

        $this->assertSame([
            'limit' => 1,
            'page'  => 1
        ], $view->getOption(GridView::PAGINATOR));
    }

    public function testPaginateWithCount(): void
    {
        $schema = new GridSchema();
        $schema->setPaginator(new PagePaginator(1));

        $view = $this
            ->initGenerator()
            ->withInput(new ArrayInput([
                GridGenerator::KEY_PAGINATE    => ['page' => 2],
                GridGenerator::KEY_FETCH_COUNT => true
            ]))
            ->generate(
                $this->db->table('users')->select('*'),
                $schema
            );

        $this->assertEquals([
            [
                'id'     => 2,
                'status' => 'active',
                'name'   => 'John'
            ]
        ], iterator_to_array($view));

        $this->assertSame(3, $view->getOption(GridView::COUNT));

        $this->assertSame([
            'limit' => 1,
            'page'  => 2
        ], $view->getOption(GridView::PAGINATOR));
    }

    public function testDefaultWithMapping(): void
    {
        $schema = new GridSchema();
        $schema->setPaginator(new PagePaginator(1));

        $view = $this
            ->initGenerator()
            ->withInput(new ArrayInput([
                GridGenerator::KEY_PAGINATE => ['page' => 2]
            ]))
            ->withDefault(new ArrayInput([
                GridGenerator::KEY_FETCH_COUNT => true
            ]))
            ->generate(
                $this->db->table('users')->select('*'),
                $schema
            )
            ->withMapper(static function ($u) {
                return $u['name'];
            });

        $this->assertEquals([
            'John'
        ], iterator_to_array($view));

        $this->assertSame(3, $view->getOption(GridView::COUNT));

        $this->assertSame([
            'limit' => 1,
            'page'  => 2
        ], $view->getOption(GridView::PAGINATOR));
    }

    public function testSort(): void
    {
        $schema = new GridSchema();
        $schema->addSorter('id', new Sorter('id'));

        $view = $this
            ->initGenerator()
            ->withDefault(new ArrayInput([
                GridGenerator::KEY_SORT => ['id' => 'desc']
            ]))
            ->generate(
                $this->db->table('users')->select('*'),
                $schema
            )
            ->withMapper(static function ($u) {
                return $u['name'];
            });

        $this->assertEquals([
            'Bob',
            'John',
            'Antony'
        ], iterator_to_array($view));

        $this->assertSame([
            'id' => 'desc'
        ], $view->getOption(GridView::SORTERS));
    }

    public function testSortAsc(): void
    {
        $schema = new GridSchema();
        $schema->addSorter('id', new Sorter('id'));

        $view = $this
            ->initGenerator()
            ->withDefault(new ArrayInput([
                GridGenerator::KEY_SORT => ['id' => 1]
            ]))
            ->generate(
                $this->db->table('users')->select('*'),
                $schema
            )
            ->withMapper(static function ($u) {
                return $u['name'];
            });

        $this->assertEquals([
            'Antony',
            'John',
            'Bob',
        ], iterator_to_array($view));

        $this->assertSame([
            'id' => 'asc'
        ], $view->getOption(GridView::SORTERS));
    }

    public function testSortUnknown(): void
    {
        $schema = new GridSchema();
        $schema->addSorter('id', new Sorter('id'));

        $view = $this
            ->initGenerator()
            ->withDefault(new ArrayInput([
                GridGenerator::KEY_SORT => ['id' => 2]
            ]))
            ->generate(
                $this->db->table('users')->select('*'),
                $schema
            )
            ->withMapper(static function ($u) {
                return $u['name'];
            });

        $this->assertSame([], $view->getOption(GridView::SORTERS));
    }

    /**
     * @return Compiler
     */
    public function initCompiler(): Compiler
    {
        $compiler = new Compiler();
        $compiler->addWriter(new QueryWriter());

        return $compiler;
    }

    /**
     * @return Database
     */
    private function initDB(): Database
    {
        return new Database('default', '', new SQLiteDriver(['connection' => 'sqlite::memory:']));
    }

    /**
     * @return GridGenerator
     */
    private function initGenerator(): GridGenerator
    {
        return new GridGenerator($this->initCompiler());
    }
}
