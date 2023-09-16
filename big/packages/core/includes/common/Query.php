<?php

require_once ROOT_PATH . 'packages/core/includes/common/Expression.php';
require_once ROOT_PATH . 'packages/core/includes/common/QueryException.php';

/**
 * Class dùng để xây dựng câu truy vấn cơ sở dữ liệu
 * Tính năng được lấy ý tưởng từ laravel query builder nhưng đã được gợt bỏ một số thành phần để giúp code base đơn giản 
 * Hiện tại class support gần như toàn bộ các components được liệt kê bên dưới với các câu query từ đơn giản đến rất phức tạp
 * Dev có thể xem qua implement của class để biết thêm chi tiết
 */
class Query 
{
    protected $selectComponents = [
        /**
         * select('a', 'b')
         * select(['a', 'b'])
         * select(['a', 'b' => function($q){
         *  $q->select('c')->from('d')->where('e', 1)->limit(1)
         * }]) => select a, (select c from d where e = 1 limit 1) as b
         */
        'selects',

        /**
         * from('tbl', 'alias_tbl')
         * from(function($q){
         *  $q->select('c')->from('d')->where('e', 1)->limit(1)
         * }, 'alias_tbl') => from (select c from d where e = 1 limit 1) as b
         */
        'from',
        'joins',
        'wheres',
        'groups',
        //'havings',
        'orders',
        'limit',
        'offset',
    ];

    protected $selects;
    protected $distinct;
    protected $from;
    protected $joins;
    protected $wheres;
    protected $groups;
    //protected $havings;
    protected $orders;
    protected $limit;
    protected $offset;

    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];
    protected $tablePrefix = '';
    private static $defaultConnection = null;
    public static $pretty = false;
    private $connection = null;

    /**
     * Disable new class
     * Để sử dụng class vui lòng sử dụng toán tử static call `::`
     * Ví dụ: Query::select('aaa')->from('bbb')->where('ccc', 1)->limit(20)->offset(3);
     */
    private function __construct(){}

    /**
     * { function_description }
     *
     * @param      <type>  $connection  The connection
     */
    public static function boot($connection)
    {
        self::$defaultConnection = $connection;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $connection  The connection
     *
     * @return     <type>
     */
    private function connection($connection)
    {
        return tap($this, function($connection) {
            $this->connection = $connection;
        });
    }

    /**
     * Gets the connection.
     *
     * @return     <type>  The connection.
     */
    protected function getConnection()
    {
        return $this->connection ?? self::$defaultConnection;
    }

    /**
     * { function_description }
     *
     * @return     self
     */
    protected function distinct()
    {
        $columns = func_get_args();

        if (count($columns) > 0) {
            $this->distinct = is_array($columns[0]) || is_bool($columns[0]) ? $columns[0] : $columns;
        } else {
            $this->distinct = true;
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      array  $columns  The columns
     *
     * @return     self 
     */
    protected function select($columns = ['*'])
    {
        $this->selects = [];
        $columns = is_array($columns) ? $columns : func_get_args();

        foreach ($columns as $as => $column) {
            if (is_string($as) && $this->isQueryable($column)) {
                $this->selectSub($column, $as);
            } else {
                $this->selects[] = $column;
            }
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      string  $query  The query
     * @param      <type>  $as     { parameter_description }
     *
     * @return     string
     */
    protected function selectSub($query, $as)
    {
        $query = $this->createSub($query);

        return $this->selectRaw(
            '('.$query.') as '. $as
        );
    }

    /**
     * Add a new "raw" select expression to the query.
     *
     * @param  string  $expression
     * @return self
     */
    protected function selectRaw($expression)
    {
        $this->addSelect(new Expression($expression));

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      string  $query  The query
     * @param      <type>  $as     { parameter_description }
     *
     * @return     string
     */
    protected function fromSub($query, $as)
    {
        $query = $this->createSub($query);

        return $this->fromRaw(
            '('.$query.') as '.$as
        );
    }

    /**
     * Add a raw from clause to the query.
     *
     * @param  string  $expression
     * @return self
     */
    protected function fromRaw($expression)
    {
        $this->from = new Expression($expression);

        return $this;
    }

    /**
     * Creates a sub.
     *
     * @param      <type>  $query  The query
     *
     * @return     <type>
     */
    protected function createSub($query)
    {
        if ($query instanceof Closure) {
            $callback = $query;

            $callback($query = $this->forSubQuery());
        }

        return $this->parseSub($query);
    }

    /**
     * { function_description }
     *
     * @param      <type>                    $query  The query
     *
     * @throws     InvalidArgumentException  (description)
     *
     * @return     <type>                  
     */
    protected function parseSub($query)
    {
        if ($query instanceof self) {
            return $query->toSql();
        } 

        if (is_string($query)) {
            return $query;
        }    

        throw new InvalidArgumentException();
    }

    /**
     * Determine if the value is a query builder instance or a Closure.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isQueryable($value)
    {
        return $value instanceof self ||
               $value instanceof Closure;
    }

    /**
     * Adds a select.
     *
     * @param      <type>  $column  The column
     *
     * @return     self  
     */
    protected function addSelect($column)
    {
        $columns = is_array($column) ? $column : func_get_args();

        foreach ($columns as $as => $column) {
            if (is_string($as) && $this->isQueryable($column)) {
                if (is_null($this->selects)) {
                    $this->select($this->from.'.*');
                }

                $this->selectSub($column, $as);
            } else {
                $this->selects[] = $column;
            }
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $table  The table
     * @param      <type>  $as     { parameter_description }
     *
     * @return     self  
     */
    protected function from($table, $as = null)
    {
        if ($this->isQueryable($table)) {
            return $this->fromSub($table, $as);
        }

        $this->from = $as ? "{$table} as {$as}" : $table;

        return $this;
    }


    /**
     * { function_description }
     *
     * @param      <type>  $table     The table
     * @param      <type>  $first     The first
     * @param      <type>  $operator  The operator
     * @param      <type>  $second    The second
     * @param      string  $type      The type
     * @param      bool    $where     The where
     *
     * @return     self  
     */
    protected function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $join = $this->newJoin($this, $type, $table);

        if ($first instanceof Closure) {
            $first($join);

            $this->joins[] = $join;
        } else {
            $method = $where ? 'where' : 'on';

            $this->joins[] = $join->$method($first, $operator, $second);
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      self       $parentQuery  The parent query
     * @param      <type>     $type         The type
     * @param      <type>     $table        The table
     *
     * @return     QueryJoin  The query join.
     */
    protected function newJoin(self $parentQuery, $type, $table)
    {
        return new QueryJoin($parentQuery, $type, $table);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $table     The table
     * @param      <type>  $first     The first
     * @param      <type>  $operator  The operator
     * @param      <type>  $second    The second
     *
     * @return     <type>
     */
    protected function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $query     The query
     * @param      <type>  $as        { parameter_description }
     * @param      <type>  $first     The first
     * @param      <type>  $operator  The operator
     * @param      <type>  $second    The second
     *
     * @return     <type>
     */
    protected function leftJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        return $this->joinSub($query, $as, $first, $operator, $second, 'left');
    }

    /**
     * Add a subquery join clause to the query.
     *
     * @param  \Closure|Query|string  $query
     * @param  string  $as
     * @param  \Closure|string  $first
     * @param  string|null  $operator
     * @param  string|null  $second
     * @param  string  $type
     * @param  bool  $where
     * @return Query|static
     *
     * @throws \InvalidArgumentException
     */
    protected function joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $query = $this->createSub($query);

        $expression = '('.$query.') as '.$as;

        return $this->join(new Expression($expression), $first, $operator, $second, $type, $where);
    }

    /**
     * Add a right join to the query.
     *
     * @param  string  $table
     * @param  \Closure|string  $first
     * @param  string|null  $operator
     * @param  string|null  $second
     * @return $this
     */
    protected function rightJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'right');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $query     The query
     * @param      <type>  $as        { parameter_description }
     * @param      <type>  $first     The first
     * @param      <type>  $operator  The operator
     * @param      <type>  $second    The second
     *
     * @return     <type>
     */
    protected function rightJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        return $this->joinSub($query, $as, $first, $operator, $second, 'right');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column           The column
     * @param      string  $operator         The operator
     * @param      <type>  $value            The value
     * @param      string  $boolean          The boolean
     * @param      mixed   ...$unnamed_args  The unnamed arguments
     *
     * @return     self  
     */
    protected function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_array($column)) {
            return $this->addArrayOfWheres($column, $boolean);
        }

        // Nếu chỉ 2 tham số truyền vào thì nó sẽ là column operator(=) value 
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );
        
        // Trường hợp  column là hàm và không có operator thì ta xem như nó là where nested (where được bọc trong dấu đóng mở ngoặc "()" ) 
        if ($column instanceof Closure && is_null($operator)) {
            return $this->whereNested($column, $boolean);
        }

        if ($this->invalidOperator($operator)) {
            [$value, $operator] = [$operator, '='];
        }

        // Nếu giá trị là clouse thì giá trị là là sub query kiểu (a = (select ...))
        if ($value instanceof Closure) {
            return $this->whereSub($column, $operator, $value, $boolean);
        }

        if (is_null($value)) {
            return $this->whereNull($column, $boolean, $operator !== '=');
        }

        $type = 'Basic';

        $this->wheres[] = compact(
            'type', 'column', 'operator', 'value', 'boolean'
        );

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $value       The value
     * @param      <type>  $operator    The operator
     * @param      bool    $useDefault  The use default
     *
     * @return     bool  
     */
    protected function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        return $useDefault ? [$operator, '='] : [$value, $operator];
    }

    /**
     * Determine if the given operator is supported.
     *
     * @param  string  $operator
     * @return bool
     */
    protected function invalidOperator($operator)
    {
        return ! in_array(strtolower($operator), $this->operators, true);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column    The column
     * @param      <type>  $operator  The operator
     * @param      <type>  $value     The value
     *
     * @return     <type>
     */
    protected function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * { function_description }
     *
     * @param      Closure  $closure  The closure
     * @param      string   $boolean  The boolean
     *
     * @return     self   
     */
    protected function whereNested(Closure $closure, string $boolean = 'and')
    {   
        $type = 'Nested';
        $closure($query = $this->newQuery()->from($this->from));
        $this->wheres[] = compact('type', 'query', 'boolean');

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>   $column    The column
     * @param      <type>   $operator  The operator
     * @param      Closure  $closure   The closure
     * @param      string   $boolean   The boolean
     *
     * @return     self   
     */
    protected function whereSub($column, $operator, Closure $closure, string $boolean = 'and')
    {   
        $type = 'Sub';
        $closure($query = $this->forSubQuery());
        $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $first     The first
     * @param      <type>  $operator  The operator
     * @param      <type>  $second    The second
     * @param      string  $boolean   The boolean
     *
     * @return     self  
     */
    protected function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
    {
        if (is_array($first)) {
            return $this->addArrayOfWheres($first, $boolean, 'whereColumn');
        }

        $type = 'Column';

        $this->wheres[] = compact(
            'type', 'first', 'operator', 'second', 'boolean'
        );

        return $this;
    }

    /**
     * Adds array of wheres.
     *
     * @param      <type>  $column   The column
     * @param      <type>  $boolean  The boolean
     * @param      string  $method   The method
     *
     * @return     <type>
     */
    protected function addArrayOfWheres($column, $boolean, $method = 'where')
    {
        return $this->whereNested(function ($query) use ($column, $method, $boolean) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->{$method}(...array_values($value));
                } else {
                    $query->$method($key, '=', $value, $boolean);
                }
            }
        }, $boolean);
    }


    /**
     * { function_description }
     *
     * @param      <type>  $value  The value
     *
     * @return     array 
     */
    protected static function arrayWrap($value)
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    /**
     * { function_description }
     *
     * @param      <type>  $columns  The columns
     * @param      string  $boolean  The boolean
     * @param      bool    $not      Not
     *
     * @return     self  
     */
    protected function whereNull($columns, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        foreach (self::arrayWrap($columns) as $column) {
            $this->wheres[] = compact('type', 'column', 'boolean');
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column  The column
     *
     * @return     <type>
     */
    protected function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $columns  The columns
     * @param      string  $boolean  The boolean
     *
     * @return     <type>
     */
    protected function whereNotNull($columns, $boolean = 'and')
    {
        return $this->whereNull($columns, $boolean, true);
    }

    /**
     * Add a where between statement to the query.
     *
     * @param  string  $column
     * @param  array  $values
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    protected function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('type', 'column', 'values', 'boolean', 'not');

        return $this;
    }

    /**
     * Add an or where between statement to the query.
     *
     * @param  string  $column
     * @param  array  $values
     * @return Query|static
     */
    protected function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    /**
     * Add a where not between statement to the query.
     *
     * @param  string  $column
     * @param  array  $values
     * @param  string  $boolean
     * @return Query|static
     */
    protected function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * Add an or where not between statement to the query.
     *
     * @param  string  $column
     * @param  array  $values
     * @return Query|static
     */
    protected function orWhereNotBetween($column, array $values)
    {
        return $this->whereNotBetween($column, $values, 'or');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column   The column
     * @param      <type>  $values   The values
     * @param      string  $boolean  The boolean
     * @param      bool    $not      Not
     *
     * @return     self  
     */
    protected function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';

        if ($this->isQueryable($values)) {
            $values= [new Expression($this->createSub($values))];
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column  The column
     * @param      <type>  $values  The values
     *
     * @return     <type>
     */
    protected function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }

    /**
     * Add a basic "where not" clause to the query.
     *
     * @param  \Closure|string|array  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    protected function whereNot($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->where($column, $operator, $value, $boolean.' not');
    }

    /**
     * Add an "or where not" clause to the query.
     *
     * @param  \Closure|string|array  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return $this
     */
    protected function orWhereNot($column, $operator = null, $value = null)
    {
        return $this->whereNot($column, $operator, $value, 'or');
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column   The column
     * @param      <type>  $values   The values
     * @param      string  $boolean  The boolean
     *
     * @return     <type>
     */
    protected function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column  The column
     * @param      <type>  $values  The values
     *
     * @return     <type>
     */
    protected function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }

    /**
     * Determines if where exists.
     *
     * @param      Closure  $callback  The callback
     * @param      string   $boolean   The boolean
     * @param      bool     $not       Not
     *
     * @return     <type>   True if where exists, False otherwise.
     */
    protected function whereExists(Closure $callback, $boolean = 'and', $not = false)
    {
        $callback($query = $this->forSubQuery());

        return $this->addWhereExistsQuery($query, $boolean, $not);
    }

    /**
     * Add an or exists clause to the query.
     *
     * @param  \Closure  $callback
     * @param  bool  $not
     * @return $this
     */
    protected function orWhereExists(Closure $callback, $not = false)
    {
        return $this->whereExists($callback, 'or', $not);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param  \Closure  $callback
     * @param  string  $boolean
     * @return $this
     */
    protected function whereNotExists(Closure $callback, $boolean = 'and')
    {
        return $this->whereExists($callback, $boolean, true);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    protected function orWhereNotExists(Closure $callback)
    {
        return $this->orWhereExists($callback, true);
    }

    /**
     * Add an exists clause to the query.
     *
     * @param  self $query
     * @param  string  $boolean
     * @param  bool  $not
     * @return $this
     */
    protected function addWhereExistsQuery(self $query, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $this->wheres[] = compact('type', 'query', 'boolean');

        return $this;
    }


    /**
     * { function_description }
     *
     * @param      int   $limit  The limit
     *
     * @return     self
     */
    protected function limit(int $value = null)
    {   
        if ($value >= 0) {
            $this->limit = ! is_null($value) ? (int) $value : null;
        }

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      int   $offset  The offset
     *
     * @return     self
     */
    protected function offset(int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $groups  The groups
     *
     * @return     self  
     */
    protected function groupBy($groups)
    {
        $this->groups = is_array($groups) ? $groups : func_get_args();

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>                    $column     The column
     * @param      string                    $direction  The direction
     *
     * @throws     InvalidArgumentException  (description)
     *
     * @return     Query
     */
    protected function orderBy($column, $direction = 'asc')
    {
        if ($this->isQueryable($column)) {
            $column= [$this->createSub($column)];
        }

        $direction = strtolower($direction);

        if (! in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order direction must be "asc" or "desc".');
        }

        $this->orders[] = [
            'column' => $column,
            'direction' => $direction,
        ];

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $column  The column
     *
     * @return     Query
     */
    protected function orderByDesc($column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * { function_description }
     *
     * @return     Query
     */
    protected function newQuery()
    {   
        return new static();
    }

    /**
     * { function_description }
     *
     * @return     Query
     */
    protected function forSubQuery()
    {
        return $this->newQuery();
    }

    /**
     * { function_description }
     *
     * @param      bool    $pretty  The pretty
     *
     * @return     string
     */
    protected function toSql(bool $pretty = false)
    {
        self::$pretty = $pretty;

        return implode($this->pretty(), $this->compileComponents());
    }

    /**
     * Compile the components necessary for a select clause.
     *
     * @return array
     */
    protected function compileComponents()
    {   
        return array_reduce($this->selectComponents, function($sql,  $component) {
            if(!is_null($this->{$component})){
                $sql[$component] = $this->{"compile".ucfirst($component)}();
            }

            return $sql;
        }, []);
    }


    /**
     * Returns a where representation of the object.
     *
     * @return     string  Where representation of the object.
     */
    protected function toWhere()
    {
        if (!is_null($this->wheres) && count($wheres = $this->compileWheresToArray()) > 0) {
            return $this->removeLeadingBoolean(implode(' ', $wheres));
        }

        return '';
    }

    /**
     * Update records in the database.
     *
     * @param  array  $values
     * @return boolean
     */
    protected function update(array $values)
    {
        $result = $this->getConnection()->query(
            $sql = $this->compileUpdate($values)
        );

        if($result === false) {
            throw new QueryException('Cập nhật bản ghi thất bại!', null, null, $sql, $this->getConnection()->error);
        }

        return tap(!!$result, function($result) {
            if (!System::is_local()) {
                CrmSync::publishEventOnUpdate($this->from, Session::get('group_id'), $this->compileWhereWithoutConjunction());
            }
        });
    }


    /**
     * Compile an update statement into SQL.
     *
     * @param  array  $values
     * @return string
     */
    protected function compileUpdate(array $values)
    {
        $table = $this->wrapTable($this->from);

        $columns = $this->compileUpdateColumns($values);

        $where = $this->compileWheres();

        return trim(
            isset($this->joins)
                ? $this->compileUpdateWithJoins($table, $columns, $where)
                : $this->compileUpdateWithoutJoins($table, $columns, $where)
        );
    }

    /**
     * Compile the columns for an update statement.
     *
     * @param  array  $values
     * @return string
     */
    protected function compileUpdateColumns(array $values)
    {   
        foreach ($values as $key => $value) {
            $values[$key] = $this->wrap($key) . ' = ' . $this->parameter($value);
        }

        return implode(', ', $values);
    }

    /**
     * Compile an update statement without joins into SQL.
     *
     * @param  string  $table
     * @param  string  $columns
     * @param  string  $where
     * @return string
     */
    protected function compileUpdateWithoutJoins($table, $columns, $where)
    {
        return "update {$table} set {$columns} {$where}";
    }

    /**
     * Compile an update statement with joins into SQL.
     *
     * @param  Query  $query
     * @param  string  $table
     * @param  string  $columns
     * @param  string  $where
     * @return string
     */
    protected function compileUpdateWithJoins($table, $columns, $where)
    {
        $joins = $this->compileJoins();

        return "update {$table} {$joins} set {$columns} {$where}";
    }

    /**
     * Insert new records into the database.
     *
     * @param  array  $values
     * @return bool
     */
    protected function insert(array $values)
    {
        if (empty($values)) {
            return true;
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        }else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $result = $this->getConnection()->query(
            $sql = $this->compileInsert($values)
        );

        if($result === false) {
            throw new QueryException('Thêm bản ghi thất bại!', null, null, $sql, $this->getConnection()->error);
        }

        if (!System::is_local()) {
            CrmSync::publishEventOnInsert($this->from, Session::get('group_id'), "`id` = {$this->getConnection()->insert_id}");
        }

        return $this->getConnection()->insert_id;
    }

    /**
     * Insert or update a record matching the attributes, and fill it with values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @return bool
     */
    protected function updateOrInsert(array $attributes, array $values = [])
    {
        if (! $this->where($attributes)->exists()) {
            return $this->insert(array_merge($attributes, $values));
        }

        if (empty($values)) {
            return true;
        }

        return (bool) $this->limit(1)->update($values);
    }

    /**
     * Compile an insert statement into SQL.
     *
     * @param  array  $values
     * @return string
     */
    protected function compileInsert(array $values)
    {
        $table = $this->wrapTable($this->from);

        if (empty($values)) {
            return "insert into {$table} default values";
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        }

        $columns = $this->columnize(array_keys(reset($values)));

        $records = array_map(function($record){
            return '('.$this->parameterize($record).')';
        }, $values);

        $parameters = implode(', ', $records);

        return "insert into $table ($columns) values $parameters";
    }

    /**
     * Wrap a table in keyword identifiers.
     *
     * @param  Expression|string  $table
     * @return string
     */
    protected function wrapTable($table)
    {
        if (! $this->isExpression($table)) {
            return $this->wrap($table, true);
        }

        return $this->getValue($table);
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param  Expression|string  $value
     * @param  bool  $prefixAlias
     * @return string
     */
    protected function wrap($value, $prefixAlias = false)
    {
        if ($this->isExpression($value)) {
            return $this->getValue($value);
        }

        if (stripos($value, ' as ') !== false) {
            return $this->wrapAliasedValue($value, $prefixAlias);
        }

        return $this->wrapSegments(explode('.', $value));
    }

    /**
     * Wrap a value that has an alias.
     *
     * @param  string  $value
     * @param  bool  $prefixAlias
     * @return string
     */
    protected function wrapAliasedValue($value, $prefixAlias = false)
    {
        $segments = preg_split('/\s+as\s+/i', $value);

        if ($prefixAlias) {
            $segments[1] = $this->tablePrefix.$segments[1];
        }

        return $this->wrap($segments[0]).' as '.$this->wrapValue($segments[1]);
    }

    /**
     * Wrap the given value segments.
     *
     * @param  array  $segments
     * @return string
     */
    protected function wrapSegments($segments)
    {
        foreach ($segments as $key => $segment) {
            $segments[$key] = $key == 0 && count($segments) > 1
                                        ? $this->wrapTable($segment)
                                        : $this->wrapValue($segment);
        }
        
        return implode('.', $segments);
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value)
    {
        return $value === '*' ? $value : '`'.str_replace('`', '``', $value).'`';
    }

    /**
     * Convert an array of column names into a delimited string.
     *
     * @param  array  $columns
     * @return string
     */
    protected function columnize(array $columns)
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    /**
     * Get the value of a raw expression.
     *
     * @param  Expression  $expression
     * @return mixed
     */
    protected function getValue($expression)
    {
        return $expression->getValue();
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    protected function exists()
    {
        $results = $this->querySelectRaw(
            $this->compileExists()
        );

        if (isset($results[0])) {
            $results = (array) $results[0];

            return (bool) $results['exists'];
        }

        return false;
    }

    /**
     * Queries a select raw.
     *
     * @param      string  $sql    The sql
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    protected function querySelectRaw(string $sql)
    {
        return $this->getConnection()
                    ->query($sql)
                    ->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Determine if no rows exist for the current query.
     *
     * @return bool
     */
    protected function doesntExist()
    {
        return ! $this->exists();
    }

    /**
     * { function_description }
     *
     * @return     <type>
     */
    private function pretty()
    {
        return self::$pretty ? "\n" : ' ';
    }

    /**
     * Gets the specified columns.
     *
     * @param      array  $columns  The columns
     */
    protected function get($columns = ['*'])
    {
        try{
            $result = $this->getConnection()->query(
                $this->select($columns)->toSql()
            );

            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }catch(Throwable $e) {
            System::is_local() && dd($e);
        }
    }

    /**
     * Gets the column.
     *
     * @param      string  $name   The name
     * @param      string  $key    The key
     *
     * @return     <type>  The column.
     */
    protected function getColumn(string $name, string $key = null)
    {
        $columns = array_filter([$name, $key]);
        
        $name = explode('.', $name);
        $key = explode('.', $key);

        $name = array_pop($name);
        $key = array_pop($key);

        return array_reduce($this->get($columns), function($results, $row) use($name, $key) {
            if ($key && isset($row[$key])) {
                $results[$row[$key]] = $row[$name] ?? null;
            } else {
                $results[] = $row[$name] ?? null;
            }

            return $results;
        },  []);
    }

    /**
     * { function_description }
     *
     * @param      array   $columns  The columns
     *
     * @return     <type>
     */
    protected function first($columns = ['*'])
    {
        return $this->limit(1)->get($columns)[0] ?? [];
    }

    /**
     * dump and die
     *
     * @param      array   $columns  The columns
     *
     * @return     <type>
     */
    protected function dd($columns = ['*'])
    {
        return dd($this->select($columns)->toSql(1));
    }

    /**
     * Thực thi resolve closure nếu expression trả về true
     * Ngược lại cung nếu cung cấp $reject thì thực thi reject 
     *
     * @param      array   $columns  The columns
     *
     * @return     <type>
     */
    protected function when($expression, Closure $resolve, Closure $reject = null)
    {   
        $boolean = $expression instanceof Closure ? $expression($this) : $expression;

        if($boolean) {
            $resolve($this);
        } elseif(!is_null($reject)) {
            $reject($this);
        }

        return $this;
    }

    /**
     * Tương tự when nhưng ngược lại
     *
     * @param      <type>   $expression  The expression
     * @param      Closure  $resolve     The resolve
     * @param      Closure  $reject      The reject
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    protected function unless($expression, Closure $resolve, Closure $reject = null)
    {
        return $this->when(!$expression, $resolve, $reject);
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileSelects()
    {
        return 'select ' . $this->columnize($this->selects);
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileSelect()
    {
        $original = $this->selects;

        if (is_null($this->selects)) {
            $this->selects = ['*'];
        }

        $sql = trim($this->concatenate(
            $this->compileComponents()
        ));

        $this->selects = $original;

        return $sql;
    }

    /**
     * Concatenate an array of segments, removing empties.
     *
     * @param  array  $segments
     * @return string
     */
    protected function concatenate($segments)
    {
        return implode(' ', array_filter($segments, function ($value) {
            return (string) $value !== '';
        }));
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileFrom()
    {
        return 'from ' . $this->from;
    }

    /**
     * { function_description }
     *
     * @return     <type>
     */
    protected function compileJoins()
    {
        foreach($this->joins as $key => $join){
            $table = $this->wrapTable($join->table);
            $nestedJoins = is_null($join->joins) ? '' : ' '. $join->compileJoins();
            $tableAndNestedJoins = is_null($join->joins) ? $table : '('.$table.$nestedJoins.')';

            $this->joins[$key] = trim("{$join->type} join {$tableAndNestedJoins} {$join->compileWheres()}");
        }

        return implode($this->pretty(), $this->joins);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $where  The where
     *
     * @return     <type>
     */
    protected function compileWhereColumn($where)
    {
        return $this->wrap($where['first']).' '.$where['operator'].' '.$this->wrap($where['second']);
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileWheres()
    {
        if (!is_null($this->wheres) && count($wheres = $this->compileWheresToArray()) > 0) {
            $conjunction = $this instanceof QueryJoin ? 'on' : 'where';

            return $conjunction.' '.$this->removeLeadingBoolean(implode(' ', $wheres));
        }

        return '';
    }

    /**
     * Chỉ biên dịch where condition mà không bao gồm "on" hay "where" clause
     *
     * @return     string
     */
    protected function compileWhereWithoutConjunction()
    {
        if (!is_null($this->wheres) && count($wheres = $this->compileWheresToArray()) > 0) {
            return $this->removeLeadingBoolean(implode(' ', $wheres));
        }

        return '';
    }

    /**
     * { function_description }
     *
     * @return     <type>
     */
    protected function compileWheresToArray()
    {   
        return array_map(function($where){
            return $where['boolean'] . ' ' . $this->{"compileWhere" . ucfirst($where['type'])}($where);
        }, $this->wheres);
    }

    /**
     * Removes a leading boolean.
     *
     * @param      <type>  $value  The value
     *
     * @return     <type>
     */
    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/and |or /i', '', $value, 1);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $where  The where
     *
     * @return     <type>
     */
    protected function compileWhereBasic($where)
    {   
        return sprintf('%s %s %s',
           $this->wrap($where['column']),
           $where['operator'],
           $this->parameter($where['value'])
       ); 
    }

    /**
     * { function_description }
     *
     * @param      <type>  $where  The where
     *
     * @return     bool  
     */
    protected function compileWhereNested($where)
    {   
        return pipe($where['query']->compileWhereWithoutConjunction(), function($where) {
            return empty($where) ? '' : '('.$where.')';
        });
    }

    /**
     * Create query parameter place-holders for an array.
     *
     * @param  array  $values
     * @return string
     */
    protected function parameterize(array $values)
    {
        return implode(', ', array_map([$this, 'parameter'], $values));
    }

    /**
     * Get the appropriate query parameter place-holder for a value.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function parameter($value)
    {   
        switch(true) {
            case $this->isExpression($value):
                return $this->getValue($value);

            case is_int($value):
                return $value;

            default:
                return '"' . $this->getConnection()->real_escape_string($value) . '"';
        }
    }

    /**
     * Determine if the given value is a raw expression.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isExpression($value)
    {
        return $value instanceof Expression;
    }

    /**
     * { function_description }
     *
     * @param      <type>      $value  The value
     *
     * @return     Expression  ( description_of_the_return_value )
     */
    protected function raw($value)
    {
        return new Expression($value);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $where  The where
     *
     * @return     string
     */
    protected function compileWhereIn($where)
    {
        if (! empty($where['values'])) {
            return $this->wrap($where['column']).' in ('.$this->parameterize($where['values']) .')';
        }

        return '0 = 1';
    }

    /**
     * { function_description }
     *
     * @param      <type>  $where  The where
     *
     * @return     <type>
     */
    protected function compileWhereBetween($where)
    {
        $between = $where['not'] ? 'not between' : 'between';

        $min = reset($where['values']);

        $max = end($where['values']);

        return $this->wrap($where['column']) .' ' . $between . ' "' . $min . '" and "' . $max . '"';
    }

    /**
     * { function_description }
     *
     * @param      Builder  $query  The query
     * @param      <type>   $where  The where
     *
     * @return     string 
     */
    protected function compileWhereNotIn($where)
    {
        if (! empty($where['values'])) {
            return $this->wrap($where['column']) . ' not in ('. $this->parameterize($where['values']) . ')';
        }

        return '1 = 1';
    }

    /**
     * Determines if compile where exists.
     *
     * @param      array   $where  The where
     *
     * @return     string  True if compile where exists, False otherwise.
     */
    protected function compileWhereExists(array $where)
    {
        return 'exists (' . $where['query']->compileSelect() . ')';
    }

    /**
     * Determines if compile where not exists.
     *
     * @param      array   $where  The where
     *
     * @return     string  True if compile where not exists, False otherwise.
     */
    protected function compileWhereNotExists(array $where)
    {
        return 'not exists (' . $where['query']->compileSelect() . ')';
    }

    /**
     * { function_description }
     *
     * @param      array   $where  The where
     *
     * @return     string
     */
    protected function compileWhereSub(array $where)
    {   
        return sprintf('%s %s (%s)',
           $this->wrap($where['column']),
           $where['operator'],
           $where['query']->toSql($this->pretty())
       );
    }

    /**
     * { function_description }
     *
     * @param      array  $where  The where
     *
     * @return     string
     */
    protected function compileWhereNull(array $where)
    {
        return $this->wrap($where['column']) . ' is null';
    }

    /**
     * { function_description }
     *
     * @param      array  $where  The where
     *
     * @return     string
     */
    protected function compileWhereNotNull(array $where)
    {
        return $this->wrap($where['column']) . ' is not null';
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileLimit()
    {
        return 'limit ' . $this->limit;
    }

    /**
     * Compile an exists statement into SQL.
     *
     * @return string
     */
    protected function compileExists()
    {
        $select = $this->compileSelect();

        return "select exists({$select}) as exists}";
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileOffset()
    {
        return 'offset ' . $this->offset;
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileGroups()
    {
        return 'group by ' . implode(',', self::arrayWrap($this->groups));
    }

    /**
     * { function_description }
     *
     * @return     string
     */
    protected function compileOrders()
    {
        if (! empty($this->orders)) {
            return 'order by '.implode(', ', $this->compileOrdersToArray());
        }

        return '';
    }

    /**
     * { function_description }
     *
     * @param      Builder  $query   The query
     * @param      <type>   $orders  The orders
     *
     * @return     mixed
     */
    protected function compileOrdersToArray()
    {
        return array_map(function ($order) {
            return $order['sql'] ?? $order['column'] . ' '. $order['direction'];
        }, $this->orders);
    }

    /**
     * { function_description }
     *
     * @param      <type>    $value     The value
     * @param      callable  $callback  The callback
     *
     * @return     self
     */
    protected function tap(Closure $callback)
    {
        $callback($this);

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>   $value     The value
     * @param      Closure  $callback  The callback
     *
     * @return     mixed
     */
    protected function pipe(Closure $callback)
    {
        return $callback($this);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $name   The name
     * @param      <type>  $args   The arguments
     *
     * @return     mixed
     */
    public function __call($name, $args)
    {   
        if(!method_exists($this, $name)){
            throw new Exception(sprintf('Method `%s` not exists!', $name));
        }

        return $this->{$name}(...$args);
    }

    /**
     * { function_description }
     *
     * @param      string  $name   The name
     * @param      <type>  $args   The arguments
     *
     * @return     mixed
     */
    public static function __callStatic(string $name, $args)
    {   
        if(!method_exists(self::class, $name)){
            throw new Exception(sprintf('Static method `%s` not exists!', $name));
        }

        return (new static())->{$name}(...$args);
    }    
}