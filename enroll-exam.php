<?php
    // index.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPrivate();
    $pageName = "Enroll Exam";
    include('header.php');

    $examId = 2; //$SESSION['lastInsertId'];
    $candidateId = $_SESSION['candidateId'];
?>
<!-- Index page for logged in Users -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 card-container card-margin">
        <label>Exam Name</label>
        <select name="examId" id="examId" class="form-control input-lg">
            <option value="">Select an Exam</option>
            <?php echo $exam->populateExamList($candidateId); ?>
        </select>
        <br />
        <div id="examDetails"></div>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
$(document).ready(function() {
    
    $('#examId').parsley();
    var examId = '';

    $('#examId').change(function() {
        $('#examId').attr('required', 'required');

        if($('#examId').parsley().validate()) {
            examId = $('#examId').val();
            $.ajax({
                url: "user_ajax_action.php",
                method: "POST",
                data:{ action: 'fetch_exam', page: 'index', examId: examId },
                success:function(data) {
                    $('#examDetails').html(data);
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

<?php include('master/footer.php') ?>