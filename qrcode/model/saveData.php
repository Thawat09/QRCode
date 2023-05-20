<?php
header('Content-Type: application/json');

// Receive JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Database configuration
$host        = "xxx.xxx.xxx.xxx";
$port        = 5432;
$dbname      = "dataBase";
$user        = "user";
$password    = "pass";

try {
    // Connect to the database
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Escape input data
    $technician = $pdo->quote($data['technician']);
    $formattedDate = $pdo->quote($data['formattedDate']);
    $uploadedImageNames = [];

    foreach ($data['uploadedImages'] as $uploadedImage) {
        $uploadedImageNames[] = $uploadedImage;
    }

    // Insert each job into the database
    foreach ($data['selectedJobs'] as $job) {
        $unid = $pdo->quote($job['unid']);
        $job_id = $pdo->quote($job['job']);
        $so = $pdo->quote($job['so']);
        $productCode = $pdo->quote($job['productCode']);
        $productName = $pdo->quote($job['productName']);
        $quantity = $pdo->quote($job['quantity']);
        $imageNames = $pdo->quote(json_encode($uploadedImageNames));

        // Construct SQL query
        $query = "
            INSERT INTO sys_plan_job_schedule_confirm
                (date, confirm_by, ref_job_main_unid, job_id, ref_doc_so, mat_code, mat_name, amount, photo)
            VALUES
                ($formattedDate, $technician, $unid, $job_id, $so, $productCode, $productName, $quantity, $imageNames)
        ";

        // Execute the query
        $pdo->exec($query);
    }

    // Return success message
    echo json_encode(["status" => "Success", "message" => "Data saved successfully"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "Error", "message" => "Data insertion failed: " . $e->getMessage()]);
} finally {
    // Close database connection
    $pdo = null;
}
?>