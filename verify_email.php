<?php

    // verify_email.php
    include('master/Examination.php');
    $exam = new Examination;

    if(isset($_GET['type'], $_GET['code'])) {
       if($_GET['type'] == 'master') {
            $exam->query = "SELECT adminEmailVerified FROM `admin` WHERE adminVerificationCode = '".$_GET['code']."'";

            $result = $exam->query_result();
            $adminVerifyStatus = '';
            foreach($result as $row) {
                $adminVerifyStatus =  html_entity_decode($row['adminEmailVerified']);
            }

            if($adminVerifyStatus != 'yes') {
                $exam->data = array (
                    ':adminEmailVerified'   =>  'yes'
                );

                $exam->query = "UPDATE `admin` SET adminEmailVerified = :adminEmailVerified WHERE adminVerificationCode = '".$_GET['code']."'";

                $exam->execute_query();

                $exam->redirect('master/login.php?verified=success');
            } else {
                $exam->redirect('master/login.php?alreadyverified=success');
            }
       } 

       if($_GET['type'] == 'user') {
            $exam->query = "SELECT candidateEmailVerified FROM `candidate` WHERE candidateVerificationCode = '".$_GET['code']."'";

            $result = $exam->query_result();
            $candidateVerifyStatus = '';
            foreach($result as $row) {
                $candidateVerifyStatus =  html_entity_decode($row['candidateEmailVerified']);
            }

            if($candidateVerifyStatus != 'yes') {
                $exam->data = array (
                    ':candidateEmailVerified'   =>  'yes'
                );

                $exam->query = "UPDATE `candidate` SET candidateEmailVerified = :candidateEmailVerified WHERE candidateVerificationCode = '".$_GET['code']."'";

                $exam->execute_query();

                $exam->redirect('login.php?verified=success');
            } else {
                $exam->redirect('login.php?alreadyverified=success');
            }
        }
    }

?>