<?php

declare(strict_types = 1);

namespace Weiran\MgrPage\Classes\Widgets;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use JsonException;
use Weiran\Area\Classes\Form\Field\Area;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Classes\Traits\PoppyTrait;
use Weiran\Framework\Helper\ArrayHelper;
use Weiran\Framework\Helper\FileHelper;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Form;
use Weiran\MgrPage\Classes\Form\Field;
use Weiran\MgrPage\Classes\Form\Field\Checkbox;
use Weiran\MgrPage\Classes\Layout\Content;

/**
 * Class Form.
 * @method Area                 area($name, $label = '')
 * @method Field\Code           code($name, $label = '')
 * @method Field\Text           text($name, $label = '')
 * @method Field\Link           link($label = '')
 * @method Field\Password       password($name, $label = '')
 * @method Field\Checkbox       checkbox($name, $label = '')
 * @method Field\Radio          radio($name, $label = '')
 * @method Field\Select         select($name, $label = '')
 * @method Field\SelectDo       selectDo($name, $label = '')
 * @method Field\MultipleSelect multipleSelect($name, $label = '')
 * @method Field\Textarea       textarea($name, $label = '')
 * @method Field\Hidden         hidden($name, $label = '')
 * @method Field\Id             id($name, $label = '')
 * @method Field\Ip             ip($name, $label = '')
 * @method Field\Url            url($name, $label = '')
 * @method Field\Color          color($name, $label = '')
 * @method Field\Captcha        captcha($name, $label = '')
 * @method Field\Email          email($name, $label = '')
 * @method Field\Mobile         mobile($name, $label = '')
 * @method Field\File           file($name, $label = '')
 * @method Field\Image          image($name, $label = '')
 * @method Field\MultiImage     multiImage($name, $label = '')
 * @method Field\Date           date($name, $label = '')
 * @method Field\Datetime       datetime($name, $label = '')
 * @method Field\Time           time($name, $label = '')
 * @method Field\Year           year($column, $label = '')
 * @method Field\Month          month($column, $label = '')
 * @method Field\DateRange      dateRange($start, $end, $label = '')
 * @method Field\DateTimeRange  dateTimeRange($at, $label = '')
 * @method Field\TimeRange      timeRange($start, $end, $label = '')
 * @method Field\Number         number($name, $label = '')
 * @method Field\Currency       currency($name, $label = '')
 * @method Field\SwitchField    switch ($name, $label = '')
 * @method Field\Display        display($name, $label = '')
 * @method Field\Divider        divider($title = '')
 * @method Field\Editor         editor($name, $label = '')
 * @method Field\Decimal        decimal($column, $label = '')
 * @method Field\Html           html($html, $arguments = [])
 * @method Field\Tags           tags($column, $label = '')
 * @method Field\Timezone       timezone($column, $label = '')
 * @method Field\Keyword        keyword($column, $label = '')
 * @method Field\Question       question($column, $label = '')
 * @method Field\Hook           hook($column, $label = '')
 * @method Field\TableInput     tableInput($column, $label = '')
 * @method mixed                handle(Request $request)
 */
class FormWidget implements Renderable
{
    use PoppyTrait;


    /**
     * 资源
     * @var Collection|null
     */
    private static ?Collection $assets = null;

    /**
     * 资源内容
     * @var Collection|null
     */
    private static ?Collection $assetsStr = null;

    /**
     * @var bool
     */
    public bool $inbox = true;

    /**
     * @var bool 是否是 Ajax 模式提交
     */
    public bool $ajax = false;

    /**
     * The title of form.
     * @var string
     */
    protected string $title = '';

    /**
     * 是否包含 JS 加载界面
     * @var bool
     */
    protected $withContent = true;
    /**
     * @var Field[]
     */
    protected $fields = [];
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * Available buttons.
     * @var array
     */
    protected array $buttons = ['reset', 'submit'];
    /**
     * 可用操作
     * @var Closure|null
     */
    protected ?Closure $boxTools = null;

    /**
     * Width for label and submit field.
     * @var array
     */
    protected array $width = [
        'label' => 3,
        'field' => 9,
    ];

    /**
     * Form constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);

        $this->initFormAttributes();
    }

    /**
     * 添加资源
     * @param string $name
     * @param string $path
     * @return void
     */
    public static function assets(string $name, string $path): void
    {
        if (is_null(self::$assets)) {
            self::$assets = collect();
        }
        $type = FileHelper::ext($path);
        self::$assets->push([
            'name' => $name,
            'type' => $type,
            'path' => $path,
        ]);
    }

    /**
     * 添加内容
     * @param string $name
     * @param string $content
     * @param string $type
     * @return void
     */
    public static function assetsStr(string $name, string $content, string $type = 'js'): void
    {
        if (is_null(self::$assetsStr)) {
            self::$assetsStr = collect();
        }
        self::$assetsStr->push([
            'name'    => $name,
            'type'    => $type,
            'content' => $content,
        ]);
    }

    /**
     * 根据指定名称获取资源并追加到头部
     * @param string $name
     * @return string
     */
    public static function assetsAppendHead(string $name): string
    {
        if (is_null(self::$assets)) {
            return '';
        }
        $assets = self::$assets->where('name', $name);
        return $assets->map(function ($item) {
            $path = $item['path'];
            if ($item['type'] === 'js') {
                return "<script src=\"{$path}\"></script>";
            }
            if ($item['type'] === 'css') {
                return "<link rel=\"stylesheet\" href=\"{$path}\">";
            }
            return '';
        })->implode(PHP_EOL);
    }

    /**
     * 获取整合后的内容
     * @param string $name
     * @return string
     */
    public static function assetsStrAppendBody(string $name): string
    {
        if (is_null(self::$assetsStr)) {
            return '';
        }
        $assets = self::$assetsStr->where('name', $name);
        $js     = (clone $assets)->where('type', 'js')->map(function ($item) {
            return $item['content'];
        })->implode(PHP_EOL);
        $css    = (clone $assets)->where('type', 'css')->map(function ($item) {
            return $item['content'];
        })->implode(PHP_EOL);

        return '<script>' . PHP_EOL . $js . PHP_EOL . '</script>' . PHP_EOL . '<style>' . $css . '</style>';
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Fill data to form fields.
     * @param array|Arrayable $data
     * @return $this
     */
    public function fill($data = []): self
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (!empty($data)) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sanitize()
    {
        foreach (['_form_', '_token'] as $key) {
            request()->request->remove($key);
        }

        return $this;
    }

    /**
     * Add form attributes.
     * @param string|array $attr
     * @param string       $value
     * @return $this
     */
    public function attribute($attr, $value = ''): self
    {
        if (is_array($attr)) {
            foreach ($attr as $key => $val) {
                $this->attribute($key, $val);
            }
        }
        else {
            $this->attributes[$attr] = $value;
        }

        return $this;
    }

    /**
     * Format form attributes form array to html.
     * @param array $attributes
     * @return string
     */
    public function formatAttribute($attributes = []): string
    {
        $attributes = $attributes ?: $this->attributes;

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $key => $val) {
            $html[] = "$key=\"$val\"";
        }

        return implode(' ', $html) ?: '';
    }

    /**
     * Action uri of the form.
     * @param string $action
     * @return $this
     */
    public function action($action)
    {
        return $this->attribute('action', $action);
    }

    /**
     * Method of the form.
     * @param string $method
     * @return FormWidget
     */
    public function method(string $method = 'POST'): self
    {
        if (strtolower($method) === 'put') {
            $this->hidden('_method')->default($method);

            return $this;
        }

        return $this->attribute('method', strtoupper($method));
    }

    /**
     * Disable Pjax.
     * @return $this
     */
    public function disablePjax()
    {
        Arr::forget($this->attributes, 'pjax-container');

        return $this;
    }

    /**
     * Disable reset button.
     * @return $this
     */
    public function disableReset()
    {
        ArrayHelper::delete($this->buttons, 'reset');

        return $this;
    }

    /**
     * Disable submit button.
     * @return $this
     */
    public function disableSubmit()
    {
        ArrayHelper::delete($this->buttons, 'submit');

        return $this;
    }

    /**
     * Set field and label width in current form.
     * @param int $fieldWidth
     * @param int $labelWidth
     * @return $this
     */
    public function setWidth($fieldWidth = 8, $labelWidth = 2)
    {
        collect($this->fields)->each(function ($field) use ($fieldWidth, $labelWidth) {
            /* @var Field $field */
            $field->setWidth($fieldWidth, $labelWidth);
        });

        // set this width
        $this->width = [
            'label' => $labelWidth,
            'field' => $fieldWidth,
        ];

        return $this;
    }

    /**
     * Add a form field to form.
     * @param Field $field
     * @return $this
     */
    public function pushField(Field $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Get all fields of form.
     * @return Field[]
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * Determine if form fields has files.
     * @return bool
     */
    public function hasFile(): bool
    {
        foreach ($this->fields as $field) {
            if ($field instanceof Field\File) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate this form fields.
     * @param Request $request
     * @return bool|MessageBag
     */
    public function validate(Request $request)
    {
        $failedValidators = [];

        foreach ($this->fields() as $field) {
            if (!$validator = $field->getValidator($request->all())) {
                continue;
            }

            if (($validator instanceof Validator) && !$validator->passes()) {
                $failedValidators[] = $validator;
            }
        }

        $message = $this->mergeValidationMessages($failedValidators);
        return $message->any() ? $message : false;
    }


    public function unbox()
    {
        $this->inbox = false;

        return $this;
    }


    /**
     * 设定工具栏
     * @param Closure $closure
     * @return $this
     */
    public function boxTools(Closure $closure): self
    {
        $this->boxTools = $closure;
        return $this;
    }

    /**
     * Render the form.
     */
    public function render()
    {
        if (method_exists($this, 'form')) {
            $this->form();
        }

        if (method_exists($this, 'handle')) {
            $this->method();
            $this->action(app('url')->current());
        }

        if (is_post()) {
            $request = $this->pyRequest();
            if ($errors = $this->validate($request)) {
                if ($this->ajax) {
                    return Resp::error($errors);
                }

                return back()->withInput()->withErrors($errors);
            }
            return $this->sanitize()->handle($request);
        }

        $form = view('py-mgr-page::tpl.widgets.form', $this->getVariables())->render();

        if (!$this->inbox || !($title = $this->title())) {
            if ($this->withContent) {
                return (new Content())->body($form);
            }
            return $form;
        }

        // init box and render
        $box = (new BoxWidget($title, $form));


        $box->tools($this->boxTools);
        if ($this->withContent) {
            return (new Content())->body($box->render());
        }
        return $box->render();
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     * @param string $method
     * @param array  $arguments
     * @return Field|$this
     */
    public function __call($method, $arguments)
    {
        $class = Form::findFieldClass($method);

        if (!$class) {
            return $this;
        }

        $name   = Arr::get($arguments, 0);
        $params = array_slice($arguments, 1);


        $field = new $class($name, $params);
        return tap($field, function ($field) {
            $this->pushField($field);
        });
    }

    /**
     * Get form title.
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Initialize the form attributes.
     */
    protected function initFormAttributes(): void
    {
        $this->attributes = [
            'method'         => 'POST',
            'action'         => '',
            'class'          => 'layui-form-auto layui-form layui-form-sm',
            'accept-charset' => 'UTF-8',
            'pjax-container' => true,
            'id'             => 'j_form_' . Str::random(4),
        ];
    }

    /**
     * Get variables for render form.
     * @return array
     */
    protected function getVariables(): array
    {
        collect($this->fields())->each->fill($this->data());

        return [
            'fields'     => $this->fields,
            'attributes' => $this->formatAttribute(),
            'validation' => $this->getJqValidation(),
            'action'     => $this->attributes['action'],
            'method'     => $this->attributes['method'],
            'buttons'    => $this->buttons,
            'width'      => $this->width,
            'ajax'       => $this->ajax,
            'id'         => $this->attributes['id'],
        ];
    }

    /**
     * Merge validation messages from input validators.
     * @param Validator[] $validators
     * @return MessageBag
     */
    protected function mergeValidationMessages($validators)
    {
        $messageBag = new MessageBag();

        foreach ($validators as $validator) {
            $messageBag = $messageBag->merge($validator->messages());
        }

        return $messageBag;
    }

    /**
     * 获取 Jquery Validation
     * @return false|string
     * @throws JsonException
     */
    private function getJqValidation()
    {
        $rules    = [];
        $messages = [];

        $funJqRules = function (array $rules, Field $field) {
            $jqRules = [];
            foreach ($rules as $rule) {
                if ($rule === Rule::required()) {
                    $jqRules['required'] = true;
                }
                if ($rule === Rule::numeric()) {
                    $jqRules['number'] = true;
                }
                if ($rule === Rule::email()) {
                    $jqRules['email'] = true;
                }
                if ($rule === Rule::mobile()) {
                    $jqRules['mobile'] = true;
                }
                if ($rule === Rule::ip()) {
                    $jqRules['ipv4'] = true;
                }
                if ($rule === Rule::url()) {
                    $jqRules['url'] = true;
                }
                if ($rule === Rule::alpha()) {
                    $jqRules['alpha'] = true;
                }
                if ($rule === Rule::alphaDash()) {
                    $jqRules['alpha_dash'] = true;
                }
                // 相等判定
                if (Str::contains($field->column(), '_confirmation')) {
                    $jqRules['equalTo'] = '#' . Str::replaceLast('_confirmation', '', $field->formatName($field->column()));
                }
                if (Str::contains($rule, 'regex')) {
                    $rule             = Str::replaceFirst('/', '', Str::after($rule, 'regex:'));
                    $jqRules['regex'] = Str::replaceLast('/', '', $rule);
                }

                if (in_array(Rule::numeric(), $rules, true)) {
                    if (in_array('min', $rules, true)) {
                        $jqRules['min'] = (int) Str::after($rule, 'min:');
                    }
                }

                if (Str::contains($rule, 'min')) {
                    if (in_array(Rule::numeric(), $rules, true)) {
                        $jqRules['min'] = (int) Str::after($rule, 'min:');
                    }
                    else {
                        $jqRules['minlength'] = (int) Str::after($rule, 'min:');
                    }
                }
                if (Str::contains($rule, 'max')) {
                    if (in_array(Rule::numeric(), $rules, true)) {
                        $jqRules['max'] = (int) Str::after($rule, 'max:');
                    }
                    else {
                        $jqRules['maxlength'] = (int) Str::after($rule, 'max:');
                    }
                }
            }


            return $jqRules;
        };
        collect($this->fields())->each(function (Field $field) use (&$rules, &$messages, $funJqRules) {
            if (count($field->getRules())) {
                $jqRules = $funJqRules($field->getRules(), $field);
                if (count($jqRules)) {
                    $name = $field->formatName($field->column());
                    if ($field instanceof Checkbox) {
                        $name .= '[]';
                    }
                    $rules[$name] = $jqRules;
                }
            }

            if (count($field->getValidationMessages())) {
                $messages[$field->column()] = $field->getValidationMessages();
            }
        });

        $jqValidation = [
            'rules'    => $rules,
            'messages' => $messages,
        ];
        return json_encode($jqValidation, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
