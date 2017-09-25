<?php

namespace Xuchen\ApiConverter;

/**
 * 转化器抽象类
 * Class AbstractConverter
 * @package Xuchen\ApiConverter
 */
abstract class AbstractConverter
{
    /**
     * @var array 模板类的实例化
     */
    protected $templates = [];

    /**
     * @var string 当前使用的模板类
     */
    protected $current_template = '';

    /**
     * 转化接口数据
     * @return mixed
     */
    abstract public function convert();

    /**
     * 使用模板
     * @param $template
     * @return $this
     * @throws \ErrorException
     */
    public function useTemplate($template)
    {
        if (!class_exists($template)) {
            throw new \ErrorException('Template Class ' . $template . ' Not Found.');
        }

        $this->current_template = $template;
        if (isset($this->templates[$template])) {
            return $this;
        }

        $this->templates[$template] = new $template;
        if (!is_subclass_of($this->templates[$template], 'Xuchen\AbstractTemplate')) {
            throw new \ErrorException('Wrong Template Class ' . $template . '.');
        }

        return $this;
    }

    /**
     * 获取当前正在使用的模板
     * @return string
     */
    public function getCurrentTemplate()
    {
        return $this->current_template;
    }
}
