<?php
session_start();

if (!isset($_SESSION['qr_data'])) {
    $_SESSION['qr_data'] = [];
}

if (isset($_GET['deleteIndex'])) {
    deleteRow($_GET['deleteIndex']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['JOB']) && isset($_GET['SO']) && isset($_GET['ProductCode']) && isset($_GET['ProductName']) && isset($_GET['Quantity'])) {
    $job = $_GET['JOB'];
    $so = $_GET['SO'];
    $productCode = $_GET['ProductCode'];
    $productName = $_GET['ProductName'];
    $quantity = $_GET['Quantity'];
    $qr_data = [
        'job' => $job,
        'so' => $so,
        'productCode' => $productCode,
        'productName' => $productName,
        'quantity' => $quantity
    ];
    array_push($_SESSION['qr_data'], $qr_data);
}

function deleteRow($index)
{
    array_splice($_SESSION['qr_data'], $index, 1);
}

$previousUnid = "";
$previousJob = "";
$previousSo = "";
$data = $_SESSION['qr_data'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ตารางแสดงรายการสินค้า (รับ)</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>JOB</th>
                <th>SO</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($data as $index => $row) {

                if ($index > 0 && ($previousUnid != $row["unid"] || $previousJob != $row["main_job_id"])) {
                    echo "<tr style='height: 50px;'><td colspan='7' style='border:none;'></td></tr>";
                }
                echo "<tr>";

                if ($previousUnid != $row["unid"]) {
                    $rowspanUnid = count(array_filter($data, function ($r) use ($row) {
                        return $r["unid"] == $row["unid"];
                    }));
                    echo "<td rowspan='$rowspanUnid'>" . $row["unid"] . "</td>";
                }

                if ($previousJob != $row["main_job_id"]) {
                    $rowspanJob = count(array_filter($data, function ($r) use ($row) {
                        return $r["main_job_id"] == $row["main_job_id"] && $r["unid"] == $row["unid"];
                    }));
                    echo "<td rowspan='$rowspanJob'>" . $row["main_job_id"] . "</td>";
                }

                if ($previousSo != $row["ref_doc_so"] || $previousJob != $row["main_job_id"]) {
                    $rowspanSo = count(array_filter($data, function ($r) use ($row) {
                        return $r["ref_doc_so"] == $row["ref_doc_so"] && $r["main_job_id"] == $row["main_job_id"];
                    }));
                    echo "<td rowspan='$rowspanSo'>" . $row["ref_doc_so"] . "</td>";
                }

                echo "<td>" . $row["mat_code"] . "</td>";
                echo "<td>" . $row["mat_name"] . "</td>";
                echo "<td>" . $row["amount"] . "</td>";
                echo "<td><a href='?deleteIndex={$index}' style='color:red;'>Delete</a></td>";
                echo "</tr>";

                $previousUnid = $row["unid"];
                $previousJob = $row["main_job_id"];
                $previousSo = $row["ref_doc_so"];
            }
            ?>
        </tbody>
    </table>
    <div class="button-wrapper">
        <a href="index.php">back</a>
    </div>
</body>

</html>