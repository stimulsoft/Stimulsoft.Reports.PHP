<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the viewer. */
class StiViewerOptions extends StiComponentOptions
{
    /** A class which controls settings of the viewer appearance. */
    public $appearance;

    /** A class which controls settings of the viewer toolbar. */
    public $toolbar;

    /** A class which controls the export options. */
    public $exports;

    /** A class which controls the export options. */
    public $email;

    /** Gets or sets the width of the viewer. */
    public $width = '100%';

    /** Gets or sets the height of the viewer. */
    public $height = '';

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        if (strpos($this->property, '.') > 0)
            return parent::getHtml();

        return "let $this->property = new Stimulsoft.Viewer.StiViewerOptions();\n" . parent::getHtml();
    }

    public function __construct($property = 'viewerOptions')
    {
        parent::__construct($property);

        $this->appearance = new StiAppearanceOptions("$property.appearance");
        $this->toolbar = new StiToolbarOptions("$property.toolbar");
        $this->exports = new StiExportsOptions("$property.exports");
        $this->email = new StiEmailOptions("$property.email");
    }
}