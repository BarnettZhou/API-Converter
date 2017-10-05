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

    /**
     * 获取一行数据中的值
     * @param $row
     * @param $key
     * @param null $default
     * @param string $convert_method
     * @return null
     */
    public function getItem($row, $key, $default = null, $convert_method = '')
    {
        if (isset($row[$key])) {
            return $row[$key];
        } else {
            $result = $default;
        }

        if (in_array($convert_method, ['intval', 'strval', 'floatval'])) {
            $result = $convert_method($result);
        }
        return $result;
    }

    /**
     * 递归方式获取一行数据中的值
     * @param $row
     * @param $key
     * @param null $default
     * @param string $convert_method
     * @return null
     */
    public function getItemRecursively($row, $key, $default = null, $convert_method = '')
    {
        $key_arr = explode('.', $key);
        if (count($key_arr) > 1) {
            $parent_key = $key_arr[0];
            if (isset($row[$parent_key])) {
                unset($key_arr[0]);
                $child_key = implode('.', $key_arr);
                return $this->getItemRecursively($row[$parent_key], $child_key, $default, $convert_method);
            } else {
                return $default;
            }
        } else {
            return $this->getItem($row, $key, $default, $convert_method);
        }
    }
}
