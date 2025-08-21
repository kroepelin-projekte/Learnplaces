<?php

declare(strict_types=1);

namespace KPG\Learnplaces\util;

use JetBrains\PhpStorm\NoReturn;

class VideoStream
{
    private string $path;
    private $stream;
    private int $buffer = 8192;

    public function __construct(string $file_path)
    {
        $this->path = $file_path;
    }

    #[NoReturn]
    public function start(): void
    {
        if (!is_file($this->path) || !is_readable($this->path)) {
            http_response_code(404);
            exit('File not found');
        }

        $size   = filesize($this->path);
        $start  = 0;
        $end    = $size - 1;
        $length = $size;

        if (isset($_SERVER['HTTP_RANGE'])) {
            // Beispiel: Range: bytes=500-1000
            if (preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
                if ($matches[1] !== '') {
                    $start = intval($matches[1]);
                }
                if ($matches[2] !== '') {
                    $end = intval($matches[2]);
                }
                $length = $end - $start + 1;
                header('HTTP/1.1 206 Partial Content');
                header("Content-Range: bytes $start-$end/$size");
            }
        } else {
            header('HTTP/1.1 200 OK');
        }

        header('Content-Type: video/mp4');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $length);

        $this->stream = fopen($this->path, 'rb');
        fseek($this->stream, $start);

        $bytesSent = 0;
        while (!feof($this->stream) && $bytesSent < $length) {
            $read = min($this->buffer, $length - $bytesSent);
            echo fread($this->stream, $read);
            $bytesSent += $read;
            @ob_flush();
            flush();
        }

        fclose($this->stream);
        exit;
    }
}