<?php include("../layout.php"); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css">

<section class="recordSection"> 
    <div id="recordSection">
        <h2>Item Records</h2>
        <div class="table">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Item ID </th>
                    <th>Record ID</th>
                    <th>Category ID</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Line Total</th>   
                </tr>
            </thead>
            <tbody id="tbl_itemrecords"></tbody>
        </table>
    </div>
     <div class="add-item-controls">
            <button id="btn_show_add_item" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">+ Add Item</button>
            <button id="btn_edit_item" type="button" class="btn btn-warning" disabled>✏️ Edit Item</button>
            <button id="btn_delete_item" type="button" class="btn btn-danger" disabled>🗑 Delete Item</button>


            <!-- Modal -->
            <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addItemForm">
                                <input type="hidden" name="item_id" id="item_id">
                                <div class="mb-3">
                                    <label for="record_id" class="form-label">Record ID:</label>
                                    <select id="record_id" name="record_id" class="form-select">
                                        <option value="">Select Record ID</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category:</label>
                                    <select id="category_id" name="category_id" class="form-select">
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="item_desc" class="form-label">Item Description:</label>
                                    <select id="item_desc" name="item_desc" class="form-select">
                                        <option value="">Select Item </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity:</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="unit_cost" class="form-label">Unit Cost:</label>
                                    <input type="number" name="unit_cost" id="unit_cost" class="form-control" min="0" step="0.01" value="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="line_total" class="form-label">Line Total:</label>
                                    <input type="text" name="line_total" id="line_total" class="form-control" readonly>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="addItemForm" class="btn btn-primary">Save Item</button>
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
        getItemRecords();

        $("#addItemForm").on("submit", submitAddItem);
    });
</script>
