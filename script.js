/* =====================================================
   AUTOCARE - GLOBAL JAVASCRIPT
===================================================== */


/* =========================
   1. DELETE CONFIRMATION
========================= */

function confirmDelete(message = "Are you sure you want to delete this record?") {
    return confirm(message);
}


/* =========================
   2. SEARCH / FILTER TABLE
========================= */

function searchTable(inputId, tableId) {

    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (!input || !table) return;

    const filter = input.value.toLowerCase();

    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {

        const rowText =
            rows[i].innerText.toLowerCase();

        rows[i].style.display =
            rowText.includes(filter)
            ? ""
            : "none";
    }
}


/* =========================
   3. VEHICLE NUMBER FORMAT
========================= */

function formatVehicleNumber(input) {

    input.value =
        input.value
            .toUpperCase()
            .replace(/\s+/g, "");
}


/* =========================
   4. PHONE VALIDATION
========================= */

function validatePhone(input) {

    input.value =
        input.value.replace(/\D/g, "");

    if (input.value.length > 10) {
        input.value =
            input.value.slice(0, 10);
    }
}


/* =========================
   5. NUMBER ONLY
========================= */

function numbersOnly(input) {

    input.value =
        input.value.replace(/[^0-9.]/g, "");
}


/* =========================
   6. AUTO PAYMENT AMOUNT
========================= */

function setupPaymentAutoFill() {

    const serviceSelect =
        document.getElementById("service_id");

    const amountInput =
        document.getElementById("amount");


    if (!serviceSelect || !amountInput) {
        return;
    }


    serviceSelect.addEventListener(
        "change",
        function () {

            const selectedOption =
                this.options[this.selectedIndex];

            const cost =
                selectedOption.getAttribute("data-cost");

            if (cost) {
                amountInput.value = cost;
            }

        }
    );
}


/* =========================
   7. STATUS FILTER
========================= */

function filterByStatus(selectId, tableId, columnIndex) {

    const select =
        document.getElementById(selectId);

    const table =
        document.getElementById(tableId);

    if (!select || !table) return;


    const selectedStatus =
        select.value.toLowerCase();


    const rows =
        table.getElementsByTagName("tr");


    for (let i = 1; i < rows.length; i++) {

        const cells =
            rows[i].getElementsByTagName("td");


        if (!cells[columnIndex]) {
            continue;
        }


        const cellText =
            cells[columnIndex]
                .innerText
                .trim()
                .toLowerCase();


        if (
            selectedStatus === "" ||
            cellText.includes(selectedStatus)
        ) {

            rows[i].style.display = "";

        } else {

            rows[i].style.display = "none";

        }
    }
}


/* =========================
   8. PRIORITY FILTER
========================= */

function filterByPriority(selectId, tableId, columnIndex) {

    const select =
        document.getElementById(selectId);

    const table =
        document.getElementById(tableId);

    if (!select || !table) return;


    const selectedPriority =
        select.value.toLowerCase();


    const rows =
        table.getElementsByTagName("tr");


    for (let i = 1; i < rows.length; i++) {

        const cells =
            rows[i].getElementsByTagName("td");


        if (!cells[columnIndex]) {
            continue;
        }


        const cellText =
            cells[columnIndex]
                .innerText
                .trim()
                .toLowerCase();


        if (
            selectedPriority === "" ||
            cellText.includes(selectedPriority)
        ) {

            rows[i].style.display = "";

        } else {

            rows[i].style.display = "none";

        }
    }
}


/* =========================
   9. SIMPLE FORM VALIDATION
========================= */

function validateRequiredFields(form) {

    const requiredFields =
        form.querySelectorAll("[required]");


    for (const field of requiredFields) {

        if (
            field.value.trim() === ""
        ) {

            alert("Please fill all required fields.");

            field.focus();

            return false;
        }
    }

    return true;
}


/* =========================
   10. LIVE CHARACTER COUNTER
========================= */

function setupCharacterCounter(
    textareaId,
    counterId,
    maxLength
) {

    const textarea =
        document.getElementById(textareaId);

    const counter =
        document.getElementById(counterId);


    if (!textarea || !counter) return;


    function updateCounter() {

        const length =
            textarea.value.length;

        counter.innerText =
            `${length} / ${maxLength}`;

    }


    textarea.setAttribute(
        "maxlength",
        maxLength
    );


    textarea.addEventListener(
        "input",
        updateCounter
    );


    updateCounter();
}


/* =========================
   11. SERVICE COST PREVIEW
========================= */

function calculateServiceCost(
    labourId,
    partsId,
    totalId
) {

    const labour =
        parseFloat(
            document.getElementById(labourId)?.value
        ) || 0;


    const parts =
        parseFloat(
            document.getElementById(partsId)?.value
        ) || 0;


    const total =
        labour + parts;


    const totalElement =
        document.getElementById(totalId);


    if (totalElement) {

        totalElement.innerText =
            "₹" + total.toFixed(2);

    }
}


/* =========================
   12. TOAST MESSAGE
========================= */

function showToast(message, type = "success") {

    const toast =
        document.createElement("div");


    toast.className =
        `toast toast-${type}`;


    toast.innerText = message;


    document.body.appendChild(toast);


    setTimeout(() => {

        toast.classList.add("show");

    }, 100);


    setTimeout(() => {

        toast.classList.remove("show");

        setTimeout(() => {
            toast.remove();
        }, 300);

    }, 3000);
}


/* =========================
   13. AUTO-HIDE ALERTS
========================= */

function autoHideAlerts() {

    const alerts =
        document.querySelectorAll(".alert");


    alerts.forEach(alert => {

        setTimeout(() => {

            alert.style.opacity = "0";

            setTimeout(() => {
                alert.remove();
            }, 300);

        }, 3000);

    });
}


/* =========================
   14. PAGE LOAD
========================= */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        setupPaymentAutoFill();

        autoHideAlerts();

    }
);