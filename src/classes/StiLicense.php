<?php

namespace Stimulsoft;

class StiLicense
{
    public $isHtmlRendered = false;

    public $key;
    public $file;

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = '';

        $this->isHtmlRendered = true;
        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml()
    {
        echo $this->getHtml();
    }
}