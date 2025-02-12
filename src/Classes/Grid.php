<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Exceptions\ApplicationException;
use Weiran\Framework\Http\Pagination\PageInfo;
use Weiran\MgrPage\Classes\Grid\Column;
use Weiran\MgrPage\Classes\Grid\Concerns\CanHidesColumns;
use Weiran\MgrPage\Classes\Grid\Concerns\HasActions;
use Weiran\MgrPage\Classes\Grid\Concerns\HasColumns;
use Weiran\MgrPage\Classes\Grid\Concerns\HasElementNames;
use Weiran\MgrPage\Classes\Grid\Concerns\HasExport;
use Weiran\MgrPage\Classes\Grid\Concerns\HasFilter;
use Weiran\MgrPage\Classes\Grid\Concerns\HasQuickButton;
use Weiran\MgrPage\Classes\Grid\Concerns\HasSelector;
use Weiran\MgrPage\Classes\Grid\Concerns\HasTools;
use Weiran\MgrPage\Classes\Grid\Concerns\HasTotalRow;
use Weiran\MgrPage\Classes\Grid\Concerns\LayDefines;
use Weiran\MgrPage\Classes\Grid\ListBase;
use Weiran\MgrPage\Classes\Grid\Model;
use Weiran\MgrPage\Classes\Grid\Row;
use Weiran\MgrPage\Classes\Layout\Content;
use Throwable;

class Grid
{
    use HasElementNames,
        HasExport,
        HasFilter,
        HasTools,
        HasTotalRow,
        HasActions,
        HasSelector,
        HasColumns,
        CanHidesColumns,
        LayDefines,
        HasQuickButton;

    /**
     * Initialization closure array.
     *
     * @var []Closure
     */
    protected static $initCallbacks = [];

    /**
     * All column names of the grid.
     *
     * @var array
     */
    public $columnNames = [];

    /**
     * Per-page options.
     *
     * @var array
     */
    public $perPages = [15, 30, 50, 100, 200];

    /**
     * 默认分页数
     * @var int
     */
    public $perPage = 15;

    /**
     * @var string
     */
    public $tableId;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator = null;

    /**
     * The grid data model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * Collection of all grid columns.
     *
     * @var Collection
     */
    protected $columns;

    /**
     * Collection of all data rows.
     *
     * @var Collection
     */
    protected $rows;

    /**
     * Rows callable function.
     *
     * @var Closure
     */
    protected $rowsCallback;

    /**
     * Grid builder.
     *
     * @var Closure
     */
    protected $builder;

    /**
     * Mark if the grid is built.
     *
     * @var bool
     */
    protected bool $isBuild = false;

    /**
     * All variables in grid view.
     *
     * @var array
     */
    protected array $variables = [];

    /**
     * Default primary key name.
     *
     * @var string
     */
    protected string $keyName = 'id';

    /**
     * View for grid to render.
     *
     * @var string
     */
    protected string $view = 'wr-mgr-page::tpl.grid.table';

    /**
     * @var []callable
     */
    protected $renderingCallbacks = [];

    /**
     * Options for grid.
     *
     * @var array
     */
    protected array $options = [
        'show_tools'        => true,
        'show_exporter'     => false,
        'show_quick_button' => true,
        'show_row_selector' => true,
    ];

    /**
     * Create a new grid instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param Closure|null                        $builder
     */
    public function __construct($model, Closure $builder = null)
    {
        $this->model   = new Model($model, $this);
        $this->keyName = $model->getKeyName();
        $this->builder = $builder;

        $this->initialize();

        $this->handleExportRequest();

        $this->callInitCallbacks();
    }

    /**
     * Initialize with user pre-defined default disables and exporter, etc.
     *
     * @param Closure|null $callback
     */
    public static function init(Closure $callback = null)
    {
        static::$initCallbacks[] = $callback;
    }

    /**
     * Get Grid model.
     *
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @param string $grid_class
     * @param string $field
     * @param string $order
     * @return Grid
     * @throws ApplicationException
     */
    public function setLists(string $grid_class, string $field = '', string $order = 'desc'): self
    {
        if (!class_exists($grid_class)) {
            throw new ApplicationException('Grid Class `' . $grid_class . '` Not Exists.');
        }

        /** @var ListBase $List */
        $List = new $grid_class($this);
        if ($List->title) {
            $this->setTitle($List->title);
        }

        $List->columns();
        $List->actions();
        $this->columns = $List->getColumns();
        if ($order && is_callable([$this->model(), 'orderBy'])
            &&
            (($pk = $this->model()->getOriginalModel()->getKeyName()) || $field)
        ) {
            $order = input('_order') ?: $order;
            $this->model()->orderBy(
                input('_field', $field ?: $pk),
                $order
            );
        }

        if (!$List->isShowRowSelector()) {
            $this->disableRowSelector();
        }

        if (!$List->isShowExporter()) {
            $this->disableExporter();
        }
        else {
            $this->disableExporter(false);
        }

        $this->filter($List->filter());
        $this->appendQuickButton($List->quickButtons());
        $this->batchActions($List->batchAction());
        return $this;
    }

    /**
     * Get or set option for grid.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this|mixed
     */
    public function option(string $key, $value = null)
    {
        if (is_null($value)) {
            return $this->options[$key];
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName(): string
    {
        return $this->keyName ?: 'id';
    }

    /**
     * Paginate the grid.
     *
     * @param int $perPage
     *
     * @return void
     */
    public function paginate($perPage = 15)
    {
        $this->perPage = $perPage;

        $this->model()->setPerPage($perPage);
    }

    /**
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator()
    {
        $this->paginator = $this->model()->eloquent();

        if ($this->paginator instanceof LengthAwarePaginator) {
            $this->paginator->appends(request()->all());
        }
        return $this->paginator;
    }

    /**
     * 设置分页的可选条目数
     *
     * @param array $perPages
     */
    public function perPages(array $perPages)
    {
        $this->perPages = $perPages;
    }

    /**
     * Disable row selector.
     *
     * @param bool $disable
     * @return Grid|mixed
     */
    public function disableRowSelector(bool $disable = true): self
    {
        return $this->disableBatchActions($disable);
    }

    /**
     * Build the grid.
     *
     * @return void
     */
    public function build()
    {
        if ($this->isBuild) {
            return;
        }

        $this->addDefaultColumns();

        $this->isBuild = true;
    }

    /**
     * Set grid row callback function.
     *
     * @param Closure|null $callable
     */
    public function rows(Closure $callable = null)
    {
        $this->rowsCallback = $callable;
    }

    /**
     * Get current resource url.
     * @return string
     */
    public function resource(): string
    {
        return url(app('request')->getPathInfo());
    }

    /**
     * Add variables to grid view.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function with($variables = []): self
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Set a view to render.
     *
     * @param string $view
     * @param array  $variables
     */
    public function setView(string $view, $variables = [])
    {
        if (!empty($variables)) {
            $this->with($variables);
        }

        $this->view = $view;
    }

    /**
     * Set grid title.
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->variables['title'] = $title;
        return $this;
    }

    /**
     * Set rendering callback.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function rendering(callable $callback): self
    {
        $this->renderingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Get the string contents of the grid view.
     *
     * @return string
     * @throws Throwable
     */
    public function render()
    {
        $this->handleExportRequest(true);

        if (input('_query')) {
            return $this->inquire(PageInfo::pagesize());
        }
        if (input('_edit')) {
            return $this->edit();
        }

        $this->build();

        $this->callRenderingCallback();

        $this->layFormat();

        $variables = $this->variables();

        $content = view($this->view, $variables)->render();
        return (new Content())->body($content);
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Initialize.
     */
    protected function initialize(): void
    {
        $this->tableId = str_replace('.', '-', uniqid('grid-table', true));

        $this->columns = Collection::make();
        $this->rows    = Collection::make();

        $this->initTools($this);
        $this->initFilter();
    }

    /**
     * Call the initialization closure array in sequence.
     */
    protected function callInitCallbacks()
    {
        if (empty(static::$initCallbacks)) {
            return;
        }

        foreach (static::$initCallbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * 添加多选框列
     *
     * @return void
     */
    protected function prependRowSelectorColumn(): void
    {
        if (!$this->option('show_row_selector')) {
            return;
        }

        $this->layPrependRowSelectorColumn();
    }

    /**
     * Apply column filter to grid query.
     *
     * @return void
     */
    protected function applyColumnFilter()
    {
        $this->columns->each(function (Column $column) {
            $column->bindFilterQuery($this->model());
        });
    }

    /**
     * Apply column search to grid query.
     *
     * @return void
     */
    protected function applyColumnSearch()
    {
        $this->columns->each(function (Column $column) {
            $column->bindSearchQuery($this->model());
        });
    }

    /**
     * @return array|Collection|mixed
     */
    protected function applyQuery()
    {
        $this->applyQuickSearch();

        $this->applyColumnFilter();

        $this->applyColumnSearch();

        $this->applySelectorQuery();

        $this->applyColumnOrderBy();

        return $this->applyFilter(false);
    }

    /**
     * 添加多选 / 操作项目
     * @return void
     */
    protected function addDefaultColumns()
    {
        $this->prependRowSelectorColumn();
    }

    /**
     * Build the grid rows.
     *
     * @param array $data
     *
     * @return void
     */
    protected function buildRows(array $data)
    {
        $this->rows = collect($data)->map(function ($model, $number) {
            return new Row($number, $model, $this->keyName);
        });

        if ($this->rowsCallback) {
            $this->rows->map($this->rowsCallback);
        }
    }

    /**
     * Get all variables will be used in grid view.
     *
     * @return array
     */
    protected function variables(): array
    {
        $this->variables['grid']      = $this;
        $this->variables['id']        = $this->tableId;
        $this->variables['filter_id'] = $this->getFilter()->getFilterId();
        $this->variables['scopes']    = $this->getFilter()->getScopes();
        $this->variables['lay']       = $this->layDefine();
        $this->variables['url_base']  = $this->pyRequest()->fullUrl();
        $this->variables['model_pk']  = $this->model()->getOriginalModel()->getKeyName();

        return $this->variables;
    }

    /**
     * Call callbacks before render.
     *
     * @return void
     */
    protected function callRenderingCallback()
    {
        foreach ($this->renderingCallbacks as $callback) {
            $callback($this);
        }
    }

    private function edit()
    {
        $pk    = input('_pk');
        $field = input('_field');
        $value = input('_value');
        if (!$this->model->edit($pk, $field, $value)) {
            return Resp::error('修改失败');
        }
        return Resp::success('修改成功');
    }

    /**
     * 查询并返回数据
     * @param int $pagesize
     * @return Response|JsonResponse|RedirectResponse
     */
    private function inquire(int $pagesize = 15)
    {
        $this->paginate($pagesize);
        /**
         * 获取到的模型数据
         */
        $collection = $this->applyQuery();

        $this->build();

        Column::setOriginalGridModels($collection);

        $data = $collection->toArray();
        $this->columns->map(function (Column $column) use (&$data) {
            $data = $column->fill($data);

            $this->columnNames[] = $column->name;
        });

        $this->buildRows($data);

        $rows = [];
        foreach ($this->rows as $row) {
            $item = [];
            foreach ($this->visibleColumnNames() as $name) {
                $item[$name] = $row->column($name);
            }
            $rows[] = $item;
        }

        $paginator = $this->paginator();

        return Resp::success('获取成功', [
            'list'       => $rows,
            'pagination' => [
                'total' => $paginator->total(),
                'page'  => $paginator->currentPage(),
                'size'  => $paginator->perPage(),
                'pages' => $paginator->lastPage(),
            ],
            '_json'      => 1,
        ]);
    }
}
