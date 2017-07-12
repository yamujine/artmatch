'use strict';

$(function () {
  // 그리드 아이템 클릭
  $(document).on('click', '.item .wrap', function () {
    var type = $(this).data('type');
    var id = $(this).data('id');

    location.href = '/' + type + '/' + id;

    return false;
  })
  // 카드 클릭 이벤트와 안겹치도록 이벤트 버블링 방지
    .on('click', '.grid .wrap a', function (event) {
      event.stopPropagation();
    });

  // 비밀번호 변경
  $('#passwordChangeForm').submit(function (event) {
    event.preventDefault();

    var currentPasswordElement = $("input[name=currentPassword]");
    var newPasswordElement = $("input[name=newPassword]");

    if (!currentPasswordElement.val()) {
      alert('기존 비밀번호를 입력해 주세요');
      currentPasswordElement.focus();

      return false;
    }

    if (!newPasswordElement.val()) {
      alert('새로운 비밀번호를 입력해 주세요');
      newPasswordElement.focus();

      return false;
    }

    $.ajax({
      method: "POST",
      url: "/api/users/change_password",
      data: {
        current_password: currentPasswordElement.val(),
        new_password: newPasswordElement.val()
      }
    }).done(function (data) {
      if (!data.result) {
        alert(data.body.message);

        return false;
      }

      alert('비밀번호가 성공적으로 변경되었습니다');
      $('#passwordChangeModal').modal('hide');
    }).fail(function () {
      alert('서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.');
    });

    return false;
  });

  // 프로필 이미지 등록 변경
  $('input[type=file]').change(function () {
    if (!confirm('프로필 이미지를 변경하시겠습니까?')) {
      return false;
    }

    var $this = $(this);

    // 이미지 파일 업로드를 위한 FormData 생성
    var formData = new FormData();
    formData.append("profile_image", $this[0].files[0]);

    $.ajax({
      method: "POST",
      url: "/api/users/update_image",
      processData: false, // To send file
      contentType: false, // To send file
      data: formData
    })
      .done(function (data) {
        if (!data.result) {
          alert(data.body.message);

          return false;
        }
        location.reload(true);
      })
      .fail(function () {
        alert('서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.');
      });
  });

  $('.artworkImage').click(function () {
    location.href = '/artworks/' + $(this).data('id');
    return false;
  });
});
