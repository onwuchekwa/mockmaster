<?php
    // candidate_exam_detail.php
  
    $pageName = "Candidate Exam Details";
    include('header.php');

    $examId = '';
    $examCode = '';
    $examStatus = '';
    $candidateId = '';

    if(isset($_GET['code'])) {
        $examId = $exam->getExamId($_GET['code']);
        $candidateId = $_GET['candidateId'];
        $exam->query = "SELECT examStatus, examCode FROM exam WHERE examId = $examId";

        $result = $exam->query_result();

        foreach($result as $row) {
            $examStatus = $row['examStatus'];
            $examCode = $row['examCode'];
        }
    }
    $exam->query = "SELECT * FROM question q JOIN cand_exam_question c ON q.questionId = c.questionId JOIN domain d ON c.domainId = d.domainId JOIN exam e ON d.examCode = e.examCode WHERE e.examId = '$examId' AND  c.candidateId = '$candidateId'";  

    $result = $exam->query_result();
?>
<br />
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="candidate_exam_result.php">Candidate Exam Result</a></li>
      <li class="breadcrumb-item active" aria-current="page">Candidate Exam Details</li>
    </ol>
</nav>
<div class="card card-margin">
  <div class="card-header">
    <div class="row">
      <div class="col-md-8">Exam Report Details</div>
      <div class="col-md-4 text-align-right">
        <a href="candidate_exam_pdf.php?code=<?php echo $_GET['code']; ?>&candidateId=<?php echo $_GET['candidateId']; ?>" class="btn btn-danger btn-sm" target="_blank">View Result as PDF</a>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <tr>
          <th>Question</th>
          <th>Option 1</th>
          <th>Option 2</th>
          <th>Option 3</th>
          <th>Option 4</th>
          <th>Your Answer</th>
          <th>Answer</th>
          <th>Result</th>
        </tr>
        <?php
          foreach($result as $row) {
            $exam->query = "SELECT * FROM `option` WHERE questionId = '". $row["questionId"]."'";

            $sub_result = $exam->query_result();
            $userAnswer = '';
            $correctAnswer = '';
            $questionResult = '';

            if($row['candidateQuestionScore'] == '0.00' || $row['candidateQuestionScore'] === NULL) {
              $questionResult = '<h4 class="badge badge-danger">Wrong</h4>';
            } else {
              $questionResult = '<h4 class="badge badge-success">Correct</h4>';
            }

            echo '<tr>
                <td>'.$row['questionText'].'</td>';

            foreach($sub_result as $sub_row) {
              echo '<td>'.$sub_row['optionText'].'</td>';

              if($sub_row['optionCode'] == $row['optionCode']) {
                $userAnswer = $sub_row['optionText'];
              }

              if($sub_row['optionCode'] == $row['questionAnswer']) {
                $correctAnswer = $sub_row['optionText'];
              }
            }
            echo '
              <td>'.$userAnswer.'</td>
              <td>'.$correctAnswer.'</td>
              <td>'.$questionResult.'</td>
            </tr>';
          }
        ?>
      </table>
    </div>
  </div>
</div>
<?php include('footer.php'); ?>