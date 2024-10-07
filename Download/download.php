<?php

declare(strict_types=1);

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;

$dl = Request::dl();
http_response(is_null($dl), ApiResponse::OK, 'invalid download type requested');

if ($dl === 0) {

    /* File we want to send to the browser */
    $filename = "zaqmapper.xpi";

    /**
     * The following header is required for browsers that do not
     * recognize the xpi extension. i.e all browsers other than Firefox.
     * This will display the familiar 'save/open' dialog if the xpi
     * extension is not supported.
     */
    header("Content-Disposition: filename={$filename}");

    /* Tell the browser that the content that is coming is an xpinstall */
    header('Content-type: application/x-xpinstall');

    /* Also send the content length */
    header('Content-Length: ' . filesize($filename));

    /* readfile reads the file content and echos it to the output */
    readfile($filename);
} elseif ($dl === 1) {
    // Set Univers Variable
    $uni = Request::uni();
    http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

    // Session Name
    session_name($uni);

    // Start the Session
    session_start();

    require_once(templates('download'));
}
