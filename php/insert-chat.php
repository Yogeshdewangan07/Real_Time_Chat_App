<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);

        function str_openssl_enc($message,$iv){
            $key="chatapp";
            $chiper="AES-128-CTR";
            $option=0;
            $message=openssl_encrypt($message, $chiper, $key, $option, $iv);
            return $message;
        }

        if(!empty($message)){
            $iv=openssl_random_pseudo_bytes(16);
            $message=str_openssl_enc($message,$iv);
            $iv=bin2hex($iv);

            $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, iv)
                                        VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$iv}')") or die();
        }
    }else{
        header("location: ../login.php");
    }


    
?>