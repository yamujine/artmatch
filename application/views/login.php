<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<!-- Mock -->
<html>
<head>
    <meta charset="utf-8">
    <title>회원가입</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<div>
    이메일
    <input id="email"/>
</div>
<div>
    패스워드
    <input id="password"/>
</div>
<div><button id="join">로그인</button></div>
<div><?php echo $email?> <?php echo $auth?></div>
</body>

<script>
    $('#join').click(function(){
        $.ajax({
            method:"POST",
            url:"/login",
            data:{
                email:$("#email").val(),
                password:$("#password").val()
            }
        }).done(function(data){
            alert(data);
        })
    });
</script>
</html>