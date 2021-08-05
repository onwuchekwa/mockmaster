<?php
    // view_exam.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPrivate();
    $pageName = "MockMaster Online Exam";
    include('header.php');

    $examId = '';
    $examCode = '';
    $examStatus = '';
    $examText = '';
    $examDate = '';
    $examRemainingMinutes = '';

    if(isset($_GET['code'])) {
        $exam->changeExamStatus($_SESSION['candidateId']);
        $examId = $exam->getExamId($_GET['code']);
        $exam->query = "SELECT examStatus, examStartTime, examDuration, ex.examCode, examText FROM exam ex JOIN exam_master em ON ex.examCode = em.examCode WHERE ex.examId = $examId";

        $result = $exam->query_result();

        foreach($result as $row) {
            date_default_timezone_set("Africa/Accra");
            $examStatus = $row['examStatus'];
            $examCode = $row['examCode'];
            $examText = $row['examText'];
            $examStartTime = $row['examStartTime'];
            $examDate = date('j F Y', strtotime($examStartTime));
            $examDuration = $row['examDuration'] . ' minute';
            $examEndTime = strtotime($examStartTime . '+' . $examDuration);
            $examEndTime = date('Y-m-d H:i:s', $examEndTime);
            $currentTime = date('Y-m-d H:i:s', time());
            $examRemainingMinutes = strtotime($examEndTime) - strtotime($currentTime);
        }
    } else {
        header('location: enroll-exam.php');
    }

    if($examStatus == 'Started') {
        $exam->data = array(
            ':candidateId'       => $_SESSION['candidateId'],
            ':examCode'          => $examCode,
            ':attendanceStatus'  => 'present',
            ':examDatetime'      => $examStartTime   
        );

        $exam->query = "UPDATE enrollment SET attendanceStatus = :attendanceStatus WHERE candidateId = :candidateId AND examCode = :examCode AND examDatetime = :examDatetime";

        $exam->execute_query();
?>

<div class="row" id="examTest">
  <div class="col-md-8">
    <div class="card margin">
      <div class="card-header"><h4>MockMasters Online Exam</h4></div>
      <div class="card-body">
        <div id="singleQuestionArea"></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div align="center">
			<div id="exam_timer" data-timer="<?php echo $examRemainingMinutes; ?>" style="max-width:400px; width: 100%; height: 200px;"></div>
		</div>
		<div id="userDetailArea"></div>	
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div id="questionNumberNavArea"></div>
  </div>
</div>

<script>
  $(document).ready(function() {
    var examId = "<?php echo $examId; ?>";
    var examCode = "<?php echo $examCode; ?>";

    loadQuestion();
    questionNavigation();
    loadUserDetails();

    function loadQuestion(questionId = '') {
      $.ajax({
        url: "user_ajax_action.php",
        method: "POST",
        data:{ questionId: questionId, page: 'view_exam', action: 'loadQuestion', examCode: examCode, examId: examId },
        success: function(data) {
          $('#singleQuestionArea').html(data);          
        }
      });
    }

    $(document).on('click', '.next', function(){
      var questionId = $(this).attr('id');
      loadQuestion(questionId);
    });

    $(document).on('click', '.previous', function() {
      var questionId = $(this).attr('id');
      loadQuestion(questionId);
    });

    function questionNavigation() {
      $.ajax({
        url: "user_ajax_action.php",
        method: "POST",
        dataType: 'JSON',
        data:{ examCode: examCode, page: 'view_exam', action: 'question_nav', examId: examId },
        success: function(data) {
          $('#questionNumberNavArea').html(data.output);
          let optionCode = data.optionCode;
          let questionNum = data.questionNum;
          $.each(optionCode, function(optIndex, optValue) {
            if(optValue !== null) {
              $.each(questionNum, function(questIndex, questValue) {
                let optCode = $('#' + questValue).data('code');
                let questId = $('#' + questValue).attr('id');
                if(optCode !== '' || window.optCode === 'undefined') {
                  $('#' + questValue).removeClass('btn-danger');
                  $('#' + questValue).addClass('btn-primary');
                } 

                $('#' + questValue).on('click', function(e) {
                  let testElement = $('#examTest').offset();
                  $('html, body').stop().animate({scrollTop: testElement.top}, 500);
                  e.preventDefault();
                });
              });
            }
          });
        }
      });
    }

    $(document).on('click', '.question_navigation', function() {
      var questionId = $(this).data('question_id');
      loadQuestion(questionId);
    });

    $("#exam_timer").TimeCircles({ 
      time:{
        Days:{
          show: false
        }
      }
    });

    setInterval(function() {
      var remaining_second = $("#exam_timer").TimeCircles().getTime();
      if(remaining_second == 30) {
        alert('Your exam will end in 30 minutes. Click OK to continue');
      }

      if(remaining_second == 5) {
        alert('Your exam will end in 5 minutes. Click OK to continue');
      }

      if(remaining_second < 1) {
        alert('Sorry! Your time is up. Click OK to end exam');
        $.ajax({
          url: "user_ajax_action.php",
          method: "POST",
          data:{ page: 'view_exam', action: 'autoStartEndExam', examId: examId },
          success: function(data) {
            location.reload();
          }
        });
      }
    }, 1000);

    $(document).on('click', '.end', function(){
      var endMock = confirm('This action cannot be reversed once you click on OK. Are you sure you end the exam?');
      if(endMock == true) {        
        $.ajax({
          url: "user_ajax_action.php",
          method: "POST",
          data:{ page: 'view_exam', action: 'endExam', examId: examId },
          success: function(data) {
            location.reload();
          }
        });
        return true;
      } else {
        return false;
      }
    });
    
    function loadUserDetails() {
      $.ajax({
        url: "user_ajax_action.php",
        method: "POST",
        data:{ page: 'view_exam', action: 'userDetails' },
        success: function(data) {
          $('#userDetailArea').html(data);
        }
      });
    }

    $(document).on('click', '.answer-option', function() {
      let questionId = $(this).data('question_id');
      let answerOption = $(this).data('id');
      let domainId = $(this).data('domainid');
      $.ajax({
        url:"user_ajax_action.php",
        method:"POST",
        dataType: "JSON",
        data:{ questionId: questionId, answerOption: answerOption, examId: examId, domainId: domainId, page:'view_exam', action: 'candidateAnswer' },
        success:function(data){
          let questCount = data.questNo;           
          let questionNo = $('#' + questCount).attr('id');
          if(questionNo == questCount) {
            $('#' + questCount).removeClass('btn-danger');
            $('#' + questCount).addClass('btn-primary');
          }
        }
      })
    });
  });
</script>

<?php 
    }

    if($examStatus == "Completed") {
      $exam->query = "SELECT domainText, ROUND(SUM(candidateQuestionScore), 0) AS domainExamScore FROM cand_exam_question c JOIN domain d ON c.domainId = d.domainId WHERE c.examId = '$examId' AND  c.candidateId = '$_SESSION[candidateId]' GROUP BY domainText ORDER BY domainText";  

      $result = $exam->query_result();

      $exam->query = "SELECT candidateFirstname, candidateLastname FROM candidate WHERE candidateId = '".$_SESSION['candidateId']."'";

      $candidateResult = $exam->query_result();
      $candidateName = '';
      foreach($candidateResult as $row) {
        $candidateName = $row['candidateFirstname'].' '.$row['candidateLastname'];
      }
      
      $scaleScore = '';
      $exam->query = "SELECT ROUND(COUNT(candidateQuestionScore) * 5.33, 0) AS scaleScore FROM cand_exam_question WHERE candidateId = '".$_SESSION['candidateId']."' AND examId = '".$examId."' AND candidateQuestionScore <> '0.00'";

      $examResult = $exam->query_result();

      foreach($examResult as $row) {
        if($row['scaleScore'] <= '200') {
          $scaleScore = '200';

          if($row['scaleScore'] <= '0') {
            $exam->data = array(
              ':candidateId'       => $_SESSION['candidateId'],
              ':examCode'          => $examCode,
              ':attendanceStatus'  => 'absent',
              ':examDatetime'      => $examStartTime   
            );
    
            $exam->query = "UPDATE enrollment SET attendanceStatus = :attendanceStatus WHERE candidateId = :candidateId AND examCode = :examCode AND examDatetime = :examDatetime";
    
            $exam->execute_query();
          }
        } else {
          $scaleScore = $row['scaleScore'];
        }
      }
?>
  <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">MockMaster's Exam Result</div>
          <div class="col-md-4 text-align-right" style="display: none;">
            <a href="view_exam_detail.php?code=<?php echo $_GET['code']; ?>" class="btn btn-dark btn-sm" target="_blank">View Exam Details</a>
          </div>
          <div class="col-md-2 text-align-right">
            <a href="view_exam_pdf.php?code=<?php echo $_GET['code']; ?>" class="btn btn-danger btn-sm" target="_blank">View Result as PDF</a>
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
          <p>This is to notify you of your <?php echo $examText; ?> mock exam result held on <?php echo $examDate; ?>. A scaled score of 450 or higher is required to pass, which represent the minimum consistent standard of knowledge.</p><p>
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
<?php } include('master/footer.php') ?>