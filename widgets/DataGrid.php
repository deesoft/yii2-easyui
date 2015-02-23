<?php

namespace dee\easyui\widgets;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * DataGrid
 *
 * @property array|string $url
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DataGrid extends Widget
{
    /**
     * @var array 
     */
    public $columns = [];
    public $enableFilter = true;

    /**
     * @inheritdoc
     */
    protected $properties = [
        'url'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->normalizeColumns();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        $this->normalizeColumns();
        $this->registerWidget('datagrid');
        if ($this->enableFilter) {
            DataGridAsset::register($view);
            $view->registerJs("jQuery('#{$this->options['id']}').datagrid('enableFilter');");
        }
        echo Html::tag('table', '', $this->options);
    }

    /**
     * 
     * @return array
     */
    protected function normalizeColumns()
    {
        $columns = [];
        $frozenColumns = [];
        $filters = [];
        foreach ($this->columns as $column) {
            if (is_string($column)) {
                $column = $this->createColumn($column);
            }
            $field = $column['field'];
            $filter = ArrayHelper::remove($column, 'filter', []);
            if ($filter !== false) {
                $filter['field'] = $field;
                $filters[] = $filter;
            }

            if (ArrayHelper::remove($column, 'frozen')) {
                $frozenColumns[] = $column;
            } else {
                $columns[] = $column;
            }
        }
        $this->clientOptions['columns'] = [$columns];
        $this->clientOptions['frozenColumns'] = [$frozenColumns];
        if ($this->enableFilter) {
            $this->clientOptions['filterRules'] = $filters;
        }
    }

    protected function createColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute" or "attribute:label"');
        }
        return[
            'field' => $matches[1],
            'title' => isset($matches[3]) ? $matches[3] : $matches[1],
        ];
    }
}