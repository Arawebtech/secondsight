<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Orders</h1>
	</div>

    <div class="content-header-right">
        <form id="downloadForm" action="download-order.php" method="post">
            <input type="date" name="start_date">
            <input type="date" name="end_date">
            <button class="btn btn-success" name="download_report">Download PDF</button>
        </form>
    </div>
</section>

<section class="content">

  <div class="row">
    <div class="col-md-12">

      <div class="box box-info">
        <div class="box-body table-responsive">
            <form id="bulkForm" action="../invoice.php" method="POST" target="_blank">
            <div class="bulk-actions" style="margin:10px 0;">
                        <select id="bulk_status" class="form-control" style="width:auto;display:inline-block;">
                            <option value="">-- Bulk Change Status --</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Waiting for pickup">Waiting for pickup</option>
                            <option value="Shift">Shift</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Return">Return</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                        <button type="button" onclick="bulkChangeStatus()" class="btn btn-primary btn-sm">
                            Apply
                        </button>
                        <!-- Bulk download button -->
                        <button type="submit" name="download_labels_bulk" class="btn btn-primary btn-sm">
                            Download Selected Labels
                        </button>
                    </div>
          <table id="example1" class="table table-bordered table-hover table-striped">
			<thead>
			    <tr>
			        <th><input type="checkbox" id="select_all"></th>
			        <th>#</th>
                    <!-- <th>Image</th>
                    <th>User Id</th> -->
                    <th>Order</th>
			        <!-- <th>Product Name</th>
                    <th>SKU</th>
                    <th>Weight</th>
                    <th>Unit</th>
                    <th>color</th>
                    <th>size</th>
                    <th>Total Price</th> -->
                    <!--<th>Price</th>-->
                    <!-- <th>GST</th>
                    <th>Quantity</th>
                    <th>No. Of Item</th>
                    <th>Billing Address</th>
                    <th>Shipping Address</th> -->
                     <th>Order Status</th>
                    <th>Order Date</th>
                    <th>Print Label</th>
			    </tr>
			</thead>
            <tbody>
                
                
                
                <?php
                $statement = $pdo->prepare("
                SELECT 
                    o.order_id,
                    u.full_name,
                    u.email,
                    u.phone,
                    MAX(o.order_date) AS order_date,
                    MAX(o.order_status) AS order_status,
                    COUNT(o.p_id) AS total_items,
                    p.payable_amount,
                    p.payment_status
                FROM tbl_order o
                JOIN tbl_user u ON o.user_id = u.id
                LEFT JOIN tbl_payment p ON o.order_id = p.order_id
                GROUP BY o.order_id, u.full_name, u.email, u.phone, p.payable_amount, p.payment_status
                ORDER BY MAX(o.id) DESC;

                ");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                $i = 0;
                foreach ($result as $row) {
                    $i++;
                    $orderLink = "order-details.php?order_id=" . urlencode($row['order_id']);
                    ?>
					<tr class="<?php echo (
        $row['payment_status'] === 'Pending' || 
        $row['payment_status'] === 'Failed'
    ) ? 'bg-r' : 'bg-g'; ?>">
					    <td>
                            <input type="checkbox" name="order_ids[]" class="order_checkbox" value="<?= $row['order_id']; ?>">
                        </td>
	                    <td><?php echo $i; ?></td>
                       
                        <td> <a href="<?php echo $orderLink; ?>"><?php echo $row['order_id']; ?> - <?php echo htmlspecialchars($row['full_name']); ?></a> </td>
                        <td>
                            <select name="shipping_status" id="<?= $row['order_id']; ?>" onchange="change_status(this.value)">
                                <option value="<?= $row['order_status']; ?>">
                                    <?= $row['order_status']; ?>
                                </option>
                                <option value="<?= $row['order_id']; ?>~Accepted">Accepted</option>
                                <option value="<?= $row['order_id']; ?>~Waiting for pickup">Waiting for pickup</option>
                                <option value="<?= $row['order_id']; ?>~Shift">Shift</option>
                                <option value="<?= $row['order_id']; ?>~Rejected">Rejected</option>
                                <option value="<?= $row['order_id']; ?>~Delivered">Delivered</option>
                                <option value="<?= $row['order_id']; ?>~Return">Return</option>
                                <option value="<?= $row['order_id']; ?>~Canceled">Canceled</option>
                            </select>
                        </td>
	                    <td><?php echo $row['order_date']; ?></td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm singleDownload" data-id="<?= $row['order_id']; ?>">
                                Download Shipping Label
                            </button>
                        </td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
          </table>
          </form>
          <!-- Hidden form for single download -->
        <form id="singleForm" action="../invoice.php" method="POST" target="_blank" style="display:none;">
            <input type="hidden" name="order_id" id="single_order_id">
            <input type="hidden" name="download_label" value="1">
        </form>
        </div>
      </div>
  
  

</section>

<!-- 
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Sure you want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>
 -->
 
 <script type="text/javascript">
    function change_status(id)
    {
        var myArray = id.split("~");
        var order_id = myArray[0];
        var status = myArray[1];
        // alert(order_id);

        $.ajax({
          type: "POST",
          dataType: "text",
          url: "ajax/change-status.php",
          data: "action=order_status&order_id=" + order_id + "&status=" + status,
          success: function(result) {
              // alert(result);
              location.reload();
          }
      });
    }
</script>


<?php require_once('footer.php'); ?>
<script>
$(document).ready(function() {
    
    $("#select_all").on("click", function() {
        $(".order_checkbox").prop("checked", this.checked);
    });
});

function bulkChangeStatus() {
    var status = $("#bulk_status").val();
    var selected = [];

    $(".order_checkbox:checked").each(function() {
        selected.push($(this).val());
    });

    if (selected.length === 0) {
        alert("⚠️ Please select at least one order to change status.");
        return;
    }
    if (status === "") {
        alert("⚠️ Please select a status to apply.");
        return;
    }

    $.ajax({
        type: "POST",
        url: "ajax/change-status.php",
        data: {
            action: "bulk_order_status",
            order_ids: selected, 
            status: status
        },
        success: function(response) {
            location.reload();
        }
    });
}
</script>

<script>
    document.getElementById('downloadForm').addEventListener('submit', function(e) {
        // Remove previous hidden inputs for order_ids if any
        this.querySelectorAll('input[name="order_ids[]"]').forEach(el => el.remove());

        // Collect selected checkboxes
        var selected = Array.from(document.querySelectorAll('.order_checkbox:checked'))
                            .map(cb => cb.value);

        // Append selected order_ids to the form
        selected.forEach(function(id) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order_ids[]';
            input.value = id;
            e.target.appendChild(input);
        });

        // If no checkboxes selected, it will just submit the form normally (all orders or date-filtered)
    });
</script>


<script>
    // Select All functionality
    document.getElementById('select_all').addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('input[name="order_ids[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });

    // Bulk validation
    document.getElementById('bulkForm').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('input[name="order_ids[]"]:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert("⚠️ Please select at least one order to download labels.");
        }
    });

    // Single download
    document.querySelectorAll('.singleDownload').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('single_order_id').value = this.dataset.id;
            document.getElementById('singleForm').submit();
        });
    });
</script>






