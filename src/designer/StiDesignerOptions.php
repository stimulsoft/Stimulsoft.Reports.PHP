<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponentOptions;
use Stimulsoft\Viewer\StiViewerOptions;

class StiDesignerOptions extends StiComponentOptions
{
    /** A class which controls settings of the designer appearance. */
    public $appearance;

    /** A class which controls settings of the designer toolbar. */
    public $toolbar;

    /** A class which controls settings of the bands. */
    public $bands;

    /** A class which controls settings of the cross-bands. */
    public $crossBands;

    /** A class which controls settings of the components. */
    public $components;

    /** A class which controls settings of the dashboardElements. */
    public $dashboardElements;

    /** A class which controls settings of the dictionary. */
    public $dictionary;

    /** Gets or sets the width of the designer. */
    public $width = "100%";

    /** Gets or sets the height of the designer. */
    public $height = "800px";

    /** A class which controls settings of the preview window. */
    public $viewerOptions;

    public function getHtml()
    {
        return "let $this->property = new Stimulsoft.Designer.StiDesignerOptions();\n" . parent::getHtml();
    }

    public function __construct($property = 'designerOptions')
    {
        parent::__construct($property);

        $this->appearance = new StiAppearanceOptions("$property.appearance");
        $this->toolbar = new StiToolbarOptions("$property.toolbar");
        $this->bands = new StiBandsOptions("$property.bands");
        $this->crossBands = new StiCrossBandsOptions("$property.crossBands");
        $this->components = new StiComponentOptions("$property.components");
        $this->dashboardElements = new StiDashboardElementsOptions("$property.dashboardElements");
        $this->dictionary = new StiDictionaryOptions("$property.dictionary");
        $this->viewerOptions = new StiViewerOptions("$property.viewerOptions");
    }
}