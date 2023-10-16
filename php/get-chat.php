<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
        $output = "";

        function str_openssl_dec($message,$iv){
            $key="chatapp";
            $chiper="AES-128-CTR";
            $option=0;
            $message=openssl_decrypt($message, $chiper, $key, $option, $iv);
            return $message;
        }

        $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
                WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
                OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
        $query = mysqli_query($conn, $sql);
        if(mysqli_num_rows($query) > 0){
            while($row = mysqli_fetch_assoc($query)){
                $iv=hex2bin($row['iv']);
                $message=str_openssl_dec($row['msg'],$iv);

                if($row['outgoing_msg_id'] === $outgoing_id){ //if this is equal to then he is a message sender
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <p>'. $message .'</p>
                                </div>
                                </div>';
                }else{ //he is a message receiver
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$row['img'].'" alt="">
                                <div class="details">
                                    <p>'. $message .'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }else{
        header("location: ../login.php");
    }

?>