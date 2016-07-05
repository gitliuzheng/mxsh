/*
* 商品详情页特效
* create by liuzhen on 2016-06-16 8:43
*/
$(function(){
	var deal_offset = $('#J-content-navbar').offset().top;

	var deal_offset_1 = $('#business-info').offset().top - 41;
	var deal_offset_2 = $('#anchor-purchaseinfo').offset().top -41;
	var deal_offset_3 = $('#anchor-detail').offset().top - 41;
	var deal_offset_4 = $('#anchor-bizinfo').offset().top - 41;
	var deal_offset_5 = $('#anchor-comment').offset().top - 61;

	$(window).scroll(function(){
		var ling = $(document).scrollTop();

		//商品详情页导航悬浮效果
		if (ling > deal_offset){
		    $('#J-content-navbar').addClass('common-fixed');
		    $('#J-nav-buy').css('display','block');
		    $('.common-fix-placeholder').css('display','block');
		}
		if (ling < deal_offset_2){
			$('#J-content-navbar ul li').removeClass('content-navbar__item--current');
			$('#J-content-navbar ul li').eq(0).addClass('content-navbar__item--current');
		}else if (ling < deal_offset_3){
			$('#J-content-navbar ul li').removeClass('content-navbar__item--current');
			$('#J-content-navbar ul li').eq(1).addClass('content-navbar__item--current');
		}else if (ling < deal_offset_4){
			$('#J-content-navbar ul li').removeClass('content-navbar__item--current');
			$('#J-content-navbar ul li').eq(2).addClass('content-navbar__item--current');
		}else if (ling < deal_offset_5){
			$('#J-content-navbar ul li').removeClass('content-navbar__item--current');
			$('#J-content-navbar ul li').eq(3).addClass('content-navbar__item--current');
		}else if (ling >= deal_offset_5){
			$('#J-content-navbar ul li').removeClass('content-navbar__item--current');
			$('#J-content-navbar ul li').eq(4).addClass('content-navbar__item--current');
		}

		if (ling < deal_offset){
		    $('#J-content-navbar').removeClass('common-fixed');
		    $('#J-nav-buy').css('display','none');           
		    $('.common-fix-placeholder').css('display','none');
		}		
	});

	//导航菜单点击事件
	$('#J-content-navbar ul li').each(function(i){
		$(this).click(function(){
			if (i == 0){                    
                $('html,body').animate({scrollTop: deal_offset_1 + 1}, 500);                   
            }else if (i == 1){
                $('html,body').animate({scrollTop: deal_offset_2 + 1}, 500);
            }else if (i == 2){
                $('html,body').animate({scrollTop: deal_offset_3 + 1}, 500);
            }else if (i == 3){
                $('html,body').animate({scrollTop: deal_offset_4 + 1}, 500);
            }else if (i == 4){
                $('html,body').animate({scrollTop: deal_offset_5 + 1}, 500);
            }

            return false;
		});
	});

    //数量+-
    $("button[for='J-cart-add']").click(function(){
        var J_cart_quantity = $("input[class='J-cart-quantity']").val();
        J_cart_quantity = parseInt(J_cart_quantity) + 1;
        $("input[class='J-cart-quantity']").val(J_cart_quantity);
        J_cart_add_and_minus_goods_num(J_cart_quantity);
    });

    $("button[for='J-cart-minus']").click(function(){
        var J_cart_minus = $("input[class='J-cart-quantity']").val();
        J_cart_minus = parseInt(J_cart_minus) - 1;
        if(J_cart_minus < 1){
            J_cart_minus = 1;
        }
        $("input[class='J-cart-quantity']").val(J_cart_minus);
        J_cart_add_and_minus_goods_num(J_cart_minus);
    });

    function J_cart_add_and_minus_goods_num($quantity){
        $("#deal_hidden").find(".goods_num").text($quantity);
    }



});