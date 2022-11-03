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

    public function __toString()
    {
        return "let $this->group = new Stimulsoft.Viewer.StiViewerOptions();\n" . parent::__toString();
    }

    public function __construct($group = 'viewerOptions')
    {
        parent::__construct($group);

        $this->appearance = new StiAppearanceOptions("$group.appearance");
        $this->toolbar = new StiToolbarOptions("$group.toolbar");
        $this->exports = new StiExportsOptions("$group.exports");
        $this->email = new StiEmailOptions("$group.email");
    }
}