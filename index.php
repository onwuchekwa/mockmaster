<?php
    // index.php
    include('master/Examination.php');

    $exam = new Examination;

    $pageName = "Candidate Home";
    include('header.php');

    if(isset($_SESSION['candidateId'])) {
?>
<!-- Index page for logged in Users -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 card-container card-margin">
        <form method="POST" id="addExamForm">
            <label>Exam Name</label>
            <select name="examCode" id="examCode" class="form-control input-lg">
                <option value="">Select an Exam</option>
                <?php echo $exam->getExamList(); ?>
            </select>
            <br />            
            <div class="card d-none" id="examInfo">
                <span id="message_operation"></span>
                <div class="card-header">Exam Details</div>
                    <div class="card-body">
                        <div id="examPayInfo"></div>
                        <div id="examDetails">
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-md-4 text-right">Exam Name</label>
                                    <div class="col-md-8">
                                        <label class="text-left" id="examName" name="examName"></label>
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
                                    <label class="col-md-4 text-right">Exam Duration</label>
                                    <div class="col-md-8">
                                        <label class="text-left">240 Minutes (4 Hours)</label>
                                        <input type="hidden" class="form-control" id="examDuration" name="examDuration" value="240" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <label class="col-md-4 text-right">Exam Total Questions</label>
                                    <div class="col-md-8">
                                        <label class="text-left">150 Questions</label>
                                        <input type="hidden" class="form-control" id="examTotalQuestion" name="examTotalQuestion" value="150" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <!--<input type="hidden" id="examId" name="examId" />-->
                                <input type="hidden" name="page" value="index" />
                                <input type="hidden" name="action" id="action" value="Schedule Exam" />
                                <input type="submit" class="btn btn-success btn-sm" id="btn_action" name="btn_action" value="Schedule Exam" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
$(document).ready(function() {
    $('#examId').parsley();
    var examId = '';

    var date = new Date();
    date.setDate(date.getDate());

    $('#examDatetime').datetimepicker({
        startDate: date,
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true
    });

    $('#examCode').change(function() {
        $('#examCode').attr('required', 'required');

        if($('#examCode').parsley().validate()) {
            examCode = $('#examCode').val();
            $.ajax({
                url: "user_ajax_action.php",
                dataType: 'JSON',
                method: "POST",
                data:{ action: 'load_exam_form', page: 'index', examCode: examCode },
                success:function(data) {
                    $('#examName').text(data.examText);
                    $('#examPayInfo').html(data.output);
                    if($('#examCode').val() != ""){
                        $('#examInfo').removeClass('d-none');
                        $('#examInfo').addClass('d-block');
                        $('#examDatetime').val('');
                    } else {
                        $('#examInfo').removeClass('d-block');
                        $('#examInfo').addClass('d-none');
                    }
                }
            });
        }
    });

    $('#addExamForm').parsley();

    $('#addExamForm').on('submit', function(e) {
        e.preventDefault();
        $('#examDatetime').attr('required', 'required');

        if($('#addExamForm').parsley().validate()) {
            $.ajax({
                url: "user_ajax_action.php",
                method: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                beforeSend: function() {
                    $('#btn_action').attr('disabled', 'disabled');
                    //$('#btn_action').val('Validating...');
                },
                success: function(data) {
                    if(data.success) {
                        $("#message_operation").html('<span class="alert alert-success d-block">' + data.success + '</span>');
                        //resetExamForm();
                        //dataTable.ajax.reload();
                        //$('#addExamModal').modal('hide');
                    } else {
                        $("#message_operation").html('<span class="alert alert-danger d-block">' + data.error + '</span>');
                    }
                    $('#btn_action').attr('disabled', false);
                    $('#btn_action').val($('#action').val());
                }
            });
        }
    });

    $(document).on('click', '#enroll_button', function() {
        examId = $('#enroll_button').data('exam_code');
        examHashCode = $('#enroll_button').data('exam_hash_code');
        $.ajax({
            url: "user_ajax_action.php",
            method: "POST",
            data:{ action: 'enroll_exam', page: 'index', examId: examId, examHashCode: examHashCode },
            beforeSend:function() {
                $('#enroll_button').attr('disabled', 'disabled');
                $('#enroll_button').text('please wait...');
            },
            success:function() {
                $('#enroll_button').removeClass('btn-warning');
                $('#enroll_button').addClass('btn-success');
                $('#enroll_button').text('Exam Enrolled Successfully');
                $('#enroll_button').attr('disabled', 'disabled');
            }
        });
    });

});
</script>

<?php } else { ?>
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />

    <div class="text-center">
        <p><a href="register.php" class="btn btn-warning btn-lg" title="Register">Register</a></p>
        <p><a href="login.php" class="btn btn-dark btn-lg" title="Login">Login</a></p>
    </div>

    <br />
    <br />
    <br />
    <br />
    <br />
<?php } include('master/footer.php') ?>