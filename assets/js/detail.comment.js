// 검증
$("#commentRegisterForm").validate({
  rules: {
    comment: "required"
  },
  messages: {
    comment: "코멘트 내용을 입력해 주세요"
  },
  submitHandler: function(form) {
    insert_comment($(form));
  }
});

// 댓글
function insert_comment($this) {
  $.ajax({
    method: "POST",
    url: "/api/comments/insert",
    data: {
      type: $this.find("input[name=type]").val(),
      type_id: $this.find("input[name=type_id]").val(),
      comment: $this.find("textarea[name=comment]").val()
    }
  })
    .done(function (data) {
      if (!data.result) {
        alert("댓글 등록 실패");
        return false;
      }

      $('.comments').append(data.body.insert_comment);
      $this.find('textarea[name=comment]').val('');
      $('.commentArea').children().find('.number').text(data.body.comment_count);
    })
    .fail(function (data) {
      alert(data.body.message);
    });
  return false;
}

// 댓글 수정
$(document).on('click', '.commentEditBtn', function () {
  var comment_id = $(this).data("id");
  $("#commentDiv_" + comment_id).hide();

  var textarea = $("#commentEditDiv_" + comment_id).addClass('active').find('textarea');
  textarea.focus();

  // 커서 뒤로 이동시키기 위해
  var tmpStr = textarea.val();
  textarea.val('');
  textarea.val(tmpStr);
});

// 댓글 수정 취소
$(document).on('click', '.commentEditCancelBtn', function () {
  var comment_id = $(this).data("id");
  $("#commentDiv_" + comment_id).show();
  $("#commentEditDiv_" + comment_id).removeClass('active');
});

// 댓글 수정 저장
$(document).on('click', '.commentEditSubmit', function () {
  var comment_id = $(this).data("id");
  var comment = $('#commentEditDiv_' + comment_id).find('#commentEditText').val();

  function nl2br(str){
    return str.replace(/\n/g, "<br />");
  }

  $.ajax({
    method: "POST",
    url: "/api/comments/update",
    data: {
      type: $(this).data('type'),
      type_comment_id: comment_id,
      comment: comment
    }
  }).done(function (data) {
    if (data.body.result_type === 'update') {
      $("#commentDiv_" + comment_id).show();
      $('#commentEditDiv_' + comment_id).hide();
      $('#comment_' + comment_id).find('.comment').html(nl2br(data.body.comment));
    } else {
      alert(data.body.message);
    }
  }).fail(function () {
    alert('서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.');
  });
});

// 댓글 삭제
$(document).on('click', '.commentDeleteBtn', function () {
  if (confirm('정말 삭제 하시겠습니까?') === false) {
    return false;
  }

  var $this = $(this);
  $.ajax({
    method: 'POST',
    url: '/api/comments/delete',
    data: {
      type: $this.data('type'),
      type_comment_id: $this.data('id')
    }
  }).done(function (data) {
    if (data.body.result_type === 'delete') {
      $('#comment_' + data.body.type_comment_id).remove();
      $('.commentArea .number').text(data.body.comment_count);
    } else {
      alert(data.body.message);
    }
  }).fail(function () {
    alert('서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.');
  });

  return false;
});


// 댓글 더 보기
$('.commentMoreBtn').click(function () {
  var $this = $(this);
  var type = $this.data('type');
  var type_id = $this.data('type-id');
  var current_offset = $('.comments li').length;
  var changed_offset;

  // 더보기 영역 숨기기
  $this.closest('.commentMore').hide();
  // 로더 보여주기
  $('.patLoader').show();

  $.post('/api/comments', {type: type, type_id: type_id, offset: current_offset, limit: 3})
    .done(function (data) {
      $('.comments').prepend(data.body.comments);

      $('.patLoader').hide();
      changed_offset = current_offset + 3;
      if (changed_offset < data.body.comment_count) {
        $this.closest('.commentMore').show();
      }
    });
});

