<?php

namespace Stimulsoft;

class StiComponentOptions
{
    public $property;
    protected $enums = [];

    public function __toString()
    {
        $result = '<script type="text/javascript">';
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
                        $result .= "$this->property.$name = $stringValue;\n";
                    }
                }
            }
        }

        return $result . '</script>';
    }

    public function __construct($property = '')
    {
        $this->property = $property;
    }
}