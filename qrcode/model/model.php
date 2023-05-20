<?php
/**
 * Connect to the database.
 *
 * @param string $dbType The type of the database.
 * @param string $host The host of the database.
 * @param int $port The port of the database.
 * @param string $dbname The name of the database.
 * @param string $user The user for the database.
 * @param string $password The password for the database.
 *
 * @return PDO The PDO instance.
 */
function connectToDatabase($dbType, $host, $port, $dbname, $user, $password) {
    if (empty($dbType) || empty($host) || empty($dbname) || empty($user) || empty($password)) {
        die("Error: Missing required parameters for connectToDatabase function.");
    }

    $dsn = "";
    if ($dbType == "pgsql") {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    } else {
        die("Error: Unsupported database type.");
    }

    try {
        $pdo = new PDO($dsn, $user, $password);
        return $pdo;
    } catch (PDOException $e) {
        echo "Error connecting to $dbType: " . $e->getMessage();
    }
}

$dbType = "pgsql";
$host = "xxx.xxx.xxx.xxx";
$port = 5432;
$dbname = "dataBase";
$user = "user";
$password = "pass";

$pdo_pgsql = connectToDatabase($dbType, $host, $port, $dbname, $user, $password);

$sql = "
    SELECT 
        emp.emp_id AS ID, 
        emp.emp_department AS department, 
        CONCAT(emp.emp_id, ' - ', emp.emp_fname, ' ', emp.emp_lname, ' (', emp.emp_nname, ')') AS title,
        emp.emp_department,
        CASE
            WHEN zts.zone_name IS NOT NULL THEN CONCAT(zts.zone_name, ' (TS)') 
            WHEN zit.zone_name IS NOT NULL THEN CONCAT(zit.zone_name, ' (IT)')
            ELSE 'ไม่มีกลุ่มโซน' 
        END AS zone_name 
    FROM sys_plan_config_employee AS emp
    LEFT JOIN sys_plan_config_zone_ts AS zts ON zts.unid = emp.emp_zone AND emp.emp_department = 'TS'
    LEFT JOIN sys_plan_config_zone_it AS zit ON zit.unid = emp.emp_zone AND emp.emp_department = 'IT'
    LEFT JOIN sys_plan_job_schedule AS sc ON emp.emp_id = sc.emp_id 
    WHERE emp.status = '1' 
    AND (emp.emp_department = 'TS' OR emp.emp_department = 'IT' OR emp.emp_department = 'ADMIN') 
    GROUP BY 
        emp.emp_id, 
        emp.emp_department, 
        emp.emp_fname, 
        emp.emp_lname, 
        emp.emp_nname, 
        zts.zone_name, 
        zit.zone_name 
    ORDER BY 
        CASE 
            WHEN zts.zone_name IS NOT NULL THEN 1 
            WHEN zit.zone_name IS NOT NULL THEN 1 
            ELSE 2 
        END ASC, 
        zone_name ASC, 
        emp.emp_id ASC
";

$data = array();
try {
    $stmt = $pdo_pgsql->prepare($sql);
    $stmt->bindValue(':status', '1', PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error executing query: " . $e->getMessage();
}
?>