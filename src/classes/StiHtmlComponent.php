<?php

namespace Stimulsoft;

class StiHtmlComponent
{
    public $id;
    public $isHtmlRendered = false;

    protected function getEventHtml($event, $callback = false, $prevent = false)
    {
        $eventValue = $this->{$event} === true ? '' : $this->{$event} . '(args); ';
        $callbackValue = $callback ? ', callback' : '';
        $preventValue = $prevent ? 'args.preventDefault = true; ' : '';
        return "$this->id.$event = function (args$callbackValue) { {$preventValue}{$eventValue}Stimulsoft.Helper.process(args$callbackValue); }\n";
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        return '';
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml()
    {
        echo $this->getHtml();
    }
}