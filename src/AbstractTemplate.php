<?php

namespace Xuchen\ApiConverter;

abstract class AbstractTemplate
{
    const TPL_MODE_ALL = 0;
    const TPL_MODE_WITH_FIELDS = 1;
    const TPL_MODE_WITHOUT_FIELDS = 2;

    /**
     * @var int 模板模式
     */
    protected $_template_mode = self::TPL_MODE_WITH_FIELDS;

    /**
     * @var array 需要执行的字段
     */
    protected $_fields = [];

    /**
     * 模板
     * @return mixed
     */
    abstract function template();

    /**
     * 处理模板的方法
     * @param $fields
     * @return mixed
     */
    abstract function execute($fields);

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
}
