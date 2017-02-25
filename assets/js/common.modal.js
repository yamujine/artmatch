'use strict';

$(function () {
  // 백그라운드 클릭
  $(document).on('click', '.modal-backdrop', function () {
    $('.patModal').modal('hide');
  });
});
