<?php
    // view_exam_detail.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPrivate();
    $pageName = "MockMaster Online Exam";
    include('header.php');

    $examId = '';
    $examCode = '';
    $examStatus = '';

    if(isset($_GET['code'])) {
        $examId = $exam->getExamId($_GET['code']);
        $exam->query = "SELECT examStatus, examCode FROM exam WHERE examId = $examId";

        $result = $exam->query_result();

        foreach($result as $row) {
            $examStatus = $row['examStatus'];
            $examCode = $row['examCode'];
        }
    } else {
        header('location: enroll-exam.php');
    }

    if($examStatus == "Completed") {
      $exam->query = "SELECT * FROM question q JOIN cand_exam_question c ON q.questionId = c.questionId JOIN domain d ON c.domainId = d.domainId JOIN exam e ON d.examCode = e.examCode WHERE e.examId = '$examId' AND  c.candidateId = '$_SESSION[candidateId]'";  

      $result = $exam->query_result();
?>
  <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-8">Exam Report Details</div>
          <div class="col-md-4 text-align-right">
            <a href="view_exam_pdf.php?code=<?php echo $_GET['code']; ?>" class="btn btn-danger btn-sm" target="_blank">View Result as PDF</a>
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

                if($row['candidateQuestionScore'] == '0.00' || $row['candidateQuestionScore'] === NULL ) {
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
<?php } include('../mockmasters/master/footer.php') ?>