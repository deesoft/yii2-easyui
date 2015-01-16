<?php

namespace dee\easyui;

use yii\helpers\Json;

/**
 * Widget
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Widget extends \yii\base\Widget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var array the options for the underlying jQuery UI widget.
     * Please refer to the corresponding jQuery UI widget Web page for possible options.
     * For example, [this page](http://api.jqueryui.com/accordion/) shows
     * how to use the "Accordion" widget and the supported options (e.g. "header").
     */
    public $clientOptions = [];

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * 
     * @return array
     */
    protected function getClientOptions()
    {
        return $this->clientOptions;
    }

    /**
     * Registers a specific jQuery UI widget asset bundle, initializes it with client options and registers related events
     * @param string $plugin the name of the jQuery UI widget
     * @param string $id the ID of the widget. If null, it will use the `id` value of [[options]].
     */
    protected function registerWidget($plugin, $id = null)
    {
        if ($id === null) {
            $id = $this->options['id'];
        }
        $options = $this->getClientOptions();
        if ($options !== false) {
            Easyui::registerPlugin($id, $plugin, $options, $this->getView());
        }
    }
}