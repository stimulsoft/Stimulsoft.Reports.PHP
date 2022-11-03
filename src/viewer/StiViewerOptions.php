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
        $this->toolbar = new StiViewerToolbarOptions("$group.toolbar");
        $this->exports = new StiViewerExportsOptions("$group.exports");
        $this->email = new StiViewerEmailOptions("$group.email");
    }
}

/** A class which controls settings of the viewer toolbar. */
class StiViewerToolbarOptions extends StiComponentOptions
{
    public $visible;
    public $displayMode;
    public $backgroundColor;
    public $borderColor;
    public $fontColor;
    public $fontFamily;
    public $alignment;
    public $showButtonCaptions;
    public $showPrintButton;
    public $showOpenButton;
    public $showSaveButton;
    public $showSendEmailButton;
    public $showFindButton;
    public $showBookmarksButton;
    public $showParametersButton;
    public $showResourcesButton;
    public $showEditorButton;
    public $showFullScreenButton;
    public $showRefreshButton;
    public $showFirstPageButton;
    public $showPreviousPageButton;
    public $showCurrentPageControl;
    public $showNextPageButton;
    public $showLastPageButton;
    public $showZoomButton;
    public $showViewModeButton;
    public $showDesignButton;
    public $showAboutButton;
    public $showPinToolbarButton;
    public $printDestination;
    public $viewMode;
    public $multiPageWidthCount;
    public $multiPageHeightCount;
    public $zoom;
    public $menuAnimation;
    public $showMenuMode;
    public $autoHide;
}

/** A class which controls the export options. */
class StiViewerExportsOptions extends StiComponentOptions
{
    public $storeExportSettings;
    public $showExportDialog;
    public $showExportToDocument;
    public $showExportToPdf;
    public $showExportToHtml;
    public $showExportToHtml5;
    public $showExportToWord2007;
    public $showExportToExcel2007;
    public $showExportToCsv;
    public $showExportToJson;
    public $showExportToText;
    public $showExportToOpenDocumentWriter;
    public $showExportToOpenDocumentCalc;
    public $showExportToPowerPoint;
    public $showExportToImageSvg;
    public $showExportToXps;
}

/** A class which controls the export options. */
class StiViewerEmailOptions extends StiComponentOptions
{
    /** Gets or sets a value which allows to display the Email dialog, or send Email with the default settings. */
    public $showEmailDialog = true;

    /** Gets or sets a value which allows to display the export dialog for Email, or export report for Email with the default settings. */
    public $showExportDialog = true;

    /** Gets or sets the default email address of the message created in the viewer. */
    public $defaultEmailAddress = '';

    /** Gets or sets the default subject of the message created in the viewer. */
    public $defaultEmailSubject = '';

    /** Gets or sets the default text of the message created in the viewer. */
    public $defaultEmailMessage = '';
}