<?php

namespace dee\easyui\widgets;

use dee\easyui\helpers\Easyui;
use yii\web\JsExpression;

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
     * @var array 
     */
    public $events = [];

    /**
     * @var array 
     */
    protected $properties = [];

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
        foreach ($this->properties as $key => $value) {
            if (!is_int($key) && !array_key_exists($key, $this->clientOptions)) {
                $this->clientOptions[$key] = $value;
            }
        }
        foreach ($this->events as $name => $value) {
            if (!$value instanceof JsExpression) {
                $value = new JsExpression($value);
            }
            $this->clientOptions[$name] = $value;
        }
        return $this->clientOptions;
    }

    /**
     * Registers a specific jQuery UI widget asset bundle, initializes it with client options and registers related events
     * @param string $plugin the name of the jQuery UI widget
     * @param string $id the ID of the widget. If null, it will use the `id` value of [[options]].
     */
    protected function registerWidget($plugin)
    {
        $clientOptions = $this->getClientOptions();
        if ($clientOptions !== false) {
            Easyui::registerPlugin($this->options, $plugin, $clientOptions, $this->getView());
        }
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->properties) || in_array($name, $this->properties)) {
            return array_key_exists($name, $this->properties) ? $this->properties[$name] : null;
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->properties) || in_array($name, $this->properties)) {
            $this->properties[$name] = $value;
            foreach ($this->properties as $i => $v) {
                if (is_int($i) && $v === $name) {
                    unset($this->properties[$i]);
                }
            }
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->properties)) {
            unset($this->properties[$name]);
            $this->properties[] = $name;
        } elseif (!in_array($name, $this->properties)) {
            parent::__unset($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->properties) || in_array($name, $this->properties)) {
            return isset($this->properties[$name]);
        } else {
            parent::__isset($name, $value);
        }
    }
}