<?php

namespace Unirest;

class File
{
    /**
     * Prepares a file for upload. To be used inside the parameters declaration for a request.
     * @param string $filename The file path
     * @param string $mimetype MIME type
     * @param string $postname the file name
     * @return string|\CURLFile
     */
    public static function add($filename, $mimetype = '', $postname = '')
    {
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mimetype = '', $postname = '');
        } else {
            return sprintf('@%s;filename=%s;type=%s', $filename, $postname ?: basename($filename), $mimetype);
        }
    }
}
