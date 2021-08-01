<?php
    // user.php

    $pageName = "Candidate List";
    include('header.php');
?>

<div class="card card-margin">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="panel-title">Candidate List</h3>
            </div>
            <div class="col-md-3 text-align-right">

            </div>
        </div>
    </div>
    <div class="card-body">
        <span id="message_display"></span>
        <div class="table-responsive">
            <table id="userDataTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Gender</th>
                        <th>Phone No.</th>
                        <th>Email Verified</th>
                        <th>Paid?</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="updateModal">
  	<div class="modal-dialog modal-lg">
      <form method="post" id="candidateForm">
    	<div class="modal-content">
      		<!-- Modal Header -->
      		<div class="modal-header">
        		<h4 class="modal-title">Update Payment Status</h4>
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
      		</div>
      		<!-- Modal body -->
      		<div class="modal-body">
              <div class="form-group">
                    <div class="row">
                        <label class="col-md-4 text-right">Payment Status <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select name="updatePayment" id="updatePayment" class="form-control">
                                <option value="">Select Payment Status</option>
                                <option value="no">Not Paid</option>
                                <option value="yes">Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
      		</div>
      		<!-- Modal footer -->
      		<div class="modal-footer">
                <input type="hidden" name="candidateId" id="candidateId" />
                <input type="hidden" name="page" value="user" />
                <input type="hidden" name="action" id="hidden_action" value="Change" />
      			<button type="submit" name="changePayment" id="changePayment" class="btn btn-primary btn-sm">Change</button>
        		<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      		</div>
        </div>
      </form>
  	</div>
</div>

<script>
    $(document).ready(function() {
        var dataTable = $('#userDataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": { 
                url: "ajax_action.php",
                method: "POST",
                data: { action: 'fetch', page: 'user' }
            },
            "columnDefs": [{
                    "targets": [0, 7],
                    "orderable": false
                },
            ],
        });

        function resetCandidateForm() {
            $('#changePayment').val('Change');
            $('#hidden_action').val('Change'); 
            $('#candidateForm')[0].reset();
            $('#candidateForm').parsley().reset();
        }        

        $('#candidateForm').parsley();

        $(document).on('click', '.update', function() {
            let candidateId = $(this).attr('id');
            resetCandidateForm();
            $.ajax({
                url: "ajax_action.php",
                method: "POST",
                data: { action: 'fetch_pay_sts', candidateId: candidateId, page: 'user' },
                dataType: "json",
                success: function(data) {  
                    $('#updatePayment').val(data.hasCandidatePaid);
                    $('#candidateId').val(candidateId);
                    $('#updateModal').modal('show');
                }
            })
        });

        $('#candidateForm').on('submit', function(e) {
            e.preventDefault();
            $('#updatePayment').attr('required', 'required');

            if($('#candidateForm').parsley().validate()) {
                $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#changePayment').attr('disabled', 'disabled');
                        $('#changePayment').val('validating...')
                    },
                    success: function(data) {
                        if(data.success) {
                            $('#message_display').html('<span class="alert alert-success d-block">'+data.success+'</span>');
                            resetCandidateForm();
						    $('#updateModal').modal('hide');
                            dataTable.ajax.reload();
                        }
                        $('#changePayment').attr('disabled', false);
                        $('#changePayment').val($('#hidden_action').val());
                    }
                });
            }
        });
    });
</script>

<?php include('footer.php'); ?>