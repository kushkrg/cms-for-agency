<?php
/**
 * Evolvcode CMS - Database Backup Class
 * 
 * Generates SQL dump of the MySQL database.
 */

class Backup {

    /**
     * Generate SQL backup content
     * 
     * @return string The SQL dump content
     */
    public static function generate() {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $sql = "-- Evolvcode CMS Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Get all tables
        $tables = [];
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            $sql .= "-- Table structure for `$table`\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            
            $row = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            $sql .= $row[1] . ";\n\n";

            // Get data
            $result = $pdo->query("SELECT * FROM `$table`");
            $numFields = $result->columnCount();

            if ($result->rowCount() > 0) {
                $sql .= "-- Dumping data for table `$table`\n";
                $sql .= "INSERT INTO `$table` VALUES\n";
                
                $rows = [];
                while ($row = $result->fetch(PDO::FETCH_NUM)) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = "NULL";
                        } else {
                            $values[] = $pdo->quote($value);
                        }
                    }
                    $rows[] = "(" . implode(',', $values) . ")";
                }
                $sql .= implode(",\n", $rows) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        return $sql;
    }
}
