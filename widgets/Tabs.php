<?php

namespace dee\easyui\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Tab
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Tabs extends Widget
{
    /**
     * @var array list of tabs in the tabs widget. Each array element represents a single
     * tab with the following structure:
     *
     * - title: string, the tab header label.
     * - content: string, optional, the content (HTML) of the tab pane.
     * - options: array, optional, the HTML attributes of the tab pane container.
     * - selected: boolean, optional, whether the item tab header and pane should be visible or not.
     * - href: array|string
     */
    public $items = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerWidget('tabs');
        echo Html::beginTag('div', $this->options)."\n";
        $this->renderItems();
    }

    protected function renderItems()
    {
        $view = $this->getView();
        foreach ($this->items as $item) {
            if (isset($item['href'])) {
                $item['href'] = Url::to($item['href']);
            }
            if (isset($item['view'])) {
                $content = $view->render($item['view'], ArrayHelper::remove($item, 'viewParams', []));
                unset($item['view']);
            }
            if (!isset($content)) {
                $content = ArrayHelper::remove($item, 'content', '');
            }
            $options = ArrayHelper::remove($item, 'options', []);

            $options['data']['options'] = $item;
            echo Html::tag('div', $content, $options)."\n";
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag('div');
    }
}