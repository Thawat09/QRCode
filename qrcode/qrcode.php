<?php include "../qrcode/model/model.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Scan Order</title>
    <link href="./css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <h1>Scan Order</h1>
    <div class="container">
        <div class="row">
            <div class="col">
                <div id="reader"></div>
            </div>
        </div>
        <div class="row">
            <div class="col" id="scanResultCol">
                <h4>Scan Result</h4>
                <div class="container">
                    <form id="confirm-form">
                        <table id="result-table" class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-checkbox"></th>
                                    <th>Job ID</th>
                                    <th>SO</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="row" style="margin-top: 2rem;">
                            <div class="col">
                                <h4>
                                    Technician Accepts Items
                                    <span style="color: red;">*</span>
                                </h4>
                                <select id="select-job" class="select2-drop-mask">
                                    <option value="">เลือกผู้รับคำสั่งงาน...</option>
                                    <?php foreach ($data as $item) : ?>
                                        <option value="<?php echo $item['id']; ?>">
                                            <?php echo $item['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 2rem;">
                            <div class="col">
                                <h4>Upload Images</h4>
                                <input type="file" id="upload-image" multiple>
                            </div>
                        </div>
                        <input id="confirm-button" type="submit" value="Confirm Items" style="margin-top: 20px; background-color: #aaaeb3; color: white;">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="./script/html5-qrcode.min.js"></script>
    <script src="./script/script.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-job').select2();
            $("#upload-image").on("change", function() {
                var numberOfFiles = $(this).get(0).files.length;
                if (numberOfFiles > 4) {
                    alert("You can only upload a maximum of 4 images");
                    $(this).val(""); // Clear the input field
                }
            });
        });
    </script>
</body>

</html>