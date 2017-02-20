<?php
//
session_start();
set_time_limit (0);
header("Content-type: text/html; charset=utf-8");

require_once('PHPMailer/class.phpmailer.php');

define('DOC', $_SERVER['DOCUMENT_ROOT'] . strtok($_SERVER['REQUEST_URI'],'?'));
define('HOST', 'http://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'],'?'));


if (!empty($_POST) && !isset($sent)) {

    $uploadedFiles = [];
    if (isset($_FILES['files'])) {
        foreach ($_FILES["files"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $uploadedFiles[] = [
                    'tmp_name' => $_FILES['files']['tmp_name'][$key],
                    'name' => basename($_FILES['files']['name'][$key])
                ];
            }
        }
    }

    $emailer_subj       = $_POST['emailer_subj'];
    $emailer_mails      = $_POST['emailer_mails'];
    $emailer_text       = $_POST['emailer_text'];
    $emailer_yourmail   = $_POST['emailer_yourmail'];

    $mail_msg='';
    if (empty($emailer_subj) || $emailer_subj=="Тема письма") { $mail_msg.='<b>Вы не ввели тему письма</b><br>';} 
    if (empty($emailer_mails) || $emailer_mails=="Почтовые адреса(через запятую)") { $mail_msg.='<b>Не указано адреса получателей</b><br>';} 
    if (empty($emailer_text)) { $mail_msg.='<b>Вы не ввели текст письма</b><br>';} 
    
    if (empty($emailer_yourmail) || $emailer_yourmail=="Ваша почта"){ $mail_msg.='<b>Не указан адрес отправителя</b>';}
    elseif (!filter_var($emailer_yourmail, FILTER_VALIDATE_EMAIL)) { $mail_msg.='<b>Некорректный адрес отправителя</b>';}

    if (empty($mail_msg)) {        
        $emails=explode(",", $emailer_mails);
        $count_emails = count($emails);

        $report = array(
            'error' => array(),
            'success' => array()
        );

        for ($i=0; $i<$count_emails; $i++)
        {
            $email = trim($emails[$i]);
            if($email == "") continue;
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $report['error'][] = $emails[$i];
                continue; 
            }

            $mailer = new PHPMailer();
            $mailer->CharSet  = 'UTF-8';
            $mailer->From      =  $emailer_yourmail;
            $mailer->Subject   =  $emailer_subj;
            $mailer->Body      =  $emailer_text;
            $mailer->AddAddress( $email );
            $mailer->isHTML(true);

            if(!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $file) {
                    $mailer->AddAttachment( $file['tmp_name'] , $file['name'] );
                }
            }

            if($mailer->send())
                $report['success'][] = $emails[$i];
            else
                $report['error'][] = $emails[$i]; 

             if ($i != $count_emails-1)
                sleep(5);
            
        }
        $_SESSION['error'] = $report['error'];
        $_SESSION['success'] = $report['success'];

        $ret_uri=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header("Refresh: 0; URL=http://".$ret_uri."?messent");
        exit;
    }
}

if(isset($_GET['messent'])) {
    $report['error'] = $_SESSION['error'];
    $report['success'] = $_SESSION['success'];
    include DOC.'html/result.html';
} else {
    include DOC.'html/form.html';
}

unset($_SESSION['error']);
unset($_SESSION['success']);

?>
