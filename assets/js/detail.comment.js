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
