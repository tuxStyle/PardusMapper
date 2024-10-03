<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

class Settings
{
    public const UNIVERSE = ['Orion', 'Artemis', 'Pegasus'];
    public const CLUSTERS = [
        'CORE', 
        'EKC', 'EPR', 'ESC', 'EWS', 'FHC', 'FRC', 'FSH', 'GAP', 'LANE', 'NPR', 
        'PEC', 'PFC', 'PUC', 'SPLIT', 'SPR', 'UKC', 'UNR', 'URC', 'WPR'
    ];
    public const MISSIONS_LIST = [
        "All",
        "Assassination","Transport Packages","VIP Action Trip","Transport VIP","Transport Explosives",
        "Clean Wormhole Exit","Plant Explosives","Spying","Deception","Smuggle Body Parts",
        "Transport Military Explosives","Scout","Espionage"
    ];
    
    public static ?int $TESTING = 0;
    public static ?int $DEBUG = 0;
    public static ?int $SHOW_EXCEPTIONS = 0;

    public static ?string $DB_SERVER = null;
    public static ?string $DB_USER = null;
    public static ?string $DB_PWD = null;
    public static ?string $DB_NAME = null;
    public static ?int $DB_TOTAL_USERS = null;

    public static ?string $IMG_DIR = null;
    public static ?string $IMG_DIR_MAPPER = null;
    public static ?string $BASE_URL = null;
    public static ?string $URL = null;

    /**
     * Undocumented function
     */
    public static function init()
    {
        self::$TESTING = (int)env('TESTING');
        self::$DEBUG = (int)env('DEBUG');
        self::$SHOW_EXCEPTIONS = (int)env('SHOW_EXCEPTIONS');

        self::$DB_SERVER = env('DB_SERVER');
        self::$DB_USER = env('DB_USER');
        self::$DB_PWD = env('DB_PASS');
        self::$DB_NAME = env('DB_NAME');
        self::$DB_TOTAL_USERS = (int)env('DB_TOTAL_USERS');

        self::$IMG_DIR = env('IMG_DIR');
        self::$IMG_DIR_MAPPER =  env('IMG_DIR_MAPPER');
        self::$BASE_URL = env('BASE_URL');
        self::$URL = env('URL');
    }
}
