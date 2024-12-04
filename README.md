# Stimulsoft Reports.PHP

#### Stimulsoft Reports.PHP is a report generator intended to create, view, print, and export reports online using client-server technology. The Stimulsoft report generator for PHP is a fast and powerful JavaScript report engine, rich and intuitive interface, simple integration and deployment process in PHP applications, and an easy and understandable licensing model.

# Installation
You can add the necessary libraries using the command:
```
composer require stimulsoft/reports-php
```

# Usage
To work with the report generator, use the following code:
```php
<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Report\StiReport;

$report = new StiReport();
$report->process();
$report->loadFile('reports/SimpleList.mrt');
$report->render();
$report->printHtml();
?>
```

To work with the report viewer, use the following code:
```php
<?php
require_once 'vendor/autoload.php';
 
use Stimulsoft\Report\StiReport;
use Stimulsoft\Viewer\StiViewer;
 
$viewer = new StiViewer();
$viewer->process();
 
$report = new StiReport();
$report->loadFile('reports/SimpleList.mrt'); 
$viewer->report = $report;
 
$viewer->printHtml();
?>
```

To work with the report designer, use the following code:
```php
<?php
require_once 'vendor/autoload.php';
 
use Stimulsoft\Report\StiReport;
use Stimulsoft\Designer\StiDesigner;
 
$designer = new StiDesigner();
$designer->process();
 
$report = new StiReport();
$report->loadFile('reports/SimpleList.mrt'); 
$designer->report = $report;
 
$designer->printHtml();
?>
```

These code examples are basic. There are many features, options, and other variations. For details, see our examples and documentation. For more details, please see our [examples](https://github.com/stimulsoft/Samples-Reports.PHP) and [documentation](https://www.stimulsoft.com/en/documentation/online/programming-manual/reports_and_dashboards_for_php.htm).

# Useful links:
* [Live Demo](http://demo.stimulsoft.com/#Js)
* [Product Page](https://www.stimulsoft.com/en/products/reports-php)
* [Sample Projects](https://github.com/stimulsoft/Samples-Reports.PHP)
* [Documentation](https://www.stimulsoft.com/en/documentation/online/programming-manual/reports_and_dashboards_for_php.htm)
* [Free Download](https://www.stimulsoft.com/en/downloads)
* [License](LICENSE.md)
