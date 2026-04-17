<?php include("../layout.php"); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css">

<section class="recordSection"> 
    <div id="recordSection">
        <h2>Category Records</h2>
        <div class="table">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Category ID </th>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody id="tbl_categoryrecords"></tbody>
        </table>
        </div>
</section>
<script src="/bakery_records_system/js/jquery-4.0.0.min.js"></script>
<script src="/bakery_records_system/js/Records.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        getCategoryRecords();
    });
</script>
