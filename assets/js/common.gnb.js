$(function () {
  // 스크롤 시 GNB 스타일 변경을 위한 이벤트
  $(window).scroll(checkIfScrolled);

  // 이미 스크롤된 상태인지 로드할 때 한번 확인
  checkIfScrolled();

  function checkIfScrolled() {
    var scrollOffsetTop = $(window).scrollTop();

    if (scrollOffsetTop > 0) {
      $('body').addClass('scroll');
    } else {
      $('body').removeClass('scroll');
    }
  }

  // 검색 버튼을 클릭한 경우
  $('.searchBtn').click(function () {
    // 영역 버그
    $(window).scrollTop(0);
    $('body').addClass('searching');
  });

  // 검색 창 포커스, 아웃 포커스
  $('#patSearch .searchInput input[type=text]').focus(function () {
    $(this).closest('.searchInput').addClass('focusing');
  })
    .focusout(function () {
      $(this).closest('.searchInput').removeClass('focusing');
    });
});
