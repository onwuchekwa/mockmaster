<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require dirname(__DIR__) .'/class/vendor/Exception.php';
    require dirname(__DIR__) .'/class/vendor/PHPMailer.php';
    require dirname(__DIR__) .'/class/vendor/SMTP.php';
    
    class Examination {
        var $host;
        var $username;
        var $password;
        var $database;
        var $connect;
        var $home_page;
        var $query;
        var $data;
        var $statement;

        function __construct() {
            $this->host = 'localhost';
            $this->username = 'mockUser';
            $this->password = '9zkuloxrWjGHUFlM';
            $this->database = 'mockmasters';
            //$this->home_page = 'http://localhost/mockmasters/';
            $this->home_page = 'https://themockmaster.com/';

            $this->connect = new PDO("mysql:host=$this->host; dbname=$this->database", "$this->username", "$this->password");

            session_start();
        }

        function execute_query() {
            $this->statement = $this->connect->prepare($this->query);
            $this->statement->execute($this->data);
        }
/*
        function execute_get_id() {
            $this->statement = $this->connect->prepare($this->query);
            $this->statement->execute($this->data);
            return $this->connect->lastInsertId();
        }
*/
        function total_row() {
            $this->execute_query();
            return $this->statement->rowCount();
        }

        function send_email($receiver_email, $subject, $body) {
            /*
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = '465';
            // $mail->SMTPAuth = true;
            $mail->Username = 'donsonde@gmail.com';
            $mail->Password = 'p@55w0rd';
            // $mail->SMTPSecure = 'tls';
            //$mail->From = 'donsonde@gmail.com';
            //$mail->FromName = 'The Mockmasters';
            $mail->setFrom('donsonde@gmail.com', 'The Mockmasters');
            $mail->AddAddress($receiver_email, '');
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            if(!$mail->Send()){
                echo $mail->ErrorInfo;
            }
            */
            
            $mail = new PHPMailer;
            $mail->isSMTP();
            //$mail->Host = 'sxb1plzcpnl473189.prod.sxb1.secureserver.net';
            $mail->Host = 'p3plsmtpa11-02.prod.phx3.secureserver.net';
            $mail->Port = '465';
            //$mail->SMTPAuth = true;
            $mail->Username = 'register@themockmaster.com';
            $mail->Password = 'Pressplay1';
            //$mail->SMTPSecure = 'ssl';
            $mail->setFrom('register@themockmaster.com', 'The Mockmasters');
            //$mail->From = 'register@themockmaster.com';
            //$mail->FromName = 'The Mockmasters';
            $mail->AddAddress($receiver_email, '');
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            if(!$mail->Send()){
                echo $mail->ErrorInfo;
            }
        }

        function redirect($page) {
            header('location: '.$page.'');
            exit;
        }

        function adminSessionPrivate() {
            if (!isset($_SESSION['adminId'])) {
                $this->redirect('login.php');
            }
        }

        function adminSessionPublic() {
            if (isset($_SESSION['adminId'])) {
                $this->redirect('index.php');
            }
        }

        function query_result() {
            //$this->execute_query();
            //return $this->statement->fetchAll();
            $this->statement = $this->connect->prepare($this->query);
            $this->statement->execute();
            return $this->statement->fetchAll();
        }

        function query_result_new() {
            $this->execute_query();
            return $this->statement->fetchAll();
        }

        function getExamList() {
            $this->query = "SELECT examCode, examText FROM exam_master ORDER BY examCode ASC";

            $result = $this->query_result();
            $output = '';
            foreach($result as $row) {
                $output .= '<option value="'.$row["examCode"].'">'.$row["examText"].'</option>';
            }
            return $output;
        }

        function cleanData($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        function IsExamNotStarted($examId) {
            $currentDatetime = date("Y-m-d") . ' ' . date("H:i:s", strtotime(date('h:i:sa')));
            $examDatetime = '';
            $this->query = "SELECT examDatetime FROM exam WHERE examId = '$examId'";
            $result = $this->query_result();
            foreach($result as $row) {
                $examDatetime = $row['examDatetime'];
            }
            if($examDatetime > $currentDatetime) {
                return true;
            }
            return false;
        }

        function questionExists($domainId) {
            $this->query = "SELECT COUNT(*) AS domainQuestionCount FROM question WHERE domainId = '$domainId'";

            $result = $this->query_result();

            foreach($result as $row) {
                $domainQuestionCount = $row['domainQuestionCount'];
            }

            if($domainQuestionCount > 0) {
                return true;
            }
            return false;
        }

        function getDomainList($examCode) {
            $this->query = "SELECT domainId, domainText FROM domain WHERE examCode = '$examCode' ORDER BY domainText ASC";

            $result = $this->query_result();
            $output = '';            
            $output .= '<option value="">Select a Domain</option>';
            foreach($result as $row) {
                $output .= '<option value="'.$row["domainId"].'">'.$row["domainText"].'</option>';
            }
            return $output;
        }

        function executeQuestionWithLastId() {
            $this->statement = $this->connect->prepare($this->query);
            $this->statement->execute($this->data);
            return $this->connect->lastInsertId();
        }

        function getExamId($examHashCode) {
            $this->query = "SELECT examId FROM exam WHERE examHashCode = '$examHashCode'";

            //$result = $this->query_result();
            $this->statement = $this->connect->prepare($this->query);
            $this->statement->execute();
            $result = $this->statement->fetchAll();

            foreach($result as $row) {
                return $row['examId'];
            }
        }

        function getDomainId($domanHashCode) {
            $this->query = "SELECT domainId FROM domain WHERE domainHashCode = '$domanHashCode'";

            $result = $this->query_result();

            foreach($result as $row) {
                return $row['domainId'];
            }
        }

        function candidateSessionPrivate(){
            if(!isset($_SESSION['candidateId'])) {
                $this->redirect('login.php');
            }
        }

        function candidateSessionPublic(){
            if(isset($_SESSION['candidateId'])) {
                $this->redirect('index.php');
            }
        }

        function populateExamList($candidateId) {
            $this->query = "SELECT e.examId, examText FROM exam e JOIN exam_master em ON e.examCode = em.examCode WHERE (examStatus = 'Created' OR examStatus = 'Pending') AND candidateId = $candidateId ORDER BY em.examCode ASC";

            $result = $this->query_result();
            $output = '';
            foreach($result as $row) {
                $output .= '<option value="'.$row["examId"].'">'.$row["examText"].'</option>';
            }
            return $output;
        }

        function alreadyEnrolled($examId, $candidateId) {
            $this->query = "SELECT * FROM enrollment WHERE examId = '$examId' AND candidateId = '$candidateId' AND attendanceStatus = 'not due'";

            if($this->total_row() > 0) {
                return true;
            }
            return false;
        }

        function changeExamStatus($candidateId) { 
            date_default_timezone_set("Africa/Accra");
            $current_datetime = date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')));

            $this->query = "SELECT ex.examDateTime, ca.hasCandidatePaid FROM exam ex JOIN candidate ca ON ca.candidateId = ex.candidateId WHERE ex.candidateId = '".$candidateId."' AND ex.examStatus <> 'Completed'";

            $time_result = $this->query_result();  
            $original_time = '';
            $hasCandidatePaid = '';

            foreach($time_result as $time_row){
                $original_time = $time_row["examDateTime"];
                $hasCandidatePaid = $time_row['hasCandidatePaid'];
            }

            if($hasCandidatePaid == 'yes') {
                if($current_datetime >= $original_time) {            
                    $this->query = "UPDATE exam ex, enrollment en SET ex.examStartTime = '".$current_datetime."', en.examDateTime = '".$current_datetime."' WHERE en.examId = en.examId AND ex.candidateId = en.candidateId AND en.candidateId = '".$candidateId."' AND ex.examStatus <> 'Completed' AND ex.examStartTime IS NULL";
    
                    $this->execute_query();
                }

                $this->query = "SELECT ex.examId, ex.examDatetime, ex.examStartTime, ex.examDuration FROM enrollment en INNER JOIN exam ex ON ex.examId = en.examId AND ex.candidateId = en.candidateId WHERE en.candidateId = '".$candidateId."' AND ex.examStatus <> 'Completed'";

                $result = $this->query_result();            

                foreach($result as $row) {
                    $exam_start_time = $row["examDatetime"];

                    $actual_start_time = $row['examStartTime'];

                    $duration = $row["examDuration"] . ' minutes';

                    $exam_end_time = strtotime($actual_start_time . '+' . $duration);

                    $exam_end_time = date('Y-m-d H:i:s', $exam_end_time);

                    $view_exam = '';

                    if(($current_datetime >= $exam_start_time || $current_datetime >= $actual_start_time) && $current_datetime <= $exam_end_time) {
                        //exam started
                        $this->data = array(
                            ':examStatus'	 =>	'Started'
                        );
                        
                        $this->query = "UPDATE exam SET examStatus = :examStatus WHERE examId = '".$row['examId']."' AND examStatus <> 'Completed'";

                        $this->execute_query();
                    } else {
                        if($current_datetime > $exam_end_time) {
                            //exam completed
                            $this->data = array(
                                ':examStatus'	        =>	'Completed',
                                ':hasCandidatePaid'     => 'no'
                            );

                            $this->query = "UPDATE exam e, candidate c SET examStatus = :examStatus, hasCandidatePaid = :hasCandidatePaid WHERE c.candidateId = e.candidateId AND examId = '".$row['examId']."' AND examStatus = 'Started'";

                            $this->execute_query();
                        }					
                    }
                }
            }
        }

        function getDomainScore($domainId) {
            $this->query = "SELECT domainScorePerQuestion FROM domain WHERE domainId = '". $domainId . "'";
            $result = $this->query_result();
            foreach($result as $row) {
                return $row['domainScorePerQuestion'];
            }
        }

        function getCorrectAnswer($questionId) {
            $this->query = "SELECT questionAnswer FROM question WHERE questionId = '". $questionId . "'";

            $result = $this->query_result();

            foreach($result as $row)
            {
                return $row['questionAnswer'];
            }
        }

        function getExamStatus($examId) {
            $this->query = "SELECT examStatus FROM exam WHERE examId = '".$examId."'";
            $result = $this->query_result();
            foreach($result as $row) {
                return $row["examStatus"];
            }
        }

        function getAttendanceStatus($examId, $candidateId){
            $this->query = "SELECT attendanceStatus FROM enrollment WHERE examId = '$examId' AND candidateId = '$candidateId'";
            $result = $this->query_result();
            foreach($result as $row) {
                return $row["attendanceStatus"];
            }
        }
    }
?>