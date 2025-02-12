<?php

namespace Weiran\MgrPage\Classes\Layout;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Weiran\MgrPage\Classes\Widgets\FormWidget;
use Throwable;

class Content implements Renderable
{
    /**
     * Content title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Content description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Content constructor.
     *
     * @param Closure|null $callback
     */
    public function __construct(Closure $callback = null)
    {
        if ($callback instanceof Closure) {
            $callback($this);
        }
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set description of content.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description = '')
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Alias of method row.
     *
     * @param mixed $content
     * @return mixed
     * @throws Throwable
     */
    public function body($content)
    {
        if ($content instanceof JsonResponse) {
            return $content;
        }

        // 接收 Form 请求并返回Form 数据
        if ($content instanceof FormWidget && (is_post() || input('_query'))) {
            return $content->render();
        }

        return $this->row($content);
    }

    /**
     * Add one row for content body.
     *
     * @param $content
     *
     * @return $this
     */
    public function row($content): self
    {
        if ($content instanceof Closure) {
            $row = new Row();
            $content($row);
            $this->addRow($row);
        }
        else {
            $this->addRow(new Row($content));
        }

        return $this;
    }

    /**
     * Render giving view as content body.
     *
     * @param string $view
     * @param array  $data
     *
     * @return Content
     */
    public function view($view, $data)
    {
        return $this->body(view($view, $data));
    }

    /**
     * @param $var
     *
     * @return Content
     */
    public function dump($var)
    {
        return $this->row(dump(...func_get_args()));
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function build(): string
    {
        ob_start();

        foreach ($this->rows as $row) {
            $row->build();
        }

        return ob_get_clean();
    }

    /**
     * Render this content.
     * @throws Throwable
     */
    public function render()
    {
        $variables = [
            'title'       => $this->title,
            'description' => $this->description,
            'content'     => $this->build(),
        ];

        // 这里的显示依赖于 weiran-mgr-page
        // 因为只是后台, 所以提取一个给后边用
        return view('weiran-mgr-page::tpl.layout.content', $variables)->render();
    }

    /**
     * Add Row.
     *
     * @param Row $row
     */
    protected function addRow(Row $row)
    {
        $this->rows[] = $row;
    }
}
