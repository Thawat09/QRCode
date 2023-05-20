<?php

function connectToDatabase($dbType, $host, $port, $dbname, $user, $password)
{
    $dsn = $dbType == "pgsql" 
        ? "pgsql:host=$host;port=$port;dbname=$dbname"
        : "sqlsrv:Server=$host" . ($port ? ",$port" : "") . ";Database=$dbname";

    try {
        return new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        error_log("Error connecting to $dbType: " . $e->getMessage()); // Log error instead of echo
        throw $e; // Re-throw the exception for further handling
    }
}

$dbConfig = parse_ini_file('dbConfig.ini'); // Assume that you store db configs in dbConfig.ini file

$pdo_pgsql = connectToDatabase(
    "pgsql", 
    $dbConfig['pgsql_host'], 
    $dbConfig['pgsql_port'], 
    $dbConfig['pgsql_dbname'], 
    $dbConfig['pgsql_user'], 
    $dbConfig['pgsql_password']
);

$pdo_sqlsrv = connectToDatabase(
    "sqlsrv", 
    $dbConfig['sqlsrv_host'], 
    $dbConfig['sqlsrv_port'], 
    $dbConfig['sqlsrv_dbname'], 
    $dbConfig['sqlsrv_user'], 
    $dbConfig['sqlsrv_password']
);

?>