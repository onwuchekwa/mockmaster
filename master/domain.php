<?php
    //domain.php

    $pageName = 'Domain List';
    include('header.php');
?>

<div class="card card-margin">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="panel-title">MockMasters Domain List</h3>
            </div>
            <div class="col-md-3 text-align-right">
                <button type="button" id="addQuestionButton" class="btn btn-info btn-sm">Add New Question</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <span id="message_display"></span>
        <div class="table-responsive col-sm-12">
            <table id="domainDataTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Exam Code</th>
                        <th>Domain Name</th>
                        <th>Total Questions</th>
                        <th>Minimum Score</th>
                        <th>Maximum Score</th>
                        <th>Score Per Question</th>
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
    	<form method="post" id="newQuestionForm">
      		<div class="modal-content">
      			<!-- Modal Header -->
        		<div class="modal-header">
          			<h4 class="modal-title">Add New Question</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>

        		<!-- Modal body -->
        		<div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4 text-right">Exam Name <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="examCode" id="examCode" class="form-control input-lg">
                                    <option value="">Select an Exam</option>
                                    <?php echo $exam->getExamList(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4 text-right">Domain Name <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="domainId" id="domainId" class="form-control"></select>
                            </div>
                        </div>
                    </div>
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
	        		<input type="hidden" name="page" value="domain" />
	        		<input type="hidden" name="action" id="hidden_action" value="Add Question" />
	        		<input type="submit" name="addNewQuestion" id="addNewQuestion" class="btn btn-success btn-sm" value="Add Question" />
	          		<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
	        	</div>
        	</div>
    	</form>
  	</div>
</div>


<script>
    $(document).ready(function() {
        var dataTable = $('#domainDataTable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
               url: "ajax_action.php",
               method: "POST",
               data: { action: 'fetch_domain', page: 'domain' } 
            },
            "columnDef" : [
                { "targets": [7], "orderable": false }
            ],
        });

        function resetQuestionForm() {
            $('#addNewQuestion').val('Add New Question');
            $('#hidden_action').val('Add Question'); 
            $('#domainId').attr('disabled', 'disabled'); 
            $('#newQuestionForm')[0].reset();
            $('#newQuestionForm').parsley().reset();
        }

        $(document).on('click', '#addQuestionButton', function() {
            resetQuestionForm();
            $('#questionModal').modal('show');
            $('#domainId').attr('disabled', 'disabled');
        });

        $(document).on('change', '#examCode', function() {
            var examCode = $('#examCode option:selected').val();
            $('#domainId').attr('disabled', false);
            $.ajax({
                url: "ajax_action.php",
                method: "POST",
                data: {examCode: examCode, action: 'fetch_domain_detail', page: 'domain' },
                success: function(data) {
                    $('#domainId').html(data);
                }
            });
        });

        $('#newQuestionForm').parsley();

        $('#newQuestionForm').on('submit', function(e) {
            e.preventDefault();
            $('#examCode').attr('required', 'required');
            $('#domainId').attr('required', 'required');
            $('#questionText').attr('required', 'required');
            $('#optionText1').attr('required', 'required');
            $('#optionText2').attr('required', 'required');
            $('#optionText3').attr('required', 'required');
            $('#optionText4').attr('required', 'required');
            $('#questionAnswer').attr('required', 'required');

            if($('#newQuestionForm').parsley().validate()) {
                $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#addNewQuestion').attr('disabled', 'disabled');
                        $('#addNewQuestion').val('validating...')
                    },
                    success: function(data) {
                        if(data.success) {
                            $('#message_display').html('<span class="alert alert-success d-block">'+data.success+'</span>');
                            resetQuestionForm();
						    $('#questionModal').modal('hide');
                            dataTable.ajax.reload();
                        }
                        $('#addNewQuestion').attr('disabled', false);
                        $('#addNewQuestion').val($('#hidden_action').val());
                    }
                });
            }
        });
    });
</script>

<?php
    include('footer.php');
?>