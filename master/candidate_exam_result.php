<?php 
    // candidate_exam_result.php

    $pageName = 'Candidate Exam Result';
    include('header.php');

    $examId = $exam->getExamId($_GET['code']);
    $candidateId = $_GET['candidateId'];
    $examCode = '';
    $examText = '';
    $examDate = '';

    $exam->query = "SELECT examStatus, examDatetime, examDuration, ex.examCode, examText FROM exam ex JOIN exam_master em ON ex.examCode = em.examCode WHERE ex.examId = $examId";

    $exam_result = $exam->query_result();

    foreach($exam_result as $exam_row) {
        date_default_timezone_set("Africa/Accra");
        $examCode = $exam_row['examCode'];
        $examText = $exam_row['examText'];
        $examStartTime = $exam_row['examDatetime'];
        $examDate = date('j F Y', strtotime($examStartTime));
    }

    $exam->query = "SELECT domainText, ROUND(SUM(candidateQuestionScore), 0) AS domainExamScore FROM cand_exam_question c JOIN domain d ON c.domainId = d.domainId WHERE c.examId = '$examId' AND  c.candidateId = '$candidateId' GROUP BY domainText ORDER BY domainText";  

    $result = $exam->query_result();

    $exam->query = "SELECT candidateFirstname, candidateLastname FROM candidate WHERE candidateId = '".$candidateId."'";

    $candidateResult = $exam->query_result();
    $candidateName = '';
    foreach($candidateResult as $row) {
    $candidateName = $row['candidateFirstname'].' '.$row['candidateLastname'];
    }
    
    $scaleScore = '';
    $exam->query = "SELECT ROUND(COUNT(candidateQuestionScore) * 5.33, 0) AS scaleScore FROM cand_exam_question WHERE candidateId = '".$candidateId."' AND examId = '".$examId."' AND candidateQuestionScore <> '0.00'";

    $examResult = $exam->query_result();

    foreach($examResult as $row) {
        if($row['scaleScore'] <= '200') {
            $scaleScore = '200';
        } else {
            $scaleScore = $row['scaleScore'];
        }
    }
?>
<br />
<nav aria-label="breadcrumb">
  	<ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="enrolled_candidate.php">Enrolled Candidate List</a></li>
    	<li class="breadcrumb-item active" aria-current="page">Candidate Exam Result</li>
  	</ol>
</nav>
<div class="card card-margin">
    <div class="card-header">
    <div class="row">
        <div class="col-md-6">MockMaster's Exam Result</div>
        <div class="col-md-4 text-align-right">
        <a href="candidate_exam_detail.php?code=<?php echo $_GET['code']; ?>&candidateId=<?php echo $_GET['candidateId']; ?>" class="btn btn-dark btn-sm" target="_blank">View Exam Details</a>
        </div>
        <div class="col-md-2 text-align-right">
        <a href="candidate_exam_pdf.php?code=<?php echo $_GET['code']; ?>&candidateId=<?php echo $_GET['candidateId']; ?>" class="btn btn-danger btn-sm" target="_blank">View Result as PDF</a>
        </div>
    </div>
    </div>
    <div class="card-body">
    <div class="center-text">
        <h2><?php echo $examText . ' (' . $examCode . ')'; ?></h2>
        <h3>Notice of Mock Result</h3>
        <h5>TESTED on <?php echo $examDate; ?></h5>
    </div>
    <div>
        <p><?php echo $candidateName; ?></p>
        <p>This is to notify you of your <?php echo $examText . ' (' . $examCode . ')'; ?> mock exam result held on <?php echo $examDate; ?>. A scaled score of 450 or higher is required to pass, which represent the minimum consistent standard of knowledge.</p><p>
        <?php
            if($scaleScore < 450) {
              echo 'We regret to inform you that you <strong class="badge badge-danger">FAILED</strong> the exam. We encourage you to study a little harder and wish you the very best on the international exam.</p>';
            } else {
              echo 'We are pleased to inform you that you successfully <strong class="badge badge-success">PASSED</strong> the exam. We wish you the very best on the international exam</p>';
            }
        ?>
        <div class="table-responsive center-text">
        <table class="table table-bordered">
            <tr>
                <th><h3>Your Total Scaled Score is<br><?php echo $scaleScore; ?></h3></td>
            </tr>
        </table>
        </div>
        <p>For your information, your exam results by area are provided below.</p>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless">
        <tr>
            <th colspan="2" class="center-text">SCALED SCORES OF YOUR PERFORMANCE BY AREA</th>
        </tr>
        <?php
            foreach($result as $row) {
            $domainScore = '';

            if($row['domainExamScore'] <= '200') {
                $domainScore = '200';
            } else {
                $domainScore = $row['domainExamScore'];
            }

            echo '<tr><td>'.$row['domainText'].'</td>';
            echo '<td>'.$domainScore.'</td></tr>';
            }
        ?>
        </table>
    </div> 
    <div>
        <p>The above represents a conversion of individually weighted raw scores based on a common scale. As such, do not attempt to apply a simple arithmetic mean to convert area scores to your total scaled score.</p>
    </div>
    <div class="card-footer">
        <p>This report is computer-generated. Therefore, no signature or stamp is required.</p>
        <p>Best Regards,<br>Mock Team</p>
    </div>
    </div>
</div>
<?php include('footer.php') ?>