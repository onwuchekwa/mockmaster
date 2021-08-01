<?php
    //question.php

    $pageName = 'Question Management';
    include('header.php');
?>

<nav aria-label="breadcrumb">
  	<ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="domain.php">MockMasters Domain List</a></li>
    	<li class="breadcrumb-item active" aria-current="page">MockMasters Question List</li>
  	</ol>
</nav>
<div class="card card-margin">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="panel-title">MockMasters Question List</h3>
            </div>
            <div class="col-md-3 text-align-right">
                
            </div>
        </div>
    </div>
    <div class="card-body">
        <span id="message_display"></span>
        <div class="table-responsive col-sm-12">
            <table id="questionDataTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Correct Option</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Question Modal -->
<div class="modal" id="questionModal">
  	<div class="modal-dialog modal-lg">
    	<form method="post" id="questionForm">
      		<div class="modal-content">
      			<!-- Modal Header -->
        		<div class="modal-header">
          			<h4 class="modal-title" id="questionModalTitle"></h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>

        		<!-- Modal body -->
        		<div class="modal-body">                    
          			<div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Question Text <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<textarea name="questionText" id="questionText" cols="30" rows="5" autocomplete="off" class="form-control input-lg"  ></textarea>
	                		</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Option 1 <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<textarea cols="30" rows="3" name="optionText1" id="optionText1" autocomplete="off" class="form-control"></textarea>
	                		</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Option 2 <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<textarea name="optionText2" id="optionText2" cols="30" rows="2" autocomplete="off" class="form-control"></textarea>
	                		</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Option 3 <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<textarea cols="30" rows="3" name="optionText3" id="optionText3" autocomplete="off" class="form-control"></textarea>
	                		</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Option 4 <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<textarea cols="30" rows="3" name="optionText4" id="optionText4" autocomplete="off" class="form-control"></textarea>
	                		</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="row">
              				<label class="col-md-4 text-right">Correct Answer <span class="text-danger">*</span></label>
	              			<div class="col-md-8">
	                			<select name="questionAnswer" id="questionAnswer" class="form-control">
	                				<option value="">Select Correct Answer</option>
	                				<option value="1">Option 1</option>
	                				<option value="2">Option 2</option>
	                				<option value="3">Option 3</option>
	                				<option value="4">Option 4</option>
	                			</select>
	                		</div>
            			</div>
          			</div>
        		</div>

	        	<!-- Modal footer -->
	        	<div class="modal-footer">
                    <input type="hidden" name="questionId" id="questionId" />
	          		<input type="hidden" name="hiddenDomainId" id="hiddenDomainId" />
	        		<input type="hidden" name="page" value="domain" />
	        		<input type="hidden" name="action" id="hidden_action" value="Edit Question" />
	        		<input type="submit" name="editQuestion" id="editQuestion" class="btn btn-success btn-sm" value="Edit Question" />
	          		<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
	        	</div>
        	</div>
    	</form>
  	</div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<!-- Modal Header -->
      		<div class="modal-header">
        		<h4 class="modal-title">Delete Confirmation</h4>
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
      		</div>
      		<!-- Modal body -->
      		<div class="modal-body">
        		<h3 align="center">Are you sure you want to delete this exam?</h3>
      		</div>
      		<!-- Modal footer -->
      		<div class="modal-footer">
      			<button type="button" name="confirmDelete" id="confirmDelete" class="btn btn-primary btn-sm">OK</button>
        		<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      		</div>
    	</div>
  	</div>
</div>


<script>
    $(document).ready(function() {
        var code = "<?php echo $_GET['code']; ?>";
        var dataTable = $('#questionDataTable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
               url: "ajax_action.php",
               method: "POST",
               data: { action: 'fetch_question', page: 'domain', code: code } 
            },
            "columnDef" : [
                { "targets": [2], "orderable": false }
            ],
        });

        function resetQuestionForm() {
            $('#editQuestion').val('Edit Question');
            $('#hidden_action').val('Edit Question'); 
            $('#questionForm')[0].reset();
            $('#questionForm').parsley().reset();
        }

        $('#questionForm').parsley();

        $('#questionForm').on('submit', function(e) {
            e.preventDefault();
            $('#questionText').attr('required', 'required');
            $('#optionText1').attr('required', 'required');
            $('#optionText2').attr('required', 'required');
            $('#optionText3').attr('required', 'required');
            $('#optionText4').attr('required', 'required');
            $('#questionAnswer').attr('required', 'required');

            if($('#questionForm').parsley().validate()) {
                $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#editQuestion').attr('disabled', 'disabled');
                        $('#editQuestion').val('validating...')
                    },
                    success: function(data) {
                        if(data.success) {
                            $('#message_display').html('<span class="alert alert-success d-block">'+data.success+'</span>');
                            resetQuestionForm();
						    $('#questionModal').modal('hide');
                            dataTable.ajax.reload();
                        }
                        $('#editQuestion').attr('disabled', false);
                        $('#editQuestion').val($('#hidden_action').val());
                    }
                });
            }
        });

        var questionId = '';
        $(document).on('click', '.edit', function() {
            questionId = $(this).attr('id');
            resetQuestionForm();
            $.ajax({
                url: "ajax_action.php",
                method: "POST",
                data: { action: 'edit_fetch', questionId: questionId, page: 'domain' },
                dataType: "json",
                success: function(data) {  
                    $('#questionText').val(data.questionText);
                    $('#optionText1').val(data.optionText1);
                    $('#optionText2').val(data.optionText2);
                    $('#optionText3').val(data.optionText3);
                    $('#optionText4').val(data.optionText4);
                    $('#questionAnswer').val(data.questionAnswer);
                    $('#questionId').val(questionId);
                    $('#questionModalTitle').text('Edit Question Details');
                    $('#questionModal').modal('show');
                }
            })
        });

        $(document).on('click', '.delete', function() {
            questionId = $(this).attr('id');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').click(function() {
            $.ajax({
                url:"ajax_action.php",
                method:"POST",
                data:{questionId: questionId, action: 'question_delete', page: 'domain'},
                dataType:"json",
                success: function(data)
                {
                    $('#message_display').html('<span class="alert alert-success d-block">'+data.success+'</span>');
                    $('#deleteModal').modal('hide');
                    dataTable.ajax.reload();
                }
            })
        });
    });
</script>

<?php
    include('footer.php');
?>