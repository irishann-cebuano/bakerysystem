

let selectedRecordId = null;

// Load Daily Records
function normalizeDate(dateStr) {
    if (!dateStr) return "(No Date)";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr.split(" ")[0]; // fallback
    return d.toISOString().split("T")[0];
}
function getDailyRecords() {
    $.get("/bakery_records_system/php/dailyrec.php", function(data) {
        const records = data.records || [];

        const displayRecords = $("#tbl_dailyrecords");
        displayRecords.empty();

        let tableContent = "";
        const grouped = {};
        records.forEach(record => {
            const dateKey = normalizeDate(record.date);
            if (!grouped[dateKey]) {
                grouped[dateKey] = { rows: [], total: 0 };
            }

            const amount = parseFloat(record.amount) || 0;
            grouped[dateKey].rows.push(record);
            grouped[dateKey].total += amount;
        });
        
        const visibleDates = Object.keys(grouped).sort((a,b) => b.localeCompare(a)).slice(0,4);

        visibleDates.forEach(dateKey => {
            tableContent += `<tr class="date-break-row">
                <td colspan="7">Date: ${dateKey} | Daily Total: ₱${grouped[dateKey].total.toFixed(2)}</td>
            </tr>`;

            grouped[dateKey].rows.forEach(record => {
                const categoryDisplay = record.category_name || record.expense_category;
                const itemDisplay = record.item_desc || record.expense_items;
                tableContent += `<tr data-record_id="${record.record_id}">
                    <td>${record.record_id}</td>
                    <td>${dateKey}</td>
                    <td>${categoryDisplay}</td>
                    <td>${itemDisplay}</td>
                    <td>₱${(Number(record.amount) || 0).toFixed(2)}</td>
                </tr>`;
            });
        });

        $('#tbl_dailyrecords').html(tableContent);

         const today = normalizeDate(new Date());

        const todayTotal = grouped[today]?.total || 0;

        $("#today_expenses_card").text("₱" + todayTotal.toFixed(2));

        // ---------------- MONTH TOTAL (FIXED) ----------------
        const now = new Date();
        const currentMonth = String(now.getMonth() + 1).padStart(2, "0");
        const currentYear = String(now.getFullYear());

        let monthlyTotal = 0;

        records.forEach(record => {
            if (!record.date) return;

            const d = new Date(record.date);
            if (isNaN(d.getTime())) return;

            const year = String(d.getFullYear());
            const month = String(d.getMonth() + 1).padStart(2, "0");

            if (year === currentYear && month === currentMonth) {
                monthlyTotal += Number(record.amount) || 0;
            }
        });

        $("#daily_total_expenses").text(
            "This Month Total: ₱" + monthlyTotal.toFixed(2)
        );

        selectedRecordId = null;
        $("#btn_delete_record").prop("disabled", true);

    }, "json").fail(err =>
        alert("❌ Error fetching records: " + err.responseText)
    );
}

const categoryItemsMap = {
    "Ingredients": ["Flour", "Sugar", "Yeast", "Eggs"],
    "Labor": ["Labor Payment"],
    "Utilities": ["Electricity", "Water", "Gas", "Other"],
    "Production/Packaging": ["Plastic Bags", "Boxes", "Labels", "Other"],
    "Supply": ["Cleaning Supplies", "Tools", "Other"],
    "Other": ["Other"]
};

// Delete Record    

$(document).on("click", "#btn_delete_record", function(){
    if(!selectedRecordId) return alert("❌ No record selected!");
    if(!confirm("⚠️ Are you sure you want to delete this record?")) return;

    $.post("/bakery_records_system/php/dailyrec.php?action=delete", {record_id: selectedRecordId}, function(resp){
        if(resp.success){
            alert(`✅ Record deleted successfully!`);
            selectedRecordId = null;
            $("#btn_delete_record").prop("disabled", true);
            getDailyRecords();
        } else {
            alert(`❌ Unable to delete record! Message: ${resp.message}`);
        }
    }, "json").fail(err => alert("❌ Error deleting record: " + err.responseText));
});


// Add Record

// Add Record

function submitAddRecord(event){
    event.preventDefault();
    const formData = $("#addRecordForm").serialize();

    $.post("/bakery_records_system/php/dailyrec.php", formData, function(resp){

        if(resp.success){
            alert("✅ Record added successfully!");
            $("#addRecordForm")[0].reset();
            $('#addRecordModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            getDailyRecords();
        } else {
            alert("❌ " + resp.message);
        }

    }, "json").fail(function(err){
        alert("❌ Error adding record: " + err.responseText);
    });
}



let employeeList = [];
let selectedEmployeeId = null;

// Fetch Employee Records

function getEmployeeRecords() {
    $.get("/bakery_records_system/php/employeerec.php?action=fetch", function(data) {
        employeeList = data.records || [];

        const table = $("#tbl_employeerecords");
        table.empty();

        let tableContent = "";
        employeeList.forEach(emp => {
            tableContent += `
                <tr data-employee_id="${emp.employee_id}">
                    <td>${emp.employee_id}</td>
                    <td>${emp.lastname}</td>
                    <td>${emp.firstname}</td>
                    <td>${emp.role}</td>
                    <td>₱${parseFloat(emp.rate_per_hour || 0).toFixed(2)}</td>
                </tr>
            `;
        });

        $("#tbl_employeerecords").html(tableContent);

        // Reset selection
        selectedEmployeeId = null;
        $("#btn_edit_employee").prop("disabled", true);
        $("#btn_delete_employee").prop("disabled", true);
    }, "json").fail(err => alert("❌ Error fetching employees: " + err.responseText));
}
$(document).on("click", "#btn_edit_employee", function() {
    if (!selectedEmployeeId) {
        return alert("❌ No employee selected!");
    }

    const emp = employeeList.find(e => e.employee_id == selectedEmployeeId);

    if (!emp) return;

    // Fill form
    $("#employee_id").val(emp.employee_id);
    $("input[name='lastname']").val(emp.lastname);
    $("input[name='firstname']").val(emp.firstname);
    $("select[name='role']").val(emp.role);
    $("input[name='rate_per_hour']").val(emp.rate_per_hour);

    // Show modal
    $('#addEmployeeModal').modal('show');
});


// Submit Add Employee

function submitAddEmployee(event){
    event.preventDefault();
    const id = $("#employee_id").val();
    const formData = $("#addEmployeeForm").serialize();

    const url = id
        ? "/bakery_records_system/php/employeerec.php?action=update"
        : "/bakery_records_system/php/employeerec.php";

    $.post(url, formData, function(resp){
        if(resp.status === "success"){
            alert(id ? "✅ Employee updated!" : "✅ Employee added!");
            $("#addEmployeeForm")[0].reset();
            $("#employee_id").val("");

            $('#addEmployeeModal').modal('hide');
            getEmployeeRecords();

            $("#btn_edit_employee").prop("disabled", true);
            $("#btn_delete_employee").prop("disabled", true);
        } else {
            alert("❌ Error adding employee: " + resp.message);
        }
    }, "json").fail(err => alert("❌ Error adding employee: " + err.responseText));
}


// Delete Selected Employee

$(document).on("click", "#btn_delete_employee", function(){
    if(!selectedEmployeeId) return alert("❌ No employee selected!");
    if(!confirm("⚠️ Are you sure you want to delete this employee?")) return;

    $.post("/bakery_records_system/php/employeerec.php?action=delete", { employee_id: selectedEmployeeId }, function(resp){
        if(resp.status === "success"){
            alert("✅ Employee deleted successfully!");
            selectedEmployeeId = null;
            $("#btn_delete_employee").prop("disabled", true);
            getEmployeeRecords();
        } else {
            alert("❌ Unable to delete employee: " + resp.message);
        }
    }, "json").fail(err => alert("❌ Error deleting employee: " + err.responseText));
});

$(document).ready(function(){
    getEmployeeRecords();
    $("#addEmployeeForm").on("submit", submitAddEmployee);
});

let laborList = [];
let selectedLaborId = null;

function getLaborRecords(){
    $.get("/bakery_records_system/php/laborrec.php?action=fetch", function(data){
        laborList = data.records || [];

        const displayLabor = $("#tbl_laborRecords");
        displayLabor.empty();

        let tableContent = "";
        const grouped = {};
        laborList.forEach(labor => {
            const dateKey = normalizeDate(labor.date);

            if (!grouped[dateKey]) {
                grouped[dateKey] = { rows: [], total: 0 };
            }

            const totalPay = Number(labor.total_pay);
            grouped[dateKey].rows.push(labor);
            grouped[dateKey].total += isNaN(totalPay) ? 0 : totalPay;
        });

        const visibleDates = Object.keys(grouped).sort((a,b) => b.localeCompare(a)).slice(0,4);
        visibleDates.forEach(dateKey => {
            tableContent += `<tr class="date-break-row">
                <td colspan="6">Date: ${dateKey} | Daily Labor Total: ₱${grouped[dateKey].total.toFixed(2)}</td>
            </tr>`;
            grouped[dateKey].rows.forEach(labor => {
                tableContent += `<tr data-labor_id="${labor.labor_id}">
                    <td>${labor.labor_id}</td>
                    <td>${labor.employee_id}</td>
                    <td>${labor.record_id}</td>
                    <td>${labor.hours_worked}</td>
                    <td>₱${parseFloat(labor.rate_per_hour || 0).toFixed(2)}</td>
                    <td>₱${Number(labor.total_pay || 0).toFixed(2)}</td>
                </tr>`;
            });
        });

        displayLabor.html(tableContent);
        $(document).on("click", "#tbl_laborRecords tr[data-labor_id]", function () {
        $("#tbl_laborRecords tr").removeClass("selected-row");
        $(this).addClass("selected-row");

        selectedLaborId = $(this).data("labor_id");

        $("#btn_edit_labor").prop("disabled", false);
        $("#btn_delete_labor").prop("disabled", false);
});
    }, "json");
}
$(document).on("click", "#btn_edit_labor", function () {
    if (!selectedLaborId) return alert("❌ No labor selected!");

    const labor = laborList.find(l => l.labor_id == selectedLaborId);
    if (!labor) return alert("❌ Labor not found!");

    $.when(loadEmployees(), loadRecords()).done(function () {

        $("#labor_id").val(labor.labor_id);
        $("#employeeSelect").val(labor.employee_id);
        $("#recordSelect").val(labor.record_id);
        $("#hours_worked").val(labor.hours_worked);
        $("#rate_per_hour").val(labor.rate_per_hour);

        $("#addLaborModal").modal("show");
    });
});
$(document).on("click", "#btn_delete_labor", function () {

    console.log("Selected Labor ID:", selectedLaborId);

    if (!selectedLaborId) {
        return alert("❌ No labor selected!");
    }

    if (!confirm("⚠️ Are you sure you want to delete this labor?")) return;

    $.post("/bakery_records_system/php/laborrec.php?action=delete", 
        { labor_id: selectedLaborId },
        function (resp) {

            console.log(resp);

            if (resp.status === "success") {
                alert("✅ Deleted successfully!");

                selectedLaborId = null;
                $("#btn_delete_labor").prop("disabled", true);

                getLaborRecords();
            } else {
                alert("❌ " + resp.message);
            }
        },
        "json"
    ).fail(err => {
        alert("❌ Server error: " + err.responseText);
    });
});

$(document).on("input", "[name='hours_worked'], [name='rate_per_hour']", function(){
    const hours = parseFloat($("[name='hours_worked']").val()) || 0;
    const rate = parseFloat($("[name='rate_per_hour']").val()) || 0;
    $("[name='total_pay']").val((hours*rate).toFixed(2));
});


function loadEmployees() {
   $.get("/bakery_records_system/php/employeerec.php?action=fetch", function(data) {
        const employees = data.records || [];
        const employeeSelect = $("#employeeSelect");

        employeeSelect.empty();
        employeeSelect.append('<option value="">Select Employee</option>');

        employees.forEach(emp => {
            const fullName = `${emp.firstname} ${emp.lastname}`;

            employeeSelect.append(`
                <option value="${emp.employee_id}">
                    ${emp.employee_id} - ${fullName}
                </option>
            `);
        });

    }, "json");
}
function loadRecords() {
    $.get("/bakery_records_system/php/dailyrec.php", function(data) {
        const daily_rec = data.records|| [];

        const recordSelect = $("#recordSelect");
        recordSelect.empty();

        const recordMap = {};
       
        daily_rec.forEach(p => {
            if (!recordMap[p.record_id]) {
                recordSelect.append(`<option value="${p.record_id}">${p.date}</option>`);
                recordMap[p.record_id] = true;
            }
        });

        if (daily_rec.length === 0) {
            recordSelect.append('<option disabled>No available records</option>');
           
        }
    }, "json").fail(err => alert("❌ Error loading records: " + err.responseText));
}

function submitAddLabor(event) {
    event.preventDefault();

    const employee_id = $("#employeeSelect").val();
    const record_id = $("#recordSelect").val();
    const hours = Number($("[name='hours_worked']").val()) || 0;
    const rate = Number($("[name='rate_per_hour']").val()) || 0;

    if (!employee_id || !record_id) {
        return alert("❌ Please select employee and record");
    }

    const formData = $("#addLaborForm").serialize();

    $.post("/bakery_records_system/php/laborrec.php", formData, function(resp) {
        if (resp.status === "success") {
            alert("✅ Labor added!");
            $("#addLaborForm")[0].reset();
            $('#addLaborModal').modal('hide');
            getLaborRecords();
        } else {
            alert("❌ " + resp.message);
        }
    }, "json");
}     


function getCategoryRecords() {
    $.get("../php/categoryrec.php?action=fetch", function(data) {

        const categories = data.records || []; 
        console.log("Categories found:", categories.length);

        let tableContent = "";
        categories.forEach(cat => {
            tableContent += `
                <tr>
                    <td>${cat.category_id}</td>
                    <td>${cat.category_name}</td>
                </tr>
            `;
        });

        $("#tbl_categoryrecords").html(tableContent);
        console.log("Table updated with", categories.length, "categories");
        
    }, "json").fail(function(err) {
        console.error("AJAX error:", err);
        alert("❌ Error fetching category: " + err.responseText);
    });
}

function loadCategories() {
    $.get("/bakery_records_system/php/categoryrec.php?action=fetch", function(data) {
        const categories = data.records || [];
        const categoryDropdown = $("#expense_category");

        categoryDropdown.empty();
        categoryDropdown.append('<option value="">Select Category</option>');

        categories.forEach(category => {
            categoryDropdown.append(`<option value="${category.category_name}">${category.category_name}</option>`);
        });
    }, "json").fail(err => console.error("Error loading categories:", err));
}





let selectedItemId = null;
let itemList = [];

function getItemRecords() {
    $.get("/bakery_records_system/php/itemrec.php?action=fetch", function(data) {
        const item = data.records || [];
        itemList = item;

        const displayItems = $("#tbl_itemrecords");
        displayItems.empty();  

        let tableContent = "";
        const grouped = {};
        item.forEach(item => {
            const dateKey = item.date || "(No Date)";
            if (!grouped[dateKey]) grouped[dateKey] = { rows:[], total:0 };
            grouped[dateKey].rows.push(item);
            grouped[dateKey].total += Number(item.line_total) || 0;
        });
            const visibleDates = Object.keys(grouped).sort((a,b) => b.localeCompare(a)).slice(0,4); 
                visibleDates.forEach(dateKey => {
                    tableContent += `<tr class="date-break-row">
                        <td colspan="7">Date: ${dateKey} | Daily Item Total: ₱${grouped[dateKey].total.toFixed(2)}</td>
                    </tr>`;
                        grouped[dateKey].rows.forEach(item => {
                            
                            tableContent += `
                                <tr data-item_id="${item.item_id}">
                                <td>${item.item_id}</td>
                                <td>${item.record_id || ''}</td>
                                <td>${item.category_id}</td>
                                <td>${item.item_desc}</td>
                                <td>${item.quantity || 0}</td>
                                <td>₱${Number(item.unit_cost || 0).toFixed(2)}</td>
                                <td>₱${Number(item.line_total || 0).toFixed(2)}</td>
                    </tr>
                `;
            });
        });

        $("#tbl_itemrecords").html(tableContent);

    }, "json").fail(err => alert("❌ Error fetching items: " + err.responseText));
}
function populateItems(categoryName, dropdownId) {
    const dropdown = $(dropdownId);

    dropdown.empty();
    dropdown.append('<option value="">Select Item</option>');

    if (categoryItemsMap[categoryName]) {
        categoryItemsMap[categoryName].forEach(item => {
            dropdown.append(`<option value="${item}">${item}</option>`);
        });
    }
}
$("#btn_edit_item").on("click", function () {
    if (!selectedItemId) return alert("❌ No item selected!");

    const item = itemList.find(i => i.item_id == selectedItemId);
    if (!item) return alert("❌ Item not found in list!");

    $.when(loadRecordsDropdown(), loadCategoriesDropdown()).done(function () {

        $("#item_id").val(item.item_id);
        $("#record_id").val(item.record_id || "");
        $("#category_id").val(item.category_id || "");
        $("#item_desc").val(item.item_desc || "");
        $("#quantity").val(item.quantity || 0);
        $("#unit_cost").val(item.unit_cost || 0);
        $("#line_total").val(item.line_total || 0);

        $("#addItemModal").modal("show");
    });
});
// Delay to ensure dropdowns are populated before setting values
$("#expense_category").on("change", function () {
    populateItems($(this).val(), "#expense_items");
});

// Item form
$("#category_id").on("change", function () {
    const name = $(this).find("option:selected").data("name");
    populateItems(name, "#item_desc");
});

function toggleAddItemForm() {
    const form = $("#addItemModal");
    if (form.is(":visible")) {
        form.hide();
        $("#addItemForm")[0].reset();
        $("#item_id").val("");
        $("#btn_edit_item").prop("disabled", true);
    } else {
        form.show();
        loadRecordsDropdown();
        loadCategoriesDropdown();
    }
}

function updateLineTotal() {
    const qty = parseFloat($("#quantity").val() || 0);
    const cost = parseFloat($("#unit_cost").val() || 0);
    $("#line_total").val((qty * cost).toFixed(2));
}

function loadRecordsDropdown() {
    $.get("/bakery_records_system/php/dailyrec.php", function(data) {
        const records = data.records || [];
        const dropdown = $("#record_id");

        const today = new Date().toISOString().split("T")[0];

        dropdown.empty();

        let found = false;
        const recordMap = {};

        records.forEach(r => {
            // 🔥 normalize BOTH dates
            const recordDate = new Date(r.date).toISOString().split("T")[0];

            if (recordDate === today && !recordMap[r.record_id]) {
                dropdown.append(`
                    <option value="${r.record_id}">
                        Record #${r.record_id}
                    </option>
                `);

                recordMap[r.record_id] = true;
                found = true;
            }
        });

        // ✅ AUTO SELECT FIRST
        if (found) {
            dropdown.prop("selectedIndex", 0);
        } else {
            dropdown.append('<option disabled>No records for today</option>');
        }

    }, "json").fail(err => console.error("Error loading records:", err));
}
function loadCategoriesDropdown() {
    $.get("/bakery_records_system/php/categoryrec.php?action=fetch", function(data) {
        const categories = data.records || [];
        const dropdown = $("#category_id");
        
        dropdown.empty();
        dropdown.append('<option value="">Select Category</option>');
        
        categories.forEach(cat => {
            dropdown.append(`<option value="${cat.category_id}" data-name="${cat.category_name}">${cat.category_id} - ${cat.category_name}</option>`);
        });
    }, "json").fail(err => console.error("Error loading categories:", err));
}


function submitAddItem(event) {
    event.preventDefault();

    const itemId = $("#item_id").val();
    const categoryId = $("#category_id").val();
    const categoryName = $("#category_id").find("option:selected").data("name") || "";
    
    const itemData = {
        item_id: itemId,
        record_id: $("#record_id").val() || 0,
        category_id: categoryId,
        category_name: categoryName,
        item_desc: $("#item_desc").val(),
        quantity: $("#quantity").val(),
        unit_cost: $("#unit_cost").val(),
        line_total: $("#line_total").val()
    };

    const url = itemId ? "/bakery_records_system/php/itemrec.php?action=update" : "/bakery_records_system/php/itemrec.php";

    $.post(url, itemData, function(resp) {
        if (resp.status === "success") {
            alert(itemId ? "✅ Item updated successfully!" : "✅ Item added successfully!");
            $("#addItemForm")[0].reset();
            $("#item_id").val("");
            $("#category_name").val("");

            // ✅ CLOSE MODAL
            $('#addItemModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            toggleAddItemForm();
            getItemRecords();

            $("#btn_edit_item").prop("disabled", true);
            $("#btn_delete_item").prop("disabled", true);
        } else {
            alert("❌ " + resp.message);
        }
    }, "json").fail(err => alert("❌ Error saving item: " + err.responseText));
}
$(document).ready(function() {
    getItemRecords();

    $("#addItemForm").on("submit", submitAddItem);

    // ✅ ADD THIS
    $('#addItemModal').on('show.bs.modal', function () {
        loadRecordsDropdown();
        loadCategoriesDropdown();
    });
});


$(document).ready(function(){
    getCategoryRecords();
});



$(document).ready(function(){
    getDailyRecords();
    getLaborRecords();
    getEmployeeRecords();
    getCategoryRecords();
    getItemRecords();

    // Event delegation for table row selection
    $(document).on("click", "#tbl_dailyrecords tr[data-record_id]", function() {
        $("#tbl_dailyrecords tr").removeClass("selected-row");
        $(this).addClass("selected-row");
        selectedRecordId = $(this).data("record_id");
        $("#btn_delete_record").prop("disabled", false);
    });

    $(document).on("click", "#tbl_employeerecords tr[data-employee_id]", function() {
        $("#tbl_employeerecords tr").removeClass("selected-row");
        $(this).addClass("selected-row");
        selectedEmployeeId = $(this).data("employee_id");
        $("#btn_edit_employee").prop("disabled", false);
        $("#btn_delete_employee").prop("disabled", false);
    });
    
    $(document).on("click", "#tbl_categoryrecords tr[data-category_id]", function() {
        $("#tbl_categoryrecords tr").removeClass("selected-row");
        $(this).addClass("selected-row");
        selectedCategoryId = $(this).data("category_id");
        $("#btn_edit_category").prop("disabled", false);
        $("#btn_delete_category").prop("disabled", false);
    });

    // ITEM CRUD HANDLERS
    $("#btn_delete_item").on("click", function() {
        if (!selectedItemId) return alert("❌ No item selected!");
        if (!confirm("⚠️ Are you sure you want to delete this item?")) return;

        $.post("/bakery_records_system/php/itemrec.php?action=delete", { item_id: selectedItemId }, function(resp) {
            if (resp.status === "success") {
                alert("✅ Item deleted successfully!");
                selectedItemId = null;
                $("#btn_edit_item").prop("disabled", true);
                $("#btn_delete_item").prop("disabled", true);
                getItemRecords();
            } else {
                alert("❌ " + resp.message);
            }
        }, "json").fail(err => alert("❌ Error deleting item: " + err.responseText));
    });


      //  CATEGORY → ITEMS DROPDOWN 
    $("#expense_category").on("change", function () {
        let selectedCategory = $(this).val();
        let itemsDropdown = $("#expense_items");

        itemsDropdown.empty();
        itemsDropdown.append('<option value="">Select Item</option>');

        if (categoryItemsMap[selectedCategory]) {
            categoryItemsMap[selectedCategory].forEach(function (item) {
                itemsDropdown.append(`<option value="${item}">${item}</option>`);
            });
        }
    });

    // Item form field sync
    $("#category_id").on("change", function() {
        const categoryName = $(this).find("option:selected").data("name") || "";
        $("#category_name").val(categoryName);
    });

    $("#item_desc").on("change", function() {
        const category = $(this).find("option:selected").data("category") || "";
        $("#category_name").val(category);
    });

    $("#quantity, #unit_cost").on("input", updateLineTotal);


    $("#addRecordForm").off("submit", submitAddRecord);
    $("#addEmployeeForm").off("submit", submitAddEmployee);
    $("#addItemForm").off("submit", submitAddItem);
    $("#addLaborForm").off("submit", submitAddLabor);

});