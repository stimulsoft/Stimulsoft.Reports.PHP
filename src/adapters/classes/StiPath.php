<?php

namespace Stimulsoft;

class StiPath
{

### Fields

    public $filePath;
    public $directoryPath;
    public $fileName;
    public $fileNameOnly;
    public $fileExtension;


### Helpers

    public static function getVendorPath()
    {
        return realpath(__DIR__ . "/../../../..");
    }

    public static function normalize($path, $checkFileNames = true): string
    {
        $result = str_replace('\\', '/', $path ?? '');
        $result = $checkFileNames ? preg_replace(['~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~'], ['/', '/', '', ''], $result) : $result;
        $normalized = explode('?', $result)[0];
        return str_replace('/', DIRECTORY_SEPARATOR, $normalized);
    }

    private static function isUrl($path): bool
    {
        return StiFunctions::startsWith($path, "http://") || StiFunctions::startsWith($path, "https://");
    }

    private static function getMissingFileName($filePath, $checkFileNames): string
    {
        $filePath = StiPath::normalize($filePath, $checkFileNames);
        return basename($filePath);
    }

    private static function getRealFilePath($filePath, $checkFileNames)
    {
        if (StiPath::isUrl($filePath)) {
            $headers = get_headers($filePath);
            return stripos($headers[0],"200 OK") ? $filePath : null;
        }

        $filePath = StiPath::normalize($filePath, $checkFileNames);
        if (is_file($filePath))
            return $filePath;

        $filePath = StiPath::normalize(getcwd() . '/' . $filePath, $checkFileNames);
        if (is_file($filePath))
            return $filePath;

        return null;
    }

    private static function getRealDirectoryPath($directoryPath, $checkFileNames)
    {
        if (StiPath::isUrl($directoryPath))
            return null;

        $filePath = StiPath::normalize($directoryPath, $checkFileNames);

        $directoryPath = $filePath;
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = StiPath::normalize(getcwd() . '/' . $directoryPath, $checkFileNames);
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = dirname($filePath);
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = StiPath::normalize(getcwd() . '/' . $directoryPath, $checkFileNames);
        if (is_dir($directoryPath))
            return $directoryPath;

        return null;
    }


### Constructor

    public function __construct($filePath, $checkFileNames = true)
    {
        $this->filePath = self::getRealFilePath($filePath, $checkFileNames);
        $this->directoryPath = self::getRealDirectoryPath($filePath, $checkFileNames);

        $this->fileName = $this->filePath !== null ? basename($this->filePath) : self::getMissingFileName($filePath, $checkFileNames);
        if ($this->filePath === null && StiFunctions::endsWith($this->directoryPath, $this->fileName))
            $this->fileName = null;

        if ($this->fileName !== null) {
            $pathInfo = pathinfo($this->fileName);
            $this->fileNameOnly = strlen($pathInfo['filename'] ?? '') > 0 ? $pathInfo['filename'] : null;
            $this->fileExtension = strlen($pathInfo['extension'] ?? '') > 0 ? $pathInfo['extension'] : null;
        }
    }
}