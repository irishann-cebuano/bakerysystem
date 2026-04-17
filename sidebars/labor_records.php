<?php include("../layout.php"); ?>
<link rel="stylesheet" href="../style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<section class="recordSection"> 
    <div id="recordSection">
        <h2>Labor Records List</h2>
        <div class="table">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Labor ID</th>
                    <th>Employee ID</th>
                    <th>Record ID</th>
                    <th>Hours Worked</th>
                    <th>Rate Per Hour</th>
                    <th>Total Pay</th>
                </tr>
            </thead>
            <tbody id="tbl_laborRecords"></tbody>
        </table>
        </div>
          <div class="add-labor-controls">
            <button id="btn_show_add_labor" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLaborModal">+ Add Labor</button>
            <button id="btn_edit_labor" type="button" class="btn btn-warning" disabled>✏️ Edit Labor</button>
            <button id="btn_delete_labor" type="button" class="btn btn-danger" disabled>🗑 Delete Labor</button>

            <!-- Modal -->
            <div class="modal fade" id="addLaborModal" tabindex="-1" aria-labelledby="addLaborModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addLaborModalLabel">Add New Labor Record</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addLaborForm">
                                <input type="hidden" name="labor_id">
                                <div class="mb-3">
                                    <label for="employeeSelect" class="form-label">Employee:</label>
                                    <select name="employee_id" id="employeeSelect" class="form-select" required></select>
                                </div>
                                <div class="mb-3">
                                    <label for="recordSelect" class="form-label">Record:</label>
                                    <select name="record_id" id="recordSelect" class="form-select" required></select>
                                </div>
                                <div class="mb-3">
                                    <label for="hours_worked" class="form-label">Hours Worked:</label>
                                    <input type="number" step="0.01" name="hours_worked" id="hours_worked" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="rate_per_hour" class="form-label">Rate Per Hour:</label>
                                    <input type="number" step="0.01" name="rate_per_hour" id="rate_per_hour" class="form-control" required>
                                </div>
                                <input type="hidden" name="total_pay" value="0.00">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="addLaborForm" class="btn btn-primary">Save Labor</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="/bakery_records_system/js/jquery-4.0.0.min.js"></script>
<script src="/bakery_records_system/js/Records.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
    getLaborRecords();
    loadEmployees();
    loadRecords();

    $("#addLaborForm").on("submit", submitAddLabor);
});
</script>