<?php
    // view_exam_pdf.php
    use Dompdf\Dompdf;

    include('Examination.php');
    require_once('../class/dompdf/autoload.inc.php');
    $exam = new Examination;
    
    $examId = '';
    $examCode = '';
    $examStatus = '';
    $examText = '';
    $examDate = '';
    $candidateId = '';

    if(isset($_GET['code'])) {
        $examId = $exam->getExamId($_GET['code']);
        $candidateId = $_GET['candidateId'];
        $exam->query = "SELECT examStatus, examDatetime, ex.examCode, examText FROM exam ex JOIN exam_master em ON ex.examCode = em.examCode WHERE ex.examId = $examId";

        $result = $exam->query_result();

        foreach($result as $row) {
            date_default_timezone_set("Africa/Accra");
            $examStatus = $row['examStatus'];
            $examCode = $row['examCode'];
            $examText = $row['examText'];
            $examStartTime = $row['examDatetime'];
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

      $htmlOutput = "
        <div style='text-align: center;'>
            <h2>$examText ($examCode)</h2>
            <h3>Notice of Mock Result</h3>
            <h5>TESTED on $examDate</h5>
        </div>
      ";

      $htmlOutput .= "
      <div style='text-align: left;'>
        <p>$candidateName</p>
        <p>This is to notify you of your $examText ($examCode) mock exam result held on $examDate. A scaled score of 450 or higher is required to pass, which represent the minimum consistent standard of knowledge.</p><p>
      ";
     
      if($scaleScore < 450) {
        $htmlOutput .= "We regret to inform you that you <strong style='background-color: #dc3545; color: #fffff; padding: 5px;'>FAILED</strong> the exam. We encourage you to study a little harder and wish you the very best on the international exam.</p>";
        } else {
            $htmlOutput .= "We are pleased to inform you that you successfully <strong style='background-color: ##28a745; color: #fffff; padding: 5px;'>PASSED</strong> the exam. We wish you the very best on the international exam</p>";
        }
        
        $htmlOutput .= "
        <div style='text-align: center; width: 100%;'>
        <table border='1' cellpadding='10' cellspacing='0' width='50%' align='center'>
            <tr>
              <th><h3>Your Total Scaled Score is<br>$scaleScore</h3></td>
            </tr>
        </table>
        </div>
        <p>For your information, your exam results by area are provided below.</p>
        </div>
      ";

      $htmlOutput .= "
            <div style='text-align: center; width: 100%'>
            <h3>SCALED SCORES OF YOUR PERFORMANCE BY AREA</h3>
            <table border='0' cellpadding='10' cellspacing='0' width='100%' align='center'>
        ";
        
        foreach($result as $row) {
            $domainScore = '';

            if($row['domainExamScore'] <= '200') {
                $domainScore = '200';
            } else {
                $domainScore = $row['domainExamScore'];
            }

            $htmlOutput .= "<tr><td>$row[domainText]</td>
            <td>$domainScore</td></tr>
            ";
        }

        $htmlOutput .= "
            </table></div><div><p>The above represents a conversion of individually weighted raw scores based on a common scale. As such, do not attempt to apply a simple arithmetic mean to convert area scores to your total scaled score.</p></div>
            <h6>* This report is computer-generated. Therefore, no signature or stamp is required.</h6>
            <p>Best Regards,<br>Mock Team</p>
        ";

      $pdf = new Dompdf();
      $pdf->set_paper('letter', 'portrait');
      $file_name = $candidateName . ' Mock Exam Result.pdf';
      $pdf->loadHtml($htmlOutput);
      $pdf->render();
      $pdf->stream($file_name, array("Attachment" => false));
      exit(0);
    }
?>