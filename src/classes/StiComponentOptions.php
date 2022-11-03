<?php

namespace Stimulsoft;

class StiComponentOptions
{
    protected $group;
    protected $enums = [];

    public function __toString()
    {
        $result = '';
        $className = get_class($this);
        $vars = get_class_vars($className);
        foreach ($vars as $name => $defaultValue) {
            if ($name != 'group' && $name != 'enums') {
                if (is_object($this->{$name}))
                    $result .= $this->{$name};
                else {
                    $currentValue = $this->{$name};
                    if ($currentValue != $defaultValue) {
                        $stringValue = in_array($name, $this->enums) ? $currentValue : var_export($currentValue, true);
                        if ($stringValue == 'NULL') $stringValue = 'null';
                        $result .= "$this->group.$name = $stringValue;\n";
                    }
                }
            }
        }

        return $result;
    }

    public function __construct($group = '')
    {
        $this->group = $group;
    }
}