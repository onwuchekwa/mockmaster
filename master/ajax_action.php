<?php
    // <ajax_action.php
    
    include('Examination.php');

    $exam = new Examination;

    if(isset($_POST['page'])) {
        // Admin Registration Begins
        if($_POST['page'] == 'register') {
            if($_POST['action'] == 'check_email') {
                $exam->query = "SELECT * FROM `admin` WHERE adminEmail = '".trim($_POST["email"])."'";

                $total_row = $exam->total_row();

                if($total_row == 0) {
                    $output = array(
                        'success' => true
                    );
                    echo json_encode($output);
                }
            }

            if($_POST['action'] == 'register') {
                $adminVerificationCode = md5(rand());

                $adminEmail = $_POST['adminEmail'];
                $exam->data = array(
                    ':adminEmail'               =>  $adminEmail,
                    ':adminFirstname'           =>  $_POST['adminFirstname'],
                    ':adminLastname'            =>  $_POST['adminLastname'],
                    ':adminPassword'            =>  password_hash($_POST['adminPassword'], PASSWORD_DEFAULT),
                    ':adminVerificationCode'    =>  $adminVerificationCode
                );

                $exam->query = "INSERT INTO `admin` (adminFirstname, adminLastname, adminEmail, adminPassword, adminVerificationCode) VALUES (:adminFirstname, :adminLastname, :adminEmail, :adminPassword, :adminVerificationCode)";

                $exam->execute_query();

                $subject = 'MockMaster Registration Verification';
                $body = '
                    <p>Thank you for accepting to serve as an administrator for the Mockmasters.</p>
                    <p>This is a verification email. Please, verify your email address by clicking this <a href="'.$exam->home_page.'verify_email.php?type=master&code='.$adminVerificationCode.'" target="_blank"><b>link</b></a>.</p>
                    <p>Your Admin credential is listed below:</p>
                    <ul>
                        <li>Username: <b>'.$adminEmail.'</b></li>
                        <li>Password: <b>'.$_POST['adminPassword'].'</b></li>
                    </ul>
                    <p>In case if you have any difficulty, please email us.</p>
                    <p>Thank you.</p>
                    <p>The MockMaster</p>
                ';

                $exam->send_email($adminEmail, $subject, $body);

                $output = array (
                    'success'   =>  true
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'changePassword') {
                $exam->data = array(
                    ':adminPassword'    =>  password_hash($_POST['adminPassword'], PASSWORD_DEFAULT),
                    ':adminId'          =>  $_SESSION['adminId']
                );

                $exam->query = "UPDATE `admin` SET adminPassword = :adminPassword WHERE adminId = :adminId";

                $exam->execute_query();

                session_destroy();

                $output = array(
                    'success'   => 'Your password has been changed successfully'
                );
                echo json_encode($output);
            }
        }
        // Admin Registration Ends

        // Admin Login Beings
        if($_POST['page'] == 'login') {
            if($_POST['action'] == 'login') {
                $exam->data = array (
                    ':adminEmail'       =>  $_POST['adminEmail']
                );

                $exam->query = "SELECT * FROM `admin` WHERE `adminEmail` = :adminEmail";

                $total_row = $exam->total_row();

                if($total_row > 0) {
                    $result = $exam->query_result_new();

                    foreach($result as $row) {  
                        if($row['adminEmailVerified'] == 'yes') {  
                            if(password_verify($_POST['adminPassword'], $row['adminPassword'])) {
                                $_SESSION['adminId'] = $row['adminId'];
                                $output = array (
                                    'success' =>  true
                                );
                            } else {
                                $output = array (
                                    'error' =>  'Login failed. Check your email address and password!'
                                );
                            }
                        } else {
                            $output = array (
                                'error' =>  'Your email is not verified!'
                            );
                        }
                    }
                } else {
                    $output = array (
                        'error' =>  'Login failed. Check your email address and password!'
                    );
                }
                echo json_encode($output);
            }
        }
        // Admin Login Ends

        // Exam DataTable Begins
        if($_POST['page'] == 'exam') {
            if($_POST['action'] == 'fetch') {
                $output = array ();

                $exam->query = "SELECT e.examHashCode, e.examId, em.examCode, examText, examDatetime, examDuration, examTotalQuestion, examMaxScore, examMinScore, examStatus FROM exam e JOIN exam_master em ON e.examCode = em.examCode WHERE e.adminId = '".$_SESSION['adminId']."' AND (";

                if(isset($_POST['search']['value'])) {
                    $exam->query .= 'em.examCode LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examText LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examDateTime LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examDuration LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examTotalQuestion LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examMaxScore LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examMinScore LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examStatus LIKE "%'.$_POST["search"]["value"].'%"';
                }

                $exam->query .= ')';

                if(isset($_POST['order'])) {
                    $exam->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
                } else {
                    $exam->query .= 'ORDER BY e.examId DESC ';
                }

                $extra_query = '';

                if($_POST['length'] != -1) {
                    $extra_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = 'SELECT e.examHashCode, e.examId, em.examCode, examText, examDatetime, examDuration, examTotalQuestion, examMaxScore, examMinScore, examStatus FROM exam e JOIN exam_master em ON e.examCode = em.examCode WHERE e.adminId = "'.$_SESSION["adminId"].'"';

                $total_rows = $exam->total_row();

                $data = array();

                foreach($result as $row) {
                    $sub_array = array();
                    $sub_array[] = html_entity_decode($row['examCode']);
                    $sub_array[] = html_entity_decode($row['examText']);
                    $sub_array[] = $row['examDatetime'];
                    $sub_array[] = $row['examDuration'] .' Minute(s)';
                    $sub_array[] = $row['examTotalQuestion'] .' Question(s)';
                    $sub_array[] = $row['examMaxScore'] .' Mark(s)';
                    $sub_array[] = $row['examMinScore'] .' Mark(s)';

                    $status = '';
                    $btn_edit = '';
                    $btn_delete = '';
                    $btn_result = '';

                    if($row['examStatus'] == 'Created') {
                        $status = '<span class="badge badge-success">Created</span>';
                    }

                    if($row['examStatus'] == 'Started') {
                        $status = '<span class="badge badge-primary">Started</span>';
                    }

                    if($row['examStatus'] == 'Completed') {
                        $status = '<span class="badge badge-dark">Completed</span>';
                    }

                    if($exam->IsExamNotStarted($row['examId'])) {
                        $btn_edit = '<button type="button" name="edit" class="btn btn-primary btn-sm edit" id="'.$row['examId'].'">Edit</button>';

                        $btn_delete = '<button type="button" name="delete" class="btn btn-danger btn-sm delete" id="'.$row['examId'].'">Delete</button>';
                    } else {
                        $btn_result = '<a href="generate_report.php?code='.$row['examHashCode'].'" class="btn btn-danger btn-sm">Generate Report</a>';
                    }
                    $sub_array[] = $status;

                    $sub_array[] = '<a href="enrolled_candidate.php?code='.$row['examHashCode'].'" class="btn btn-warning btn-sm">View Candidates</a>';

                    $sub_array[] = $btn_result;

                    $sub_array[] = $btn_edit . ' ' . $btn_delete;

                    $data[] = $sub_array;
                } 

                $output = array(
                    "draw"				=>	intval($_POST["draw"]),
                    "recordsTotal"		=>	$total_rows,
                    "recordsFiltered"	=>	$filtered_rows,
                    "data"				=>	$data
                );    
                echo json_encode($output);
            }

            if($_POST['action'] == "Add New Exam") {
                $exam->data = array(
                    ':adminId'              =>      $_SESSION['adminId'],
                    ':examCode'             =>      $_POST['examCode'],
                    ':examDatetime'         =>      $_POST['examDatetime'],
                    ':examDuration'         =>      $_POST['examDuration'],
                    ':examTotalQuestion'    =>      $_POST['examTotalQuestion'],
                    ':examMinScore'         =>      $_POST['examMinScore'],
                    ':examMaxScore'         =>      $_POST['examMaxScore'],
                    ':examStatus'           =>      'Created',
                    ':examHashCode'         =>      md5(rand())
                );

                $exam->query = "INSERT INTO exam (adminId, examCode, examDatetime, examDuration, examTotalQuestion, examMinScore, examMaxScore, examStatus, examHashCode) VALUES (:adminId, :examCode, :examDatetime, :examDuration, :examTotalQuestion, :examMinScore, :examMaxScore, :examStatus, :examHashCode)";

                $exam->execute_query();

                $output = array(
                    'success'   =>  'New exam details added successfully.'
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'edit_fetch') {
                $exam->query = "SELECT * FROM exam WHERE examId = '".$_POST["examId"]."'";

                $result = $exam->query_result();
                foreach($result as $row) {
                    $output['examCode'] = $row['examCode'];
                    $output['examDatetime'] = $row['examDatetime'];
                    $output['examDuration'] = $row['examDuration'];
                    $output['examTotalQuestion'] = $row['examTotalQuestion'];
                    $output['examMinScore'] = $row['examMinScore'];
                    $output['examMaxScore'] = $row['examMaxScore'];
                    $output['examId'] = $row['examId'];
                }
                echo json_encode($output);
            }

            if($_POST['action'] == 'Edit Exam') {
                $exam->data = array (
                    ':examCode'             =>      $_POST['examCode'],
                    ':examDatetime'         =>      $_POST['examDatetime'],
                    ':examDuration'         =>      $_POST['examDuration'],
                    ':examTotalQuestion'    =>      $_POST['examTotalQuestion'],
                    ':examMinScore'         =>      $_POST['examMinScore'],
                    ':examMaxScore'         =>      $_POST['examMaxScore'],
                    ':examId'               =>      $_POST['examId']
                );

                $exam->query = "UPDATE exam SET examCode = :examCode, examDatetime = :examDatetime, examDuration = :examDuration, examTotalQuestion = :examTotalQuestion, examMinScore = :examMinScore, examMaxScore = :examMaxScore WHERE examId = :examId";
                $exam->execute_query($exam->data);
                $output = array (
                    'success'       =>      'Exam details updated successfully.'
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'delete') { 
                $exam->data = array (
                    ':examId'       =>      $_POST['examId']
                );
                $exam->query = "DELETE FROM exam WHERE examId = :examId";
                $exam->execute_query();
                $output = array (
                    'success'       =>  'Exam details has been deleted successfully.'
                );
                echo json_encode($output);
            }
        }
        // Exam DataTable Ends

        // Domain DataTable Begins
        if($_POST['page'] == 'domain') {
            if($_POST['action'] == 'fetch_domain') {
                $output = array();

                $exam->query = "SELECT examCode, domainId, domainText, domainTotalQuestion, domainMaxScore, domainMinScore, domainScorePerQuestion, domainHashCode from domain WHERE (";

                if(isset($_POST['search']['value'])) {
                    $exam->query .= 'examCode LIKE "%'.$_POST['search']['value'].'%"';
                    $exam->query .= 'OR domainText LIKE "%'.$_POST['search']['value'].'%"';
                    $exam->query .= 'OR domainTotalQuestion LIKE "%'.$_POST['search']['value'].'%"';
                    $exam->query .= 'OR domainMaxScore LIKE "%'.$_POST['search']['value'].'%"';
                    $exam->query .= 'OR domainMinScore LIKE "%'.$_POST['search']['value'].'%"';
                    $exam->query .= 'OR domainScorePerQuestion LIKE "%'.$_POST['search']['value'].'%"';
                }

                $exam->query .= ')';

                if(isset($_POST['order'])) {
                    $exam->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
                } else {
                    $exam->query .= 'ORDER BY examCode ASC ';
                }

                $extra_query = '';

                if($_POST['length'] != -1)
                {
                    $extra_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = "SELECT examCode, domainId, domainText, domainTotalQuestion, domainMaxScore, domainMinScore, domainScorePerQuestion, domainHashCode FROM domain";

                $total_rows = $exam->total_row();

                $data = array();

                foreach($result as $row) {
                    $table_array = array();
                    $table_array[] = html_entity_decode($row['examCode']);
                    $table_array[] = html_entity_decode($row['domainText']);
                    $table_array[] = $row['domainTotalQuestion'] . ' Question(s)';
                    $table_array[] = $row['domainMinScore'] . ' Mark(s)';
                    $table_array[] = $row['domainMaxScore'] . ' Mark(s)';
                    $table_array[] = $row['domainScorePerQuestion'] . ' Mark(s)';

                    $btnManageQuestions = '';

                    if($exam->questionExists($row['domainId'])) {
                        $btnManageQuestions = '<a href="question.php?code='.$row['domainHashCode'].'" class="btn btn-info btn-sm" title="Manage Question">Manage Question</a>';
                    }

                    $table_array[] = $btnManageQuestions;
                    $data[] =  $table_array;
                }
                $output = array(
                    "draw"				=>	intval($_POST["draw"]),
				    "recordsTotal"		=>	$total_rows,
				    "recordsFiltered"	=>	$filtered_rows,
				    "data"				=>	$data
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'fetch_domain_detail') {
                $examCode = $_POST['examCode'];
                if($exam->getDomainList($examCode)){
                    echo $exam->getDomainList($examCode); 
                }
            }

            if($_POST['action'] == 'Add Question') {
                $exam->data = array(
                    ':domainId'     =>  $_POST['domainId'],
                    ':questionText'     =>  $_POST['questionText'],
                    ':questionAnswer'     =>  $_POST['questionAnswer'],
                    ':adminId'     =>  $_SESSION['adminId']
                );

                $exam->query = 'INSERT INTO question (domainId, questionText, questionAnswer, adminId) VALUES (:domainId, :questionText, :questionAnswer, :adminId)';

                $questionId = $exam->executeQuestionWithLastId($exam->data);

                for($count = 1; $count <= 4; $count++) {
                    $exam->data = array(
                        ':questionId'       =>      $questionId,
                        ':optionCode'        =>     $count,
                        ':optionText'       =>      $_POST['optionText' . $count],
                        ':adminId'     =>       $_SESSION['adminId']
                    );

                    $exam->query = 'INSERT INTO `option` (questionId, optionCode, optionText, adminId) VALUES (:questionId, :optionCode, :optionText, :adminId)';

                    $exam->execute_query($exam->data);
                }

                $output = array(
                   'success'    =>  'New question has been added'
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'fetch_question') {
                $output = array();
                $domainId = '';
                if(isset($_POST['code'])) {
                    $domainId = $exam->getDomainId($_POST['code']);
                }

                $exam->query = "SELECT questionId, questionText, questionAnswer FROM question WHERE domainId = '$domainId' AND (";

                if(isset($_POST['search']['value'])) {
                    $exam->query .= 'questionText LIKE "%'.$_POST['search']['value'].'%"';
                }

                $exam->query .= ')';

                if(isset($_POST["order"]))
                {
                    $exam->query .= '
                    ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' 
                    ';
                }
                else
                {
                    $exam->query .= 'ORDER BY questionId ASC ';
                }

                $extra_query = '';

                if($_POST['length'] != -1)
                {
                    $extra_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = "SELECT questionId, questionText, questionAnswer FROM question WHERE domainId = '$domainId'";

                $total_rows = $exam->total_row();

                $data = array();

                foreach($result as $row)
                {
                    $sub_array = array();

                    $sub_array[] = $row['questionText'];

                    $sub_array[] = 'Option ' . $row['questionAnswer'];
                    
                    $edit_button = '<button type="button" name="edit" class="btn btn-primary btn-sm edit" id="'.$row['questionId'].'">Edit</button>';

                    $delete_button = '<button type="button" name="delete" class="btn btn-danger btn-sm delete" id="'.$row['questionId'].'">Delete</button>';

                    $sub_array[] = $edit_button . ' ' . $delete_button;

                    $data[] = $sub_array;
                }

                $output = array(
                    "draw"		        =>	intval($_POST["draw"]),
                    "recordsTotal"	    =>	$total_rows,
                    "recordsFiltered"	=>	$filtered_rows,
                    "data"		        =>	$data
                );

                echo json_encode($output);
            }

            if($_POST['action'] == 'edit_fetch') {
                $exam->query = "SELECT questionText, questionAnswer FROM question WHERE questionId = '".$_POST["questionId"]."'";

                $result = $exam->query_result();

                foreach($result as $row) {
                    $output['questionText'] = html_entity_decode($row['questionText']);
                    $output['questionAnswer'] = $row['questionAnswer'];

                    for($count = 1; $count <= 4; $count++) {
                        $exam->query = "SELECT optionText FROM `option` WHERE questionId = '".$_POST['questionId']."' AND optionCode = '".$count."'";

                        $sub_result = $exam->query_result();

                        foreach($sub_result as $sub_row) {
                            $output['optionText'.$count] = html_entity_decode($sub_row['optionText']);
                        }
                    }
                }
                echo json_encode($output);
            }

            if($_POST['action'] == 'Edit Question') {
                $exam->data = array(
                    ':questionText'     =>  $_POST['questionText'],
                    ':questionAnswer'   =>  $_POST['questionAnswer'],
                    ':questionId'       =>  $_POST['questionId']
                );

                $exam->query = "UPDATE question SET questionText = :questionText, questionAnswer = :questionAnswer WHERE questionId = :questionId";

                $exam->execute_query();

                for($count = 1; $count <= 4; $count++) {
                    $exam->data = array(
                        ':questionId'       =>  $_POST['questionId'],
                        ':optionCode'       =>  $count,
                        ':optionText'       => $_POST['optionText'.$count]
                    );

                    $exam->query = "UPDATE `option` SET optionText = :optionText WHERE questionId = :questionId AND optionCode = :optionCode";

                    $exam->execute_query();
                }
                $output = array(
                    'success'       =>  'The question has been amended successfully.'
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'question_delete') {
                $exam->data = array(
                    ':questionId'       =>  $_POST['questionId']
                );

                $exam->query = "DELETE FROM `option` WHERE questionId = :questionId";

                $exam->execute_query();

                $exam->query = "DELETE FROM question WHERE questionId = :questionId";

                $exam->execute_query();

                $output = array(
                    'success'       =>  'Question has been deleted successfully'
                );

                echo json_encode($output);
            }
        }
        // Domain DataTable Ends

        if($_POST['page'] == 'user') {
            if($_POST['action'] == 'fetch') {
                $output = array();
                $exam->query = "SELECT * FROM candidate WHERE ";

                if(isset($_POST["search"]["value"])) {
                    $exam->query .= 'candidateEmail LIKE "%'.$_POST["search"]["value"].'%"';
                    $exam->query .= 'OR candidateFirstname LIKE "%'.$_POST["search"]["value"].'%"';
                    $exam->query .= 'OR candidateLastname LIKE "%'.$_POST["search"]["value"].'%"';
                    $exam->query .= 'OR candidateGender LIKE "%'.$_POST["search"]["value"].'%"';
                    $exam->query .= 'OR candidatePhone LIKE "%'.$_POST["search"]["value"].'%"';
                    $exam->query .= 'OR candidateEmailVerified LIKE "%'.$_POST["search"]["value"].'%"';
                    $exam->query .= 'OR hasCandidatePaid LIKE "%'.$_POST["search"]["value"].'%"';
                }

                if(isset($_POST["order"])) {
                    $exam->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
                }
                else {
                    $exam->query .= 'ORDER BY candidateId DESC ';
                }

                $extra_query = '';

                if($_POST["length"] != -1) {
                    $extra_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = "SELECT * FROM candidate";

                $total_rows = $exam->total_row();

                $data = array();

                foreach($result as $row) {
                    $sub_array = array();                    
                    $isEmailVerified = '';
                    $hasCandidatePaid = '';
                    $candidateGender = '';
                    $sub_array[] = $row['candidateFirstname'];
                    $sub_array[] = $row['candidateLastname'];
                    $sub_array[] = $row['candidateEmail'];
                    if($row['candidateGender'] == 'm') {
                        $candidateGender = 'Male';
                    } else {
                        $candidateGender = 'Female';
                    }
                    $sub_array[] = $candidateGender;
                    $sub_array[] = $row['candidatePhone'];
                    if($row['candidateEmailVerified'] == 'yes') {
                        $isEmailVerified = '<label class=" badge badge-success">Yes</label>';
                    } else {
                        $isEmailVerified = '<label class="badge badge-danger">No</label>';
                    }

                    if($row['hasCandidatePaid'] == 'yes') {
                        $hasCandidatePaid = '<label class="badge badge-success">Yes</label>';
                    } else {
                        $hasCandidatePaid = '<label class="badge badge-danger">No</label>';
                    }

                    $sub_array[] = $isEmailVerified;
                    $sub_array[] = $hasCandidatePaid;
                    $sub_array[] = '<button type="button" name="update" class="btn btn-primary btn-sm update" id="'.$row['candidateId'].'">Change Payment Status</button>';
                    $data[] = $sub_array;
                }
                $output = array(
                    "draw"              =>  intval($_POST["draw"]),
                    "recordsTotal"      =>  $total_rows,
                    "recordsFiltered"   =>  $filtered_rows,
                    "data"              =>  $data
                );
                echo json_encode($output);
            }

            if($_POST['action'] == 'fetch_pay_sts') {
                $exam->query = "SELECT hasCandidatePaid FROM candidate WHERE candidateId = '$_POST[candidateId]'";

                $result = $exam->query_result();

                foreach($result as $row) {
                    $output['hasCandidatePaid'] = $row['hasCandidatePaid'];
                }
                echo json_encode($output);
            }

            if($_POST['action'] == 'Change') {
                $exam->data = array(
                    ':hasCandidatePaid'     =>  $_POST['updatePayment'],
                    ':candidateId'          =>  $_POST['candidateId']
                );

                $exam->query = "UPDATE candidate SET hasCandidatePaid = :hasCandidatePaid WHERE candidateId = :candidateId";

                $exam->execute_query();
                $output = array(
                    'success'       =>  'The payment status has been amended successfully.'
                );
                echo json_encode($output);
            }
        }
        // User DataTable Ends

        if($_POST['page'] == 'exam_enroll') {
            if($_POST['action'] == 'fetch') {
                $output = array();

                $examId = $exam->getExamId($_POST['code']);

                $exam->query = "SELECT * FROM enrollment e INNER JOIN candidate c ON e.candidateId = c.candidateId WHERE e.examId = '".$examId."' AND (";

                if(isset($_POST['search']['value'])) {
                    $exam->query .= 'candidateFirstname LIKE "%'.$_POST["search"]["value"].'%" ';
                    $exam->query .= 'OR candidateLastname LIKE "%'.$_POST["search"]["value"].'%" ';
                    $exam->query .= 'OR candidateGender LIKE "%'.$_POST["search"]["value"].'%" ';
                    $exam->query .= 'OR candidatePhone LIKE "%'.$_POST["search"]["value"].'%" ';
                    $exam->query .= 'OR candidateEmail LIKE "%'.$_POST["search"]["value"].'%" ';
                    $exam->query .= 'OR candidateEmailVerified LIKE "%'.$_POST["search"]["value"].'%" ';
                    $exam->query .= 'OR hasCandidatePaid LIKE "%'.$_POST["search"]["value"].'%" ';
                }
                $exam->query .= ') ';

                if(isset($_POST['order'])) {
                    $exam->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
                } else {
                    $exam->query .= 'ORDER BY e.enrollmentId ASC ';
                }

                $extra_query = '';

                if($_POST['length'] != -1) {
                    $extra_query = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = " SELECT * FROM enrollment e INNER JOIN candidate c ON e.candidateId = c.candidateId WHERE examId = '".$examId."'";

                $total_rows = $exam->total_row();

                $data = array();

                foreach($result as $row) {
                    $isEmailVerified = '';
                    $hasPaid = '';
                    $gender = '';
                    
                    $sub_array = array();
                    $sub_array[] = $row["candidateFirstname"];
                    $sub_array[] = $row["candidateLastname"];

                    if($row["candidateGender"] == "m") {
                        $gender = 'Male';
                    } else {
                        $gender = 'Female';
                    }
                    $sub_array[] =  $gender;
                    $sub_array[] = $row["candidatePhone"];
                    $sub_array[] = $row["candidateEmail"];

                    if($row['candidateEmailVerified'] == 'yes') {
                        $isEmailVerified = '<label class="badge badge-success">Verified</label>';
                    } else {
                        $isEmailVerified = '<label class="badge badge-danger">Not Verified</label>';
                    }
                    $sub_array[] = $isEmailVerified;

                    if($row['hasCandidatePaid'] == 'yes') {
                        $hasPaid = '<label class="badge badge-success">Paid</label>';
                    } else {
                        $hasPaid = '<label class="badge badge-danger">Not Paid</label>';
                    }
                    $sub_array[] = $hasPaid;
                    $result = '';

                    if($exam->getExamStatus($examId) == 'Completed') {
                        $result = '<a href="candidate_exam_result.php?code='.$_POST['code'].'&candidateId='.$row['candidateId'].'" class="btn btn-info btn-sm" target="_blank">Candidate Result</a>';
                    }
                    $sub_array[] = $result;

                    $data[] = $sub_array;
                }

                $output = array(
                    "draw"				=>	intval($_POST["draw"]),
                    "recordsTotal"		=>	$total_rows,
                    "recordsFiltered"	=>	$filtered_rows,
                    "data"				=>	$data
                );

                echo json_encode($output);
            }
        }
        // Enrolled Candidates Ends

        if($_POST['page'] == 'exam_result') {
            if($_POST['action'] == 'fetch') {
                $output = array();
                $examId = $exam->getExamId($_POST["code"]);
                $exam->query = "SELECT ca.candidateId, candidateEmail, candidateFirstname, candidateLastname, ROUND(COUNT(candidateQuestionScore) * 5.33, 0) AS scaleScore FROM cand_exam_question ce INNER JOIN candidate ca ON ca.candidateId = ce.candidateId WHERE examId = '$examId' AND candidateQuestionScore <> '0.00' AND (";

                if(isset($_POST["search"]["value"])){
                    $exam->query .= 'candidateEmail LIKE "%'.$_POST["search"]["value"].'%" ';
                }

                $exam->query .= ') GROUP BY ca.candidateId ';

                if(isset($_POST["order"])) {
                    $exam->query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
                }
                else {
                    $exam->query .= 'ORDER BY scaleScore DESC ';
                }

                $extra_query = '';

                if($_POST["length"] != -1) {
                    $extra_query = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = "SELECT ca.candidateId, candidateEmail, candidateFirstname, candidateLastname, ROUND(COUNT(candidateQuestionScore) * 5.33, 0) AS scaleScore FROM cand_exam_question ce INNER JOIN candidate ca ON ca.candidateId = ce.candidateId WHERE examId = '$examId' AND candidateQuestionScore <> '0.00' GROUP BY ca.candidateId ORDER BY scaleScore DESC;";

                $total_rows = $exam->total_row();

                $data = array();

                foreach($result as $row) {
                    $sub_array = array();
                    $sub_array[] = $row['candidateEmail'];
                    $sub_array[] = $row["candidateFirstname"];
                    $sub_array[] = $row["candidateLastname"];
                    $examStatus = $exam->getAttendanceStatus($examId, $row["candidateId"]);
                    if($examStatus == "present") {
                        $sub_array[] = "<label class='badge badge-success'>Attended</label>";
                    } else if ($examStatus == "absent") {
                        $sub_array[] = "<label class='badge badge-danger'>Absent</label>";
                    } else {
                        $sub_array[] = "<label class='badge badge-info'>Not Due</label>";
                    }                   

                    if($row["scaleScore"] < 200) {
                        $sub_array[] = "<label class='badge badge-danger'>200</label>";
                    } else {
                        if($row["scaleScore"] < 450) {
                            $sub_array[] = "<label class='badge badge-danger'>$row[scaleScore]</label>";
                        } else {
                            $sub_array[] = "<label class='badge badge-success'>$row[scaleScore]</label>";
                        }
                    }
                    
                    $data[] = $sub_array;
                }

                $output = array(
                    "draw"				=>	intval($_POST["draw"]),
                    "recordsTotal"		=>	$total_rows,
                    "recordsFiltered"	=>	$filtered_rows,
                    "data"				=>	$data
                );

                echo json_encode($output);
            }
        }
        // Exam Result Generator
    }
?>