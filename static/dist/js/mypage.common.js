"use strict";$(function(){$(document).on("click",".item .wrap",function(){var a=$(this).data("type"),t=$(this).data("id");return location.href="/"+a+"/"+t,!1}).on("click",".grid .wrap a",function(a){a.stopPropagation()}),$("#passwordChangeForm").submit(function(a){a.preventDefault();var t=$("input[name=currentPassword]"),e=$("input[name=newPassword]");return t.val()?e.val()?($.ajax({method:"POST",url:"/api/users/change_password",data:{current_password:t.val(),new_password:e.val()}}).done(function(a){if(!a.result)return alert(a.body.message),!1;alert("비밀번호가 성공적으로 변경되었습니다"),$("#passwordChangeModal").modal("hide")}).fail(function(){alert("서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.")}),!1):(alert("새로운 비밀번호를 입력해 주세요"),e.focus(),!1):(alert("기존 비밀번호를 입력해 주세요"),t.focus(),!1)}),$("input[type=file]").change(function(){if(!confirm("프로필 이미지를 변경하시겠습니까?"))return!1;var a=$(this),t=new FormData;t.append("profile_image",a[0].files[0]),$.ajax({method:"POST",url:"/api/users/update_image",processData:!1,contentType:!1,data:t}).done(function(a){if(!a.result)return alert(a.body.message),!1;location.reload(!0)}).fail(function(){alert("서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.")})}),$(".artworkImage").click(function(){return location.href="/artworks/"+$(this).data("id"),!1})});