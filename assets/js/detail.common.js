$(function () {
  // 메인 모든 사이즈의 이미지 형태에 대응하기 위함
  var mainImageWidth = $('.mainImage .image img').width();
  $('.mainImage').css({width: mainImageWidth});

  // 추가 이미지 Carousel 구현은 6개가 넘어갈 경우
  var additionalImageCount = $('.additionalImages .slider .item').length;
  if (additionalImageCount > 6) {
    // Carousel 시작
    $('.slider').slick({
      infinite: false,
      variableWidth: true,

      slidesToShow: 6,
      slidesToScroll: 1,

      prevArrow: false,
      nextArrow: false
    });

    // 슬라이더 컨트롤
    $('.sliderPrevBtn').click(function () {
      $('.slider').slick('slickPrev');

      return false;
    });

    $('.sliderNextBtn').click(function () {
      $('.slider').slick('slickNext');

      return false;
    });
  }

  // 이미지 전체 보기
  $('.popupImage').magnificPopup({
    type: 'image',
    gallery: {
      enabled: true
    }
  });

  // 수정 버튼
  $('.artworkEditBtn, .placeEditBtn').click(function () {
    var $this = $(this);
    var type = $this.data('type');
    var typeId = $this.data('id');

    location.replace('/' + type + '/' + typeId + '/edit');
    return false;
  });

  // 삭제 버튼
  $('.artworkDeleteBtn, .placeDeleteBtn').click(function () {
    if (confirm('정말 삭제 하시겠습니까?') === false) {
      return false;
    }
    var $this = $(this);
    var type = $this.data('type');
    var typeId = $this.data('id');

    location.replace('/' + type + '/' + typeId + '/delete');
    return false;
  });

  // PICK
  $('.pickBtn').click(function () {
    var $this = $(this);
    var number = parseInt($(this).find('.number').html(), 10);

    $.ajax({
      method: 'POST',
      url: '/api/pick',
      data: {
        type: $this.data('type'),
        type_id: $this.data('id')
      }
    })
      .done(function (data) {
        if (data.body.result_type === 'on') {
          $this.addClass('active').find('.number').html(data.body.pick_count);
        } else if (data.body.result_type === 'off') {
          $this.removeClass('active').find('.number').html(data.body.pick_count);
        } else {
          alert(data.body.message);
        }
      })
      .fail(function () {
        alert('서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.');
      });
    return false;
  });

  // 댓글 허용/비허용
  $(document).on('click', '.artworkCommentToggleBtn, .placeCommentToggleBtn', function () {
    var $this = $(this);
    $.ajax({
      method: "POST",
      url: "/api/contents/change_comment_option",
      data: {
        type: $this.data('type'),
        type_id: $this.data('type-id')
      }
    })
      .done(function (data) {
        $('.dropdown-toggle').dropdown('toggle');
        if (data.body.comment_status == 0) {
          alert('댓글이 비허용 처리 되었습니다.');
        } else if (data.body.comment_status == 1) {
          alert('댓글이 허용 처리 되었습니다.');
        }
        location.reload(true);
      })
      .fail(function () {
        alert('서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.');
      });
    return false;
  });

  // 전시 이력 Carousel 구현은 4개가 넘는 경우
  var exhibitionCount = $('.exhibitions .item').length;
  if (exhibitionCount > 4) {
    $('.exhibitions').slick({
      infinite: false,
      variableWidth: true,

      slidesToShow: 4,
      slidesToScroll: 1,

      prevArrow: false,
      nextArrow: false
    });
  }

  // 슬라이더 컨트롤
  $('.exhibitionArea .sliderControl .prevBtn').click(function () {
    $('.exhibitions').slick('slickPrev');

    return false;
  });

  $('.exhibitionArea .sliderControl .nextBtn').click(function () {
    $('.exhibitions').slick('slickNext');

    return false;
  });
});
