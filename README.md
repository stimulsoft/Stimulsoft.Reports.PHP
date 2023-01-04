# Stimulsoft Reports.PHP

#### Stimulsoft Reports.PHP is a reporting tool designed to create, edit and view reports in the Internet using a client-server technology. The PHP script works on the server side and controls the report generation. The JavaScript report engine works on the client side and provides a universal mechanism for reports generation almost on any client. Fast and powerful report engine, rich and intuitive interface, deployment and licensing.

# Installation
The product is distributed using the [Composer](https://getcomposer.org/) repository. You can add the necessary libraries using the command:

```
composer require stimulsoft/reports-php
```

# Usage
Add the minimum required code to the page:
```php
<?php
require_once 'vendor/autoload.php';
?>
```

Render the necessary scripts in the `<head>` section
```php
<head>
<?php
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Viewer);
    $js->renderHtml();
?>
</head>
```

Create and render a request handler in the `<script>` section of the page:
```php
<script>
<?php
    $handler = new \Stimulsoft\StiHandler();
    $handler->renderHtml();
?>
</script>
```

Create and render the component in the `<script>` section of the page:
```php
<script>
<?php
    $viewer = new \Stimulsoft\Viewer\StiViewer();
    $report = new \Stimulsoft\Report\StiReport();
    $report->loadFile('reports/SimpleList.mrt');
    $viewer->report = $report;
    $viewer->renderHtml();
?>
</script>
```

# Useful links:

[Live Demo](http://demo.stimulsoft.com/#Js)

[Sample Projects](https://github.com/stimulsoft/Samples-JS-PHP)

[Product Page](https://www.stimulsoft.com/en/products/reports-php)

[Free Download](https://www.stimulsoft.com/en/downloads)

[License](LICENSE.md)
