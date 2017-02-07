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
    <div>회원종류
        <select name="type" id="type">
            <option value="artist">창작자</option>
            <option value="owner">소유자?</option>
        </select>
    </div>
    <div>
        이메일
        <input id="email"/>
    </div>
    <div>
        패스워드
        <input id="password"/>
    </div>
    <div>
        프로필사진
    </div>
    <div><button id="join">가입</button></div>
</body>

<script>
    $('#join').click(function(){
        $.ajax({
            method:"POST",
            url:"/users",
            data:{
                type:$("#type").val(),
                email:$("#email").val(),
                password:$("#password").val()
            }
        }).done(function(data){
            alert(data);
        })
    });
</script>
</html>