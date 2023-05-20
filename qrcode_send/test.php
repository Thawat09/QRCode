<?php
include "./model.php";

function getQrCodeUrl($data)
{
    $size = 150;
    $url = 'http://localhost/dss_master/qrcode/qrcode.php?URL=&UNID=' . urlencode($data["unid"]) . '&JOB=' . urlencode($data["main_job_id"]) . '&SO=' . urlencode($data["ref_doc_so"]) . '&ProductCode=' . urlencode($data["mat_code"]) . '&ProductName=' . urlencode(urldecode($data["mat_name"])) . '&Quantity=' . urlencode($data["amount"]);
    return "https://chart.googleapis.com/chart?chs=" . $size . "x" . $size . "&cht=qr&chl=" . urlencode($url) . "&choe=UTF-8";
}

$newData = [];
foreach ($data as $row) {
    $newData[$row["unid"]][] = $row;
}
$unidArray = array_keys($newData);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ตารางแสดงรายการสินค้า (ส่ง)</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <input type="button" value="Print" onclick="window.print()">
        <table>
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>SO</th>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Amount</th>
                    <th>QR Code</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($unidArray as $unidIndex => $unid) {
                    $rows = $newData[$unid];
                    $rowspanUnid = count($rows);
                    foreach ($rows as $index => $row) {
                        echo "<tr>";
                        if ($index == 0) {
                            echo "<td rowspan='$rowspanUnid'>" . $row["main_job_id"] . "</td>";
                            echo "<td rowspan='$rowspanUnid'>" . $row["ref_doc_so"] . "</td>";
                        }
                        echo "<td>" . $row["mat_code"] . "</td>";
                        echo "<td>" . $row["mat_name"] . "</td>";
                        echo "<td>" . $row["amount"] . "</td>";
                        $url = getQrCodeUrl($row);
                        echo "<td style='width:150px;' ><a href='$url'><img src='" . $url . "' alt='QR Code'></a></td>";
                        echo "</tr>";
                    }

                    if ($unidIndex != count($unidArray) - 1) {
                        echo "<tr style='height: 50px;'><td colspan='7' style='border:none;'></td></tr>";
                    }
                }          
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>