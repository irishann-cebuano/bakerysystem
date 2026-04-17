<?php include("../layout.php"); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css">

<section class="recordSection"> 
    <div id="recordSection">
        <h2>Bakery Records List</h2>
        <div class="table">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Record ID</th>
                    <th>Date</th>
                    <th>Expense Category</th>
                    <th>Expense Items</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody id="tbl_dailyrecords"></tbody>
        </table>
        <div class="total-expenses">
            <span id="daily_total_expenses">Total Expenses: 0.00</span>
        </div>
        <div class="add-record-controls">
            <button id="btn_show_add_record" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRecordModal">+ Add Record</button>
            <button id="btn_delete_record" type="button" class="btn btn-danger" disabled>🗑 Delete Record</button>

            <!-- Modal -->
            <div class="modal fade" id="addRecordModal" tabindex="-1" aria-labelledby="addRecordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addRecordModalLabel">Add New Record</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addRecordForm">
                                <input type="hidden" name="date" id="hidden_date">
                                <input type="hidden" name="record_id">
                                <div class="mb-3">
                                    <label for="expense_category" class="form-label">Expense Category:</label>
                                    <select name="expense_category" id="expense_category" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <!-- Categories will be loaded dynamically -->
                                    </select>
                                    <div id="new_category_container" class="mt-2" style="display: none;">
                                        <input type="text" id="new_category_input" class="form-control" placeholder="Enter new category name">
                                        <button type="button" id="btn_save_new_category" class="btn btn-sm btn-success mt-1">Add Category</button>
                                        <button type="button" id="btn_cancel_new_category" class="btn btn-sm btn-secondary mt-1">Cancel</button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="expense_items" class="form-label">Expense Items:</label>
                                    <select name="expense_items" id="expense_items" class="form-select" required>
                                        <option value="">Select Item</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount:</label>
                                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="addRecordForm" class="btn btn-primary">Save Record</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="/bakery_records_system/js/jquery-4.0.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/bakery_records_system/js/Records.js"></script>
<script>
    $(document).ready(function() {
        getDailyRecords();
        loadCategories();

        $("#addRecordForm").on("submit", submitAddRecord);

        // Set date when modal is shown
        $('#addRecordModal').on('show.bs.modal', function () {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth()+1).padStart(2,"0");
            const dd = String(today.getDate()).padStart(2,"0");
            $("#hidden_date").val(`${yyyy}-${mm}-${dd}`);
        });
    });
</script>
