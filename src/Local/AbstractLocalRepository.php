<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Repository\Local;

use PDO;
use PDOException;

/**
 * Provides a base class for local database operations using PDO.
 * Implements common CRUD operations (query, queryAll, insert, update, delete)
 * as well as a method to prepare and execute SQL statements.
 *
 * Handles the PDO connection for local database environments.
 * The default configuration is set for a XAMPP environment, with an alternative
 * configuration for DDEV provided in commented code.
 *
 * @package Repository\Local
 */
abstract class AbstractLocalRepository
{
    protected ?PDO $pdo = null;
    protected string $sql = '';

    /**
     * Initializes the PDO connection for local database access.
     *
     * Attempts to create a new PDO instance using configuration parameters.
     * If the connection fails, the exception message is output.
     *
     * @return void
     */
    public function __construct()
    {
        // DDEV configuration
        // try {
        //     $dbhost = 'db';
        //     $dbuser = 'root';
        //     $dbpass = 'root';
        //     $dbname = 'db';

        //     $this->pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        //     $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // } catch (PDOException $ex) {
        //     echo $ex->getMessage();
        // }

        // XAMPP configuration (uncomment if needed)
        try {
            $dbhost = 'localhost';
            $dbuser = 'root';
            $dbpass = '';
            $dbname = 'db';

            $this->pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Prepares and executes the SQL statement with the provided parameters.
     * Binds the given parameters, and then executes the statement.
     *
     * @param string $sql The SQL statement to be executed.
     * @param array $params Optional array of parameters to bind to the SQL statement.
     * @return \PDOStatement The resulting PDO statement after execution.
     */
    protected function executeStatement(string $sql, array $params = []): \PDOStatement
    {
        $this->sql = $sql;
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Executes a query and returns the first row as an associative array.
     * Returns null if no rows are found.
     *
     * @param string $sql The SQL query to be executed.
     * @param array $params Optional array of parameters to bind to the query.
     * @return array{string: mixed}|null
     */
    public function query(string $sql, array $params = []): ?array
    {
        $stmt = $this->executeStatement($sql, $params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Executes a query and returns all rows as an associative array.
     * Returns null if no rows are found.
     *
     * @param string $sql The SQL query to be executed.
     * @param array $params Optional array of parameters to bind to the query.
     * @return array{int: array{string: mixed}}|null
     */
    public function queryAll(string $sql, array $params = []): ?array
    {
        $stmt = $this->executeStatement($sql, $params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows ?: null;
    }

    /**
     * Executes an INSERT query using the provided SQL and parameters.
     * If the insert is successful, the last inserted ID is returned.
     * Otherwise, null is returned.
     *
     * @param string $sql The SQL INSERT statement to be executed.
     * @param array $params Optional array of parameters to bind to the statement.
     * @return int|null The ID of the last inserted row, or null if the insert failed.
     */
    public function insert(string $sql, array $params = []): ?int
    {
        $stmt = $this->executeStatement($sql, $params);
        if ($stmt->rowCount() > 0) {
            return (int) $this->pdo->lastInsertId();
        }
        return null;
    }

    /**
     * Executes an UPDATE statement and returns the number of affected rows.
     * Returns the number of rows that were modified.
     *
     * @param string $sql The SQL UPDATE statement to be executed.
     * @param array $params Optional array of parameters to bind to the statement.
     * @return int The number of rows affected by the update.
     */
    public function update(string $sql, array $params = []): int
    {
        $stmt = $this->executeStatement($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Executes a DELETE statement and returns the number of affected rows.
     * Returns the number of rows that were deleted.
     *
     * @param string $sql The SQL DELETE statement to be executed.
     * @param array $params Optional array of parameters to bind to the statement.
     * @return int The number of rows deleted.
     */
    public function delete(string $sql, array $params = []): int
    {
        $stmt = $this->executeStatement($sql, $params);
        return $stmt->rowCount();
    }
}