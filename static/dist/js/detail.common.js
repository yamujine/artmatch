$(function(){var t=$(".mainImage .image img").width();$(".mainImage").css({width:t});var e=$(".additionalImages .slider .item").length;e>6&&($(".slider").slick({infinite:!1,variableWidth:!0,slidesToShow:6,slidesToScroll:1,prevArrow:!1,nextArrow:!1}),$(".sliderPrevBtn").click(function(){return $(".slider").slick("slickPrev"),!1}),$(".sliderNextBtn").click(function(){return $(".slider").slick("slickNext"),!1})),$(".popupImage").magnificPopup({type:"image",gallery:{enabled:!0}}),$(".artworkEditBtn, .placeEditBtn").click(function(){var t=$(this),e=t.data("type"),i=t.data("id");return location.replace("/"+e+"/"+i+"/edit"),!1}),$(".artworkDeleteBtn, .placeDeleteBtn").click(function(){if(confirm("정말 삭제 하시겠습니까?")===!1)return!1;var t=$(this),e=t.data("type"),i=t.data("id");return location.replace("/"+e+"/"+i+"/delete"),!1}),$(".pickBtn").click(function(){var t=$(this);parseInt($(this).find(".number").html(),10);return $.ajax({method:"POST",url:"/api/pick",data:{type:t.data("type"),type_id:t.data("id")}}).done(function(e){"on"===e.body.result_type?t.addClass("active").find(".number").html(e.body.pick_count):"off"===e.body.result_type?t.removeClass("active").find(".number").html(e.body.pick_count):alert(e.body.message)}).fail(function(){alert("서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.")}),!1}),$(document).on("click",".artworkCommentToggleBtn, .placeCommentToggleBtn",function(){var t=$(this);return $.ajax({method:"POST",url:"/api/contents/change_comment_option",data:{type:t.data("type"),type_id:t.data("type-id")}}).done(function(t){$(".dropdown-toggle").dropdown("toggle"),0==t.body.comment_status?alert("댓글이 비허용 처리 되었습니다."):1==t.body.comment_status&&alert("댓글이 허용 처리 되었습니다."),location.reload(!0)}).fail(function(){alert("서버에 문제가 있어 정상적으로 처리되지 않았습니다. \r\n잠시 후에 다시 시도해주세요.")}),!1});var i=$(".exhibitions .item").length;i>4&&$(".exhibitions").slick({infinite:!1,variableWidth:!0,slidesToShow:4,slidesToScroll:1,prevArrow:!1,nextArrow:!1}),$(".exhibitionArea .sliderControl .prevBtn").click(function(){return $(".exhibitions").slick("slickPrev"),!1}),$(".exhibitionArea .sliderControl .nextBtn").click(function(){return $(".exhibitions").slick("slickNext"),!1})});