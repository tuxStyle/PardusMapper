<?php
declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\MySqlDB;

class DB
{
    /**
     * Get cluster by sector
     *
     * @param string $sector
     * @return object|false|null
     */
    public static function cluster(?string $sector): object|false|null
    {
        $db = MySqlDB::instance();

        if (is_null($sector)) {
            return null;
        }

        $result = $db->execute('SELECT * FROM Pardus_Clusters WHERE c_id = (SELECT c_id FROM Pardus_Sectors WHERE name = ?)', [
            's', $sector
        ]);

        return $result;
    }

    /**
     * Get sector by sector id or name
     *
     * @param integer|null $id
     * @param string|null $sector
     * @return object|false|null
     */
    public static function sector(?int $id = null, ?string $sector = null): object|false|null
    {
        $db = MySqlDB::instance();

        if (is_null($id) && is_null($sector)) {
            return null;
        }

        if($sector) {
            $result = $db->execute('SELECT * FROM Pardus_Sectors WHERE name = ?', [
                's', $sector
            ]);
        } else {
            // TODO: check the code but this should probably be
            // SELECT * FROM Pardus_Sectors WHERE s_id = ?
            // so mysql will not have to scan that many rows

            $result = $db->execute('SELECT * FROM Pardus_Sectors WHERE s_id <= ? ORDER BY s_id DESC LIMIT 1', [
                'i', $id
            ]);
        }

        return $result;
    }
}