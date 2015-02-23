<?php

namespace dee\easyui\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * NavTree
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class NavTree extends Widget
{
    public $items = [];
    public $encodeLabel = false;

    public function renderItem($item, $encode = false)
    {
        if (is_string($item)) {
            return "<li>{$item}</li>";
        } else {
            $encode = ArrayHelper::remove($item, 'encode', $encode);
            $label = ArrayHelper::remove($item, 'label', '');
            if ($encode) {
                $label = Html::encode($label);
            }
            $options = ArrayHelper::remove($item, 'options', []);
            if (isset($item['url'])) {
                $content = Html::a($label, $item['url'], $options);
                unset($item['url']);
            } else {
                $content = Html::tag('span', $label, $options);
            }
            if (isset($item['items'])) {
                $content .= "\n" . Html::tag('ul', $this->renderItems($item['items'], $encode));
                unset($item['items']);
            }
            return Html::tag('li', $content, $item);
        }
    }

    public function renderItems($items, $encode = false)
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = $this->renderItem($item, $encode);
        }
        return implode("\n", $result);
    }

    public function run()
    {
        $this->registerWidget('tree');
        echo Html::tag('ul', $this->renderItems($this->items, $this->encodeLabel), $this->options);
    }
}