<?php
    // generate_exam_pdf_result.php
    use Dompdf\Dompdf;

    include('Examination.php');
    require_once('../class/dompdf/autoload.inc.php');
    $exam = new Examination;
    
    $examId = '';
    $examCode = '';
    $examText = '';
    $examDate = '';

    if(isset($_GET['code'])) {
        $examId = $exam->getExamId($_GET['code']);
        $exam->query = "SELECT examDatetime, ex.examCode, examText FROM exam ex JOIN exam_master em ON ex.examCode = em.examCode WHERE ex.examId = $examId";

        $result = $exam->query_result();

        foreach($result as $row) {
            date_default_timezone_set("Africa/Accra");
            $examCode = $row['examCode'];
            $examText = $row['examText'];
            $examStartTime = $row['examDatetime'];
            $examDate = date('j F Y', strtotime($examStartTime));
        }

        $exam->query = "SELECT domainText FROM domain d JOIN exam e ON d.examCode = e.examCode WHERE examId = '$examId' ORDER BY domainId";

        $domainHeaderResult = $exam->query_result();

        $exam->query = "SELECT ROUND(SUM(candidateQuestionScore), 0) AS domainExamScore FROM cand_exam_question c JOIN domain d ON c.domainId = d.domainId WHERE c.examId = '$examId' GROUP BY c.domainId ORDER BY c.domainId";  

        $dom_result = $exam->query_result();

        $exam->query = "SELECT candidateFirstname, candidateLastname FROM candidate c JOIN enrollment e ON c.candidateId = e.candidateId WHERE examId = '$examId'";      
        
        $candidateResult = $exam->query_result();
        
        $exam->query = "SELECT ROUND(COUNT(candidateQuestionScore) * 5.33, 0) AS scaleScore FROM cand_exam_question WHERE examId = '$examId' AND candidateQuestionScore <> '0.00' GROUP BY candidateId";

        $examResult = $exam->query_result();

        $htmlOutput = "
        <div style='text-align: center; line-height: 5px;'>
            <h2>$examText</h2>
            <h3>Notice of Mock Result</h3>
            <h5>TESTED on $examDate</h5>
        </div>
      
        <div style='text-align: center; width: 100%;'>
            <h3>Mock Examination Report</h3>
            <table border='1' cellpadding='5' cellspacing='0' width='100%' align='center'>
                <tr>
                <th>Candidate Name</td>
        ";
        foreach($domainHeaderResult as $dom_row) {
            $htmlOutput .= "<th>$dom_row[domainText]</td>";
        }

        $htmlOutput .= "<th>Scaled Score</th>
                </tr>
        ";

        foreach($candidateResult as $cand_row) {
          $candidateName = $cand_row['candidateFirstname'].' '.$cand_row['candidateLastname'];
            $htmlOutput .= "<tr><td>$candidateName</td>";

            foreach($dom_result as $drow) {
                $domainScore = '';
    
                if($drow['domainExamScore'] < '200') {
                    $domainScore = '200';
                } else {
                    $domainScore = $drow['domainExamScore'];
                }
    
                $htmlOutput .= "<td>$domainScore</td>";
            }

            foreach($examResult as $row) {
                $scaleScore = '';

                if($row['scaleScore'] <= '200') {
                    $scaleScore = '200';
                } else {
                    $scaleScore = $row['scaleScore'];
                }
                $htmlOutput .= "<td>$scaleScore</td>";
            } 
            $htmlOutput .= "</tr>";
        }

        $htmlOutput .= "
            </table>
        </div>
        <h6>* This report is computer-generated. Therefore, no signature or stamp is required.</h6>
        <p>Best Regards,<br>The Mockmaster</p>
      ";


      $pdf = new Dompdf();
      //$pdf->set_paper('letter', 'portrait');
      $pdf->setPaper('letter', 'portrait');
      $file_name = $examCode . ' Mock Exam Result.pdf';
      $pdf->loadHtml($htmlOutput);
      $pdf->render();
      $pdf->stream($file_name, array("Attachment" => false));
      exit(0);
    }
?>