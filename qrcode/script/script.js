var canScan = true;
var confirmButton = document.getElementById("confirm-button");

if (confirmButton) {
    confirmButton.addEventListener("click", onConfirmButtonClick);
}

function onScanSuccess(qrCodeMessage) {
    if (!canScan) {
        return;
    }

    canScan = false;

    setTimeout(function () {
        canScan = true;
    }, 3000);
    // console.log("qrCodeMessage:", qrCodeMessage);

    var urlParams = new URLSearchParams(qrCodeMessage);
    var data = {
        unid: urlParams.get("UNID"),
        job: urlParams.get("JOB"),
        so: urlParams.get("SO"),
        productCode: urlParams.get("ProductCode"),
        productName: decodeURIComponent(urlParams.get("ProductName")),
        quantity: Number(urlParams.get("Quantity")).toFixed(0)
    };
    // console.log("data:", data);

    var confirmed = confirm(`Would you like to add Job ID:'${data['job']}' to the table?`);

    if (!confirmed) {
        return;
    }

    addToTable(data);
}

function onScanError(errorMessage) {
    // console.error(errorMessage);
}

async function onConfirmButtonClick(e) {
    e.preventDefault();

    var uploadImage = document.querySelector("#upload-image");
    if (uploadImage.files.length === 0 || uploadImage.files.length > 4) {
        alert("Please upload between 1 and 4 images.");
        return;
    }
    var uploadedImages = [];
    for (var i = 0; i < uploadImage.files.length; i++) {
        var filename = uploadImage.files[i].name;
        var fileExtension = filename.split('.').pop().toLowerCase();

        if (fileExtension !== 'jpg' && fileExtension !== 'png') {
            alert("Only .jpg or .png images are allowed.");
            return;
        }
        uploadedImages.push(filename);
    }
    var technician = document.querySelector("#select-job").value;
    if (!technician) {
        alert("Please select a technician.");
        return;
    }
    var selectedJobs = [];
    var rows = document.querySelectorAll("#result-table tbody tr");
    rows.forEach(function (row) {
        var checkbox = row.querySelector(".job-checkbox");
        if (checkbox.checked) {
            var item = {
                unid: row.cells[1].innerText,
                job: row.cells[2].innerText,
                so: row.cells[3].innerText,
                productCode: row.cells[4].innerText,
                productName: row.cells[5].innerText,
                quantity: row.cells[6].innerText
            };
            selectedJobs.push(item);
        }
    });
    // console.log(selectedJobs);
    var date = new Date();
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day = ('0' + date.getDate()).slice(-2);
    var hours = ('0' + date.getHours()).slice(-2);
    var minutes = ('0' + date.getMinutes()).slice(-2);
    var seconds = ('0' + date.getSeconds()).slice(-2);
    var formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    var data = {
        formattedDate: formattedDate,
        technician: technician,
        selectedJobs: selectedJobs,
        uploadedImages: uploadedImages
    };

    var response = await fetch('./model/saveData.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        alert(`HTTP error! status: ${response.status}`);
        return;
    }

    var result = await response.json();

    if (result.status === 'Success') {
        alert('Data saved successfully');
        location.reload();
    } else {
        alert('Data insertion failed');
    }
}
document.querySelector('#confirm-form').addEventListener('submit', onConfirmButtonClick);

function addToTable(data) {
    const tableBody = document.getElementById("result-table").getElementsByTagName("tbody")[0];
    const newRow = tableBody.insertRow();

    // Create a cell with a checkbox
    createCell(newRow, 'input', { type: "checkbox", className: "job-checkbox" });

    // Create cells with data
    ['unid', 'job', 'so', 'productCode', 'productName', 'quantity'].forEach(field => {
        createCell(newRow, 'text', { text: data[field], display: (field === 'unid') ? "none" : null });
    });

    // Create a cell with a delete button
    createCell(newRow, 'button', { text: "Delete", event: { name: "click", handler: function () { newRow.remove(); } } });
}

function createCell(row, type, config) {
    const cell = row.insertCell();
    const element = document.createElement(type === 'button' ? 'button' : 'input');
    if (type === 'button') {
        element.innerText = config.text;
        element.addEventListener(config.event.name, config.event.handler);
    } else if (type === 'input') {
        element.type = "checkbox";
        element.className = config.className;
    } else {
        cell.innerText = config.text;
        cell.style.display = config.display;
    }
    cell.appendChild(element);
}

var html5QrCodeScanner = new Html5QrcodeScanner("reader", {
    fps: 10,
    qrbox: 250
});

document.getElementById("select-all-checkbox").addEventListener("change", function () {
    var selectAllChecked = this.checked;
    var checkboxes = document.querySelectorAll(".job-checkbox");
    checkboxes.forEach(function (checkbox) {
        checkbox.checked = selectAllChecked;
    });
});

html5QrCodeScanner.render(onScanSuccess, onScanError);ห