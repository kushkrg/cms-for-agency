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
    /**
     * Generate SQL backup content
     * 
     * @return string The SQL dump content
     */
    public static function generate() {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Use output buffering to capture echo output if needed, but here we build string.
        // For very large DBs, we should stream to file, but for now we'll improve the string building.
        // Increasing memory limit just in case
        ini_set('memory_limit', '512M');
        
        $sql = "-- Evolvcode CMS Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET time_zone = \"+00:00\";\n\n";

        // Get all tables
        $tables = [];
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            $sql .= "-- \n";
            $sql .= "-- Table structure for table `$table`\n";
            $sql .= "-- \n\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            
            $row = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
            $sql .= $row[1] . ";\n\n";

            // Get data
            $result = $pdo->query("SELECT * FROM `$table`");
            
            if ($result->rowCount() > 0) {
                $sql .= "-- \n";
                $sql .= "-- Dumping data for table `$table`\n";
                $sql .= "-- \n\n";
                
                while ($row = $result->fetch(PDO::FETCH_NUM)) {
                    $sql .= "INSERT INTO `$table` VALUES(";
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = "NULL";
                        } else {
                            $values[] = $pdo->quote($value);
                        }
                    }
                    $sql .= implode(',', $values);
                    $sql .= ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        return $sql;
    }

    /**
     * Import SQL backup from file
     * 
     * @param string $filePath Path to SQL file
     * @return bool True on success
     * @throws Exception On failure
     */
    public static function import($filePath) {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        if (!file_exists($filePath)) {
            throw new Exception("Backup file not found.");
        }
        
        $sql = file_get_contents($filePath);
        if (!$sql) {
            throw new Exception("Could not read backup file.");
        }
        
        try {
            // Robust SQL splitter
            $statements = [];
            $len = strlen($sql);
            $current = '';
            $inString = false;
            $escaped = false;
            
            for ($i = 0; $i < $len; $i++) {
                $char = $sql[$i];
                
                // Handle string state
                if ($inString) {
                    if ($escaped) {
                        $escaped = false;
                    } elseif ($char === '\\') {
                        $escaped = true;
                    } elseif ($char === "'") {
                        $inString = false;
                    }
                    $current .= $char;
                    continue;
                }
                
                // Handle normal state
                if ($char === "'") {
                    $inString = true;
                    $current .= $char;
                } elseif ($char === ';') {
                    // Statement end
                    $trim = trim($current);
                    if ($trim !== '') {
                        $statements[] = $trim;
                    }
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
            // Add last statement if any
            $trim = trim($current);
            if ($trim !== '') {
                $statements[] = $trim;
            }
            
            foreach ($statements as $stmt) {
                // Skip comments only statements or empty ones
                if (!empty($stmt)) {
                    $pdo->exec($stmt);
                }
            }
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Import failed: " . $e->getMessage());
        }
    }
}
