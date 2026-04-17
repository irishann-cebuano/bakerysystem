<?php include("../layout.php"); ?>
<link rel="stylesheet" href="../style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<section class="recordSection"> 
    <div id="recordSection">
        <h2>List of Employee Records</h2>
        <div class="table">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Employee ID</th>
                    <th>Lastname</th>
                    <th>First Name</th>
                    <th>Role</th>
                    <th>Rate Per Hour</th>
                </tr>
            </thead>
            <tbody id="tbl_employeerecords"></tbody>
        </table>
    </div>
     <div class="add-employee-controls">
            <button id="btn_show_add_employee" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">+ Add Employee</button>
             <button id="btn_edit_employee" type="button" class="btn btn-warning" disabled>✏️ Edit Employee</button>
            <button id="btn_delete_employee" type="button" class="btn btn-danger" disabled> 🗑 Delete Employee</button>
           
           
            <!-- Modal -->
            <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addEmployeeForm">
                                <input type="hidden" name="employee_id" id="employee_id">
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Lastname:</label>
                                    <input type="text" name="lastname" id="lastname" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">Firstname:</label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role:</label>
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        <option value="Baker">Baker</option>
                                        <option value="Cashier">Cashier</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="rate_per_hour" class="form-label">Rate Per Hour:</label>
                                    <input type="number" name="rate_per_hour" id="rate_per_hour" class="form-control" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="addEmployeeForm" class="btn btn-primary">Save Employee</button>
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
        getEmployeeRecords();

         $("#addEmployeeForm").on("submit", submitAddEmployee);

         // Reset form when modal is shown
         $('#addEmployeeModal').on('show.bs.modal', function () {
             const isEdit = $("#employee_id").val() !== "";
             $("#addEmployeeModalLabel").text(isEdit ? "Edit Employee" : "Add New Employee");
             if (!isEdit) {
                 $("#addEmployeeForm")[0].reset();
                 $("#employee_id").val("");
             }
         });
    });
</script>
