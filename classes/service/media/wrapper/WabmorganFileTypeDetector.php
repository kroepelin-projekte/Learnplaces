<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\media\wrapper;

use RuntimeException;
use wapmorgan\FileTypeDetector\Detector;

/**
 * Class WabmorganFileTypeDetector
 *
 * @package KPG\Learnplaces\service\media\wrapper
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class WabmorganFileTypeDetector implements FileTypeDetector
{
    /**
     * @inheritDoc
     */
    public function detectByFilename(string $filename): array
    {
        $result = Detector::detectByFilename($filename);
        if($result === false) {
            throw new RuntimeException("The detection by file name has failed for name \"$filename\"");
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function detectByContent(string $pathToFile): array
    {
        $result = Detector::detectByContent($pathToFile);
        if($result === false) {
            throw new RuntimeException("The detection by file content has failed for the file \"$pathToFile\"");
        }

        return $result;
    }
}
