
<?php

    // enroll_exam.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPrivate();
    $pageName = "Enrolled Exam";
    $exam->changeExamStatus($_SESSION['candidateId']);
    include('header.php');

?>

<div class="card card-margin">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="panel-title">MockMasters Exam List</h3>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive col-sm-12">
            <table id="userExamDataTable" class="table table-bordered table-striped table-hover">
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
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var dataTable = $('#userExamDataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                url: "user_ajax_action.php",
                type: "POST",
                data:{ action: 'fetch', page: 'enroll_exam' }
            },
            "columnDefs": [{
                    "targets":[8],
                    "orderable":false,
                },
            ],
        });
    });
</script>

<?php include('../mockmasters/master/footer.php') ?>