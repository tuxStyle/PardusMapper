<?php

declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\Settings;

class CORS
{
    public const PARDUS = 'pardus';
    public const MAPPER = 'mapper';
    
    /**
     * Allow access only from pardus.at with extended headers
     *
     * @return void
     */
    public static function pardus(): void
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && preg_match('/^https?:\/\/(orion|artemis|pegasus)?\.pardus\.at$/i', (string) $_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);  // Dynamically allow the origin
        } else {
            http_response(true, ApiResponse::FORBIDDEN, 'CORS policy does not allow access from this origin.');
        }
    }

    /**
     * Allow access only from pardus.at with extended headers
     *
     * @return void
     */
    public static function pardus_extended(): void
    {
        self::pardus();

        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');         // Allow the necessary methods
        header('Access-Control-Allow-Headers: Content-Type');               // Allow custom headers (if necessary)
        header('Access-Control-Allow-Credentials: true');                   // Allow cookies (if necessary)

        // Handle OPTIONS requests for CORS preflight (important for complex requests)
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0); // Return 200 OK for preflight requests
        }
    }

    public static function mapper(): void
    {
        header('Access-Control-Allow-Origin: ' . Settings::$BASE_URL);
    }
}
