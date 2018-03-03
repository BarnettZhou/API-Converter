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
     * 使用到的模板字段
     * @var array
     */
    protected $_fields = [];

    /**
     * 模板模式
     * @var int
     */
    protected $_template_mode = self::TPL_MODE_WITH_FIELDS;

    /**
     * 使用所有的模板字段
     */
    const TPL_MODE_ALL = 0;

    /**
     * 使用指定的模板字段
     */
    const TPL_MODE_WITH_FIELDS = 1;

    /**
     * 使用指定的字段之外的模板字段
     */
    const TPL_MODE_WITHOUT_FIELDS = 2;

    /**
     * 设置模板的模式
     * @param int $mode
     * @return $this
     * @throws \ErrorException
     */
    public function setTemplateMode($mode = self::TPL_MODE_WITH_FIELDS)
    {
        if (!in_array($mode, [0, 1, 2])) {
            throw new \ErrorException('Wrong Template Mode.', 500);
        }

        $this->_template_mode = $mode;
        return $this;
    }

    /**
     * 设置Fields
     * @param array $fields
     * @return $this
     */
    public function setFields($fields = [])
    {
        if ($this->_template_mode == self::TPL_MODE_ALL) {
            $this->_fields = array_keys($this->template());
        } else if ($this->_template_mode == self::TPL_MODE_WITHOUT_FIELDS) {
            $this->_fields = array_diff(array_keys($this->template()), $fields);
        } else {
            $this->_fields = $fields;
        }
        return $this;
    }

    /**
     * 获取模板Fields
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * 转化接口数据前的回调方法
     * @return mixed
     */
    abstract protected function beforeConvert();

    /**
     * 转化数据后的回调方法
     * @return mixed
     */
    abstract protected function afterConverter();

    /**
     * 模板
     * @return mixed
     */
    abstract protected function template();

    /**
     * 转化接口数据
     * @return mixed
     */
    abstract public function convert();

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
