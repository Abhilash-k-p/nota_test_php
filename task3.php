<?php

/**
 * use sql/task3_setup_database to create database for task3
 */

final class TableCreator
{
    /**
     * The PDO instance for database connection.
     */
    private $pdo;

    /**
     * Constructor.
     * Automatically calls the create and fill methods to set up the table.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->create();
        $this->fill();
    }

    /**
     * Creates the table `Test` with specified fields.
     *
     * @return void
     */
    private function create()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS Test (
                id INT AUTO_INCREMENT PRIMARY KEY,
                script_name VARCHAR(25) NOT NULL,
                start_time DATETIME NOT NULL,
                end_time DATETIME NOT NULL,
                result ENUM('normal', 'illegal', 'failed', 'success') NOT NULL
            )
        ";

        try {
            $this->pdo->exec($sql);
        } catch (\PDOException $e) {
            echo 'Table creation failed: ' . $e->getMessage();
        }
    }

    /**
     * Fills the table `Test` with random data.
     *
     * @return void
     */
    private function fill()
    {
        $results = ['normal', 'illegal', 'failed', 'success'];

        for ($i = 0; $i < 10; $i++) {
            $script_name = 'script_name_' . $i;
            $start_time = date('Y-m-d H:i:s', strtotime('-' . rand(1, 100) . ' days'));
            $end_time = date('Y-m-d H:i:s', strtotime($start_time . ' + ' . rand(1, 5) . ' hours'));
            $result = $results[array_rand($results)];

            $sql = "
                INSERT INTO Test (script_name, start_time, end_time, result) 
                VALUES (:script_name, :start_time, :end_time, :result)
            ";

            try {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':script_name' => $script_name,
                    ':start_time' => $start_time,
                    ':end_time' => $end_time,
                    ':result' => $result
                ]);
            } catch (\PDOException $e) {
                echo 'Data insertion failed: ' . $e->getMessage();
            }
        }
    }

    /**
     * Retrieves data from the table `Test` where result is either 'normal' or 'success'.
     *
     * @return array
     */
    public function get()
    {
        $sql = "
            SELECT * FROM Test 
            WHERE result IN ('normal', 'success')
        ";

        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo 'Data retrieval failed: ' . $e->getMessage();
            return [];
        }
    }
}

// Example usage:
// Replace the following with your actual database connection details.
$dsn = 'mysql:host=host.docker.internal:33057;dbname=nota_test_task3;charset=utf8';
$username = 'root';
$password = 'secret';

try {
    $pdo = new \PDO($dsn, $username, $password);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $tableCreator = new TableCreator($pdo);
    $data = $tableCreator->get();
    print_r($data);
} catch (\PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
