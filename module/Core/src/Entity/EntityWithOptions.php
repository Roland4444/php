<?php

namespace Core\Entity;

/**
 * Сущности с полем options в таблице в который в json можно сохранять boolean опции
 * Trait EntityWithOptions
 *
 * @package Core\Entity
 */
trait EntityWithOptions
{
    /**
     * Проверяет установлена ли опция
     *
     * @param string $optionName
     * @return boolean
     */
    public function hasOption($optionName)
    {
        return is_array($this->options) && in_array($optionName, $this->options);
    }

    /**
     * Устанавливает значение опции
     *
     * @param string $optionName
     * @param boolean $checked
     */
    public function setOption($optionName, $checked)
    {
        if ($this->hasOption($optionName) && ! $checked) {
            $key = array_search($optionName, $this->options);
            if ($key !== false) {
                unset($this->options[$key]);
            }
        } elseif (! $this->hasOption($optionName) && $checked) {
            $this->options[] = $optionName;
        }
    }
}
