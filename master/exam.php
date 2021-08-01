<?php
    //exam.php

    $pageName = 'Exam Management';
    include('header.php');
?>

<div class="card card-margin">
    <div class="card-header">
        <div class="row">
            <div class="col-md-9">
                <h3 class="panel-title">MockMasters Exam List</h3>
            </div>
            <div class="col-md-3 text-align-right">
                <button type="button" id="addExamButton" class="btn btn-info btn-sm">Add New Exam</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <span id="message_operation"></span>
        <div class="table-responsive col-sm-12">
            <table id="examDataTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Exam Code</th>
                        <th>Exam Name</th>
                        <th>Date &amp; Time</th>
                        <th>Duration</th>
                        <th>Total Questions</th>
                        <th>Maximum Score</th>
                        <th>Minimum Score</th>
                        <th>Status</th>
                        <th>Enrolled Candidates</th>
                        <th>Performance Report</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Add & Edit Modal -->
<div class="modal" id="addExamModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="addExamForm">
            <div class="modal-content">
                <!--Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal Body -->
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
                            <label class="col-md-4 text-right">Exam Date & Time <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="examDatetime" name="examDatetime" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4 text-right">Exam Duration <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="examDuration" id="examDuration" class="form-control input-lg">
                                    <option value="">Select Exam Duration</option>
                                    <option value="5">5 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                    <option value="60">1 Hour</option>
                                    <option value="120">2 Hours</option>
                                    <option value="180">3 Hours</option>
                                    <option value="240">4 Hours</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4 text-right">Exam Total Questions <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select name="examTotalQuestion" id="examTotalQuestion" class="form-control input-lg">
                                    <option value="">Select Exam Total Question</option>
                                    <option value="15">15 Questions</option>
                                    <option value="30">30 Questions</option>
                                    <option value="50">50 Questions</option>
                                    <option value="100">100 Questions</option>
                                    <option value="150">150 Questions</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4 text-right">Exam Minimum Score <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="examMinScore" name="examMinScore" readonly value="200" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4 text-right">Exam Maximum Score <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="examMaxScore" name="examMaxScore" readonly value="800" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <input type="hidden" id="examId" name="examId" />
                    <input type="hidden" name="page" value="exam" />
                    <input type="hidden" name="action" id="action" value="Add New Exam" />
                    <input type="submit" class="btn btn-success btn-sm" id="btn_action" name="btn_action" value="Add New Exam" />
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
        var dataTable = $('#examDataTable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
               url: "ajax_action.php",
               method: "POST",
               data: { action: 'fetch', page: 'exam' } 
            },
            "columnDef" : [
                { "targets": [6, 7, 8], "orderable": false }
            ],
        });

        function resetExamForm() {
            $('#modalTitle').text('Add New Exam Details');
            $('#btn_action').val('Add New Exam');
            $('#action').val('Add New Exam');
            $('#addExamForm')[0].reset();
            $('#addExamForm').parsley().reset();
        }

        $('#addExamButton').click(function() {
            resetExamForm();
            $('#addExamModal').modal('show');
            $('#message_operation').html('');
        });

        var date = new Date();
        date.setDate(date.getDate());

        $('#examDatetime').datetimepicker({
            startDate: date,
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true
        });

        $('#addExamForm').parsley();

        $('#addExamForm').on('submit', function(e) {
            e.preventDefault();
            $('#examCode').attr('required', 'required');
            $('#examDatetime').attr('required', 'required');
            $('#examDuration').attr('required', 'required');
            $('#examMinScore').attr('required', 'required');
            $('#examMaxScore').attr('required', 'required');
            $('#examTotalQuestion').attr('required', 'required');

            if($('#addExamForm').parsley().validate()) {
                $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#btn_action').attr('disabled', 'disabled');
                        $('#sbtn_action').val('Validating...');
                    },
                    success: function(data) {
                        if(data.success) {
                            $("#message_operation").html('<span class="alert alert-success d-block">' + data.success + '</span>');
                            resetExamForm();
                            dataTable.ajax.reload();
                            $('#addExamModal').modal('hide');
                        }
                        $('#btn_action').attr('disabled', false);
                        $('#btn_action').val($('#action').val());
                    }
                });
            }
        });

        var examId = '';
        $(document).on('click', '.edit', function() {
           examId =  $(this).attr('id');
           resetExamForm();
           $.ajax({
                url: "ajax_action.php",
                method: "POST",
                data: { action: "edit_fetch", examId: examId, page: "exam" },
                dataType: "json",
                success: function(data) {
                    $('#examCode').val(data.examCode);
                    $('#examDatetime').val(data.examDatetime);
                    $('#examDuration').val(data.examDuration);
                    $('#examTotalQuestion').val(data.examTotalQuestion);
                    $('#examMinScore').val(data.examMinScore);
                    $('#examMaxScore').val(data.examMaxScore);
                    $('#examId').val(examId);
                    $('#modalTitle').text('Edit Exam Details');
                    $('#btn_action').val('Edit Exam');
                    $('#action').val("Edit Exam");
                    $('#addExamModal').modal('show');
                }
           });
        });

        $(document).on('click', '.delete', function() {
            examId = $(this).attr('id');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').click(function() {
            $.ajax({
                url:"ajax_action.php",
                method:"POST",
                data:{examId: examId, action: 'delete', page: 'exam'},
                dataType:"json",
                success:function(data)
                {
                    $('#message_operation').html('<span class="alert alert-success d-block">'+data.success+'</span>');
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