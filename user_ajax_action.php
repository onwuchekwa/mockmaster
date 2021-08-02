<?php
    // user_ajax.action.php

    include('master/Examination.php');

    $exam = new Examination;

    if(isset($_POST['page'])) {
        if($_POST['page'] == 'register') {
            if($_POST['action'] == 'checkEmail') {
                $exam->query = "SELECT * FROM candidate WHERE candidateEmail = '".trim($_POST['email'])."'";

                $total_row = $exam->total_row();

                if($total_row == 0) {
                    $output = array(
                        'success'   =>  true    
                    );
                    echo json_encode($output);
                }
            }
            //End of CheckEmail
            
            if($_POST['action'] == 'register') {
                $candidateVerificationCode = md5(rand());
                $candidateEmail = $_POST['candidateEmail'];

                $exam->data = array(
                    ':candidateEmail'               =>  $candidateEmail,
                    ':candidatePassword'            =>  password_hash($_POST['candidatePassword'], PASSWORD_DEFAULT),
                    ':candidateVerificationCode'    =>  $candidateVerificationCode,
                    ':candidateFirstname'           =>  $_POST['candidateFirstname'],
                    ':candidateLastname'            =>  $_POST['candidateLastname'],
                    ':candidateGender'              =>  $_POST['candidateGender'],
                    ':candidateAddress'             =>  $_POST['candidateAddress'],
                    ':candidatePhone'               =>  $_POST['candidatePhone'],
                );

                $exam->query = "INSERT INTO candidate (candidateEmail, candidatePassword, candidateVerificationCode, candidateFirstname, candidateLastname, candidateGender, candidateAddress, candidatePhone) VALUES (:candidateEmail, :candidatePassword, :candidateVerificationCode, :candidateFirstname, :candidateLastname, :candidateGender, :candidateAddress, :candidatePhone)";

                $exam->execute_query();

                $subject = "The MockMasters' Candidate Registration Verification";

                $body = '
                    <p>Thank you for registering.</p>
                    <p>This is a verification email. Please, verify your email address by clicking this <a href="'.$exam->home_page.'verify_email.php?type=user&code='.$candidateVerificationCode.'" target="_blank"><b>link</b></a>.</p>
                    <p>Your Candidate credential is listed below:</p>
                    <ul>
                        <li>Username: <b>'.$candidateEmail.'</b></li>
                        <li>Password: <b>'.$_POST['candidatePassword'].'</b></li>
                    </ul>
                    <p>In case if you have any difficulty, please email us.</p>
                    <p>Thank you.</p>
                    <p>The MockMasters</p>
                ';

                $exam->send_email($candidateEmail, $subject, $body);

                $output = array(
                    'success'       => true
                );

                echo json_encode($output);
            }
            // Candidate Registration Ends
        }
        // register ends

        if($_POST['page'] == 'login') {
            if($_POST['action'] == 'login') {
                $exam->data = array(
                    ':candidateEmail'       =>  $_POST['candidateEmail']

                );

                $exam->query = "SELECT * FROM candidate WHERE candidateEmail = :candidateEmail";

                $total_row = $exam->total_row();

                if($total_row > 0) {
                    $result = $exam->query_result_new();

                    foreach($result as $row) {
                        if($row['candidateEmailVerified'] == 'yes') {
                            if(password_verify($_POST['candidatePassword'], $row['candidatePassword'])){
                                $_SESSION['candidateId'] = $row['candidateId'];

                                $output = array(
                                    'success'     =>  true
                                );
                            } else {
                                $output = array(
                                    'error'     =>  'Login failed! Check your credentials and try again'
                                );
                            }
                        } else {
                            $output = array(
                                'error'     =>  'Your email is not verified!'
                            );
                        }
                    }
                } else {
                    $output = array(
                        'error'     =>  'Login failed! Check your credentials and try again'
                    );
                }
                echo json_encode($output);
            }
        }
        // Candidate Login Ends

        if($_POST['page'] == 'profile') {
            if($_POST['action'] == 'profile') {
                $exam->data = array(
                    ':candidateFirstname'       =>  $_POST['candidateFirstname'],
                    ':candidateLastname'        =>  $_POST['candidateLastname'],
                    ':candidateGender'          =>  $_POST['candidateGender'],
                    ':candidateAddress'         =>  $_POST['candidateAddress'],
                    ':candidatePhone'           =>  $_POST['candidatePhone'],
                    ':candidateId'              =>  $_SESSION['candidateId']
                );

                $exam->query = "UPDATE candidate SET candidateFirstname = :candidateFirstname, candidateLastname = :candidateLastname, candidateGender = :candidateGender, candidateAddress = :candidateAddress, candidatePhone = :candidatePhone WHERE candidateId = :candidateId";

                $exam->execute_query();

                $output = array(
                    'success'   =>  true
                );

                echo json_encode($output);
            }
        }
        // End of edit profile

        if($_POST['page'] == 'changePassword') {
            if($_POST['action'] == 'changePassword') {
                $exam->data = array(
                    ':candidatePassword'    =>  password_hash($_POST['candidatePassword'], PASSWORD_DEFAULT),
                    ':candidateId'          =>  $_SESSION['candidateId']
                );

                $exam->query = "UPDATE candidate SET candidatePassword = :candidatePassword WHERE candidateId = :candidateId";

                $exam->execute_query();

                session_destroy();

                $output = array(
                    'success'   => 'Your password has been changed successfully'
                );
                echo json_encode($output);
            }
        }
        // End of change password

        if($_POST['page'] == 'index') {
            if($_POST['action'] == 'fetch_exam') {
                $exam->query = "SELECT e.*, examText FROM exam e JOIN exam_master em ON e.examCode = em.examCode WHERE e.examId = '".$_POST['examId']."'";

                $result = $exam->query_result();

                $output = '<div class="card">';
                $output .= '<div class="card-header">Exam Details</div>';
                $output .= '<div class="card-body">';
                $output .= '<span class="alert alert-danger d-block text-justify"><em><strong>Important Information:</strong> In order to enroll in the upcoming mock exam, you must first make a payment of <strong>Two hundred Ghana Cedis (GH¢200.00) only </strong> to the following MTN Mobile Money Number: <strong>0245 073 010.</strong> Please remember to use the exact <strong>email address</strong> you used for registration as the transaction reference number. This is an important step as only candidates who make payment may successfully enroll on the mock exam</em></span>';
                $output .= '<table class="table table-striped table-hover table-bordered">';
                foreach($result as $row) {
                    $output .= '
                    <tr>
                        <td><b>Exam Name</b></td>
                        <td>'.$row["examText"].'</td>
                    </tr>
                    <tr>
                        <td><b>Exam Date & Time</b></td>
                        <td>'.$row["examDatetime"].'</td>
                    </tr>
                    <tr>
                        <td><b>Exam Duration</b></td>
                        <td>'.$row["examDuration"].' Minute(s)</td>
                    </tr>
                    <tr>
                        <td><b>Exam Total Question</b></td>
                        <td>'.$row["examTotalQuestion"].' Question(s)</td>
                    </tr>
                    <tr>
                        <td><b>Exam Minimum Score</b></td>
                        <td>'.$row["examMinScore"].' Mark(s)</td>
                    </tr>
                    <tr>
                        <td><b>Exam Maximum Score</b></td>
                        <td>'.$row["examMaxScore"].' Mark(s)</td>
                    </tr>
                    ';
                    if($exam->alreadyEnrolled($_POST['examId'], $_SESSION['candidateId'])) {
                        $enroll_button = '
                        <tr>
                            <td colspan="2" class="text-center">
                                <button type="button" name="enroll_button" class="btn btn-info">You have already enrolled</button>
                            </td>
                        </tr>
                        ';
                    }
                    else {
                        $enroll_button = '
                        <tr>
                            <td colspan="2" class="text-center">
                                <button type="button" name="enroll_button" id="enroll_button" class="btn btn-warning" data-exam_code="'.$row['examId'].'" data-exam_hash_code="'.$row['examHashCode'].'">Enroll Now</button>
                            </td>
                        </tr>
                        ';
                    }
                    $output .= $enroll_button;
                }
                $output .= '</table>';
                echo $output;
            }

            if($_POST['action'] == 'enroll_exam') {
                $examDatetime = '';
                $examCode = '';
                $exam->query = "SELECT examDatetime, examCode FROM exam WHERE examHashCode = '".$_POST['examHashCode']."'";

                $result = $exam->query_result();

                foreach($result as $row) {
                    $examDatetime = $row['examDatetime'];
                    $examCode = $row['examCode'];
                }

                $examId = $_POST['examId'];
                $candidateId = $_SESSION['candidateId'];

                $exam->data = array(
                    ':examId'           =>  $examId,
                    ':candidateId'      =>  $candidateId,
                    ':examCode'         =>  $examCode,
                    ':examDatetime'     =>  $examDatetime
                );

                $exam->query = "INSERT INTO enrollment (candidateId, examCode, examDatetime, examId) VALUES (:candidateId, :examCode, :examDatetime, :examId)";

                $exam->execute_query();

                $exam->query = "";

                switch($examCode) {
                    case 'CGEIT':
                        $exam->query = "SELECT * FROM (
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CGEIT_DOM1' ORDER BY RAND() LIMIT 38)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CGEIT_DOM2' ORDER BY RAND() LIMIT 30)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CGEIT_DOM3' ORDER BY RAND() LIMIT 24)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CGEIT_DOM4' ORDER BY RAND() LIMIT 36)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CGEIT_DOM5' ORDER BY RAND() LIMIT 22)
                            ) AS CGEIT ORDER BY questionId;
                        ";
                    break;

                    case 'CISA':
                        $exam->query = "SELECT * FROM (
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISA_DOM1' ORDER BY RAND() LIMIT 31)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISA_DOM2' ORDER BY RAND() LIMIT 25)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISA_DOM3' ORDER BY RAND() LIMIT 18)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISA_DOM4' ORDER BY RAND() LIMIT 35)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISA_DOM5' ORDER BY RAND() LIMIT 41)
                            ) AS CISA ORDER BY questionId;
                        ";
                    break;

                    case 'CISM':
                        $exam->query = "SELECT * FROM (
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISM_DOM1' ORDER BY RAND() LIMIT 36)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISM_DOM2' ORDER BY RAND() LIMIT 45)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISM_DOM3' ORDER BY RAND() LIMIT 41)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CISM_DOM4' ORDER BY RAND() LIMIT 28)
                            ) AS CISM ORDER BY questionId;
                        ";
                    break;

                    case 'CRISC':
                        $exam->query = "SELECT * FROM (
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CRISC_DOM1' ORDER BY RAND() LIMIT 39)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CRISC_DOM2' ORDER BY RAND() LIMIT 30)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CRISC_DOM3' ORDER BY RAND() LIMIT 48)
                            UNION
                            (SELECT DISTINCT questionId, q.domainId FROM question q JOIN domain d ON q.domainId = d.domainId WHERE d.examCode = '$examCode' AND d.domainId = 'CRISC_DOM4' ORDER BY RAND() LIMIT 33) 
                            ) AS CRISC ORDER BY questionId;
                        ";
                    break;

                    default:
                        echo 'No record found';
                    break;
                } 
                /*
                $exam->statement = $exam->connect->prepare($exam->query);
                $exam->statement->execute();
                $result = $exam->statement->fetchAll();
                */
                $result = $exam->query_result();
                $questionNo = 1;

                foreach($result as $row) {
                    $exam->data = array(
                        ':candidateId'      =>  $candidateId,
                        ':examId'           =>  $examId,
                        ':examCode'         =>  $examCode,
                        ':domainId'         =>  $row['domainId'],
                        ':questionId'       =>  $row['questionId'],
                        ':questionNumber'   =>  $questionNo++
                    );
                    
                    $exam->query = "INSERT INTO cand_exam_question (candidateId, examId, examCode, domainId, questionId, questionNumber) VALUES (:candidateId, :examId, :examCode, :domainId, :questionId, :questionNumber)";

                    $exam->execute_query();
                }
            }
        }
        // End of Index

        if($_POST['page'] == 'enroll_exam') {
            if($_POST['action'] == 'fetch') {
                $output = array ();

                $exam->query = "SELECT ex.examId, em.examCode, examText, ex.examDatetime, examDuration, examTotalQuestion, examMaxScore, examMinScore, examStatus, examHashCode FROM exam ex JOIN exam_master em ON ex.examCode = em.examCode JOIN enrollment e ON em.examCode = e.examCode AND e.examId = ex.examId WHERE e.candidateId = '".$_SESSION['candidateId']."' AND (";

                if(isset($_POST['search']['value'])) {
                    $exam->query .= 'em.examCode LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR examText LIKE "%'.$_POST["search"]["value"].'%"';

                    $exam->query .= 'OR ex.examDateTime LIKE "%'.$_POST["search"]["value"].'%"';

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
                    $exam->query .= 'ORDER BY ex.examId DESC ';
                }

                $extra_query = '';

                if($_POST['length'] != -1) {
                    $extra_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
                }

                $filtered_rows = $exam->total_row();

                $exam->query .= $extra_query;

                $result = $exam->query_result();

                $exam->query = "SELECT ex.examId, em.examCode, examText, ex.examDatetime, examDuration, examTotalQuestion, examMaxScore, examMinScore, examStatus, examHashCode FROM exam ex JOIN exam_master em ON ex.examCode = em.examCode JOIN enrollment e ON em.examCode = e.examCode AND e.examId = ex.examId WHERE e.candidateId = '".$_SESSION['candidateId']."'";

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

                    if($row['examStatus'] == 'Created') {
                        $status = '<span class="badge badge-success">Created</span>';
                    }

                    if($row['examStatus'] == 'Started') {
                        $status = '<span class="badge badge-primary">Started</span>';
                    }

                    if($row['examStatus'] == 'Completed') {
                        $status = '<span class="badge badge-dark">Completed</span>';
                    }
                    
                    $sub_array[] = $status;
                    $view_exam = '';

                    if($row["examStatus"] == 'Started') {
                        $view_exam = '<a href="view_exam.php?code='.$row["examHashCode"].'" class="btn btn-info btn-sm">View Exam</a>';
                    }
                    if($row["examStatus"] == 'Completed') {
                        $view_exam = '<a href="view_exam.php?code='.$row["examHashCode"].'" class="btn btn-info btn-sm">View Exam</a>';
                    }

                    $sub_array[] = $view_exam;

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
        // End of enroll exam
        if($_POST['page'] == 'view_exam') {
            if($_POST['action'] == 'loadQuestion') {
                if($_POST['questionId'] == '') {
                    $exam->query = "SELECT ce.questionId, q.questionText, q.domainId, ce.questionNumber FROM cand_exam_question ce JOIN question q ON ce.questionId = q.questionId WHERE examCode = '".$_POST["examCode"]."' AND candidateId =  '".$_SESSION["candidateId"]."' AND ce.examId = '".$_POST["examId"]."' ORDER BY ce.questionId ASC LIMIT 1";
                } else {  
                    $exam->query = "SELECT ce.questionId, q.questionText, q.domainId, ce.questionNumber, optionCode FROM cand_exam_question ce JOIN question q ON ce.questionId = q.questionId WHERE ce.questionId = '".$_POST["questionId"]."' AND candidateId =  '".$_SESSION["candidateId"]."' AND ce.examId = '".$_POST["examId"]."'";
                }

                $result = $exam->query_result();
                $output = '';
                
                foreach($result as $row) {
                    $output .= '<h3>Question #'.$row["questionNumber"].'</h3>';
                    $output .= '<hr>';
                    $output .= '<h4>'.$row["questionText"].'</h4>';
                    $output .= '<hr>';
                    $output .=  '<div class="row">';
                    
                    $exam->query = "SELECT * FROM `option` WHERE questionId = '".$row['questionId']."' ORDER BY optionCode ASC";

                    $sub_result = $exam->query_result();

                    $count = 1;

                    foreach($sub_result as $sub_row) {
                        $output .= '<div class="col-md-6 margin-bottom">';
                        $output .= '<div class="radio">';
                        $output .= "<label><h5><input type='radio' name='option_1' class='answer-option' data-question_id='".$row['questionId']."' data-id='".$count."' data-domainid='".$row['domainId']."' />&nbsp;".$sub_row['optionText']."</h5></label>";
                        $output .= '</div></div>';
                        $count = $count + 1;
                    }
                    $output .= '</div>';

                    $exam->query = "SELECT questionId FROM cand_exam_question WHERE questionId < '".$row["questionId"]."' AND examCode = '".$_POST["examCode"]."' AND candidateId =  '".$_SESSION["candidateId"]."' ORDER BY questionId DESC LIMIT 1";

                    $previousQuestion = $exam->query_result();
                    $previousId = '';
                    $nextId = '';

                    foreach($previousQuestion as $prev_row) {
                        $previousId = $prev_row['questionId'];
                    }

                    $exam->query = "SELECT questionId FROM cand_exam_question WHERE questionId > '".$row["questionId"]."' AND examCode = '".$_POST["examCode"]."' AND candidateId =  '".$_SESSION["candidateId"]."' ORDER BY questionId ASC LIMIT 1";

                    $nextQuestion = $exam->query_result();

                    foreach($nextQuestion as $next_row) {
                        $nextId = $next_row['questionId'];
                    }

                    $ifPrevDisabled = '';
                    $ifNextDisabled = '';
                    $endButton = 'style="display: none;"';

                    if($previousId == '') {
                        $ifPrevDisabled = 'disabled';
                    }

                    if($nextId == '') {
                        $ifNextDisabled = 'disabled';
                        $endButton = 'style="display: inline-block;"';
                    }

                    $exam->query = "SELECT o.optionCode, o.optionText FROM cand_exam_question c JOIN `option` o ON c.questionId = o.questionId AND c.optionCode = o.optionCode WHERE c.questionId = '".$row['questionId']."'";

                    $total_row = $exam->total_row();

                    $chosenOption = '';
                    $chosenCode = '';

                    if($total_row > 0) {
                        $result = $exam->query_result_new();
    
                        foreach($result as $option_row) {
                            $chosenOption = $option_row['optionText'];
                            $chosenCode = $option_row['optionCode'];
                        }

                        $output .= '<hr>';
                        $output .= '<div class="center-text">';
                        $output .= "Your choice: <span class='text-primary text-bold'> Option $chosenCode: $chosenOption</span>";
                        $output .= '</div>';
                        $output .= '<hr>';
                    }

                    $output .= '
				  	<div class="center-text">
				   		<button type="button" name="previous" class="btn btn-info btn-lg previous btn-width" id="'.$previousId.'" '.$ifPrevDisabled.'>Previous</button>
				   		<button type="button" name="next" class="btn btn-warning btn-lg next btn-width" id="'.$nextId.'" '.$ifNextDisabled.'>Next</button>
                        <button type="button" name="endExam" class="btn btn-danger btn-lg end btn-width" id="endExam" '.$endButton.'>End Exam</button>
                    </div>';
                }
                echo $output;
            }

            if($_POST['action'] == 'question_nav') {
                $exam->query = "SELECT questionId, questionNumber, optionCode FROM cand_exam_question WHERE examCode = '".$_POST["examCode"]."' AND candidateId = '".$_SESSION["candidateId"]."' AND examId = '".$_POST["examId"]."' ORDER BY questionId ASC";

                $result = $exam->query_result();

                $output = '<div class="card">';
                $output .= '<div class="card-header"><h4>Question Navigation</h4></div>';
                $output .= '<div class="card-body">';
                $output .= '<div class="row">';

                //$count = 1;
                $optionCode = array();
                $questionNum = array();

                foreach($result as $row) {
                    $optionCode[] = $row['optionCode'];
                    $questionNum[] = $row["questionNumber"];
                    $output .= '<div class="col-sm-2 col-md-1 margin-bottom">';
                    $output .= '<button type="button" class="btn btn-danger btn-lg question_navigation nav-btn-width" id="'.$row["questionNumber"].'" data-question_id="'.$row["questionId"].'" data-code="'.$row['optionCode'].'">'.$row['questionNumber'].'</button>';
                    $output .= '</div>';
                    //$count++;
                }
                $output .= '</div></div></div>';

                $output_result = array(
                    'output'        => $output,
                    'optionCode'    => $optionCode,
                    'questionNum'  => $questionNum
                );

                echo json_encode($output_result);                
            }

            if($_POST['action'] == 'userDetails') {
                $exam->query = "SELECT * FROM candidate WHERE candidateId='".$_SESSION['candidateId']."'";
                $result = $exam->query_result();

                $output = '
                <div class="card">
                    <div class="card-header">User Details</div>
                    <div class="card-body">
                        <div class="row">
                ';
                foreach($result as $row) {
                    $candidateGender = '';
                    if($row["candidateGender"] == 'm') {
                        $candidateGender = 'Male';
                    } else {
                        $candidateGender = 'Female';
                    }
                    $output .= '
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>'.$row["candidateFirstname"]. ' '. $row["candidateLastname"].'</td>
                                </tr>
                                <tr>
                                    <th>Email ID</th>
                                    <td>'.$row["candidateEmail"].'</td>
                                </tr>
                                <tr>
                                    <th>Gendar</th>
                                    <td>'.$candidateGender.'</td>
                                </tr>
                            </table>
                        </div>';
                }
                $output .= '</div></div></div>';
                echo $output;
            }

            if($_POST['action'] == 'candidateAnswer') {
                $domainScore = $exam->getDomainScore($_POST['domainId']);
                $correctAnswer = $exam->getCorrectAnswer($_POST['questionId']);
                $candidateQuestionScore = 0;

                if($correctAnswer == $_POST['answerOption']) {
                    $candidateQuestionScore = '+' . $domainScore;
                }
                
                $exam->data = array(
                    ':candidateQuestionScore'   =>  $candidateQuestionScore,
                    ':optionCode'               =>  $_POST['answerOption'],
                    ':domainId'                 =>  $_POST['domainId'],
                    ':questionId'               =>  $_POST['questionId'],
                    ':candidateId'              =>  $_SESSION['candidateId']
                );

                $exam->query = "UPDATE cand_exam_question SET optionCode = :optionCode, candidateQuestionScore = :candidateQuestionScore WHERE domainId = :domainId AND questionId = :questionId AND candidateId = :candidateId";

                $exam->execute_query();

                $exam->query = "SELECT questionId, questionNumber FROM cand_exam_question WHERE domainId = '".$_POST['domainId']."' AND questionId = '".$_POST['questionId']."' AND candidateId = '".$_SESSION['candidateId']."'";

                $result = $exam->query_result();
                $questId = '';
                $questNo = '';

                foreach($result as $class_row) {
                    $questId = $class_row['questionId'];
                    $questNo = $class_row['questionNumber'];
                }

                $output = array(
                    'questId'		=>	$questId,
                    'questNo'		=>	$questNo
                );    
                echo json_encode($output);
            }

            if($_POST['action'] == 'endExam') {
                $exam->data = array(
                    ':examId'       =>  $_POST['examId'],
                    ':examStatus'   =>  'Completed'
                );
                $exam->query = "UPDATE exam SET examStatus = :examStatus WHERE examId = :examId";

                $exam->execute_query();
            }

            if($_POST['action'] == 'autoStartEndExam') {
                $candidateId = $_SESSION['candidateId'];
                $exam->changeExamStatus($candidateId);
            }
        }
        // View Exam ends
    }
?>