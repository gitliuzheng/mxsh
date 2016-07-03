$(function(){
    //返回顶部
    document.getElementById("fixbar-item-top").onclick = function(){
        //document.documentElement.scrollTop = document.body.scrollTop =0;                
        $('html,body').animate({scrollTop: 0}, 500);                
    };

    // 楼层导航居中显示       
    var floor_block_width = $('#floor_block ul').width();
    var floor_block_height = $('#floor_block ul').height();
    var screenw = document.documentElement.clientWidth || document.body.clientWidth;
    var screenh = document.documentElement.clientHeight || document.body.clientHeight;
    $('#floor_block').css('display','none').css('top', (screenh - floor_block_height)/2 + 'px');
    
    var offset_floor = new Array();
    $('.category-floor__head').each(function(i){
        offset_floor[i] = $(this).offset().top;
    });

    var offset_floor_0 = offset_floor[0] - screenh + 30;
    var offset_floor_1 = offset_floor[0] - screenh + 30 + (screenh - floor_block_height)/2 + 463;
    var offset_floor_2 = offset_floor[1] - screenh + 30 + (screenh - floor_block_height)/2 + 384;
    var offset_floor_3 = offset_floor[2] - screenh + 30 + (screenh - floor_block_height)/2 + 323;
    var offset_floor_4 = offset_floor[3] - screenh + 30 + (screenh - floor_block_height)/2 + 262;
    var offset_floor_5 = offset_floor[4] - screenh + 30 + (screenh - floor_block_height)/2 + 183;
    var offset_floor_6 = offset_floor[5] - screenh + 30 + (screenh - floor_block_height)/2 + 122;
    var offset_floor_7 = offset_floor[6] - screenh + 30 + (screenh - floor_block_height)/2 + 61;
    var offset_floor_8 = offset_floor[7] - screenh + 30 + (screenh - floor_block_height)/2;

    // 给窗口加滚动条事件    
    $(window).scroll(function(){
        // 获得窗口滚动上去的距离
        var ling = $(document).scrollTop();
        // 在标题栏显示滚动的距离
        //document.title = ling;
        // 如果滚动距离大于160的时候让滚动框出来
        if(ling>offset_floor_0){
            $('#floor_block').show();
        }
        if(offset_floor_0<ling && ling< offset_floor_2){
            // 让第一层突出显示，让其他兄弟元素默认显示
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(0).addClass('current');
        }else if(ling<offset_floor_3){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(1).addClass('current');
        }else if(ling<offset_floor_4){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(2).addClass('current');
        }else if(ling<offset_floor_5){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(3).addClass('current');
        }else if(ling<offset_floor_6){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(4).addClass('current');
        }else if(ling<offset_floor_7){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(5).addClass('current');
        }else if(ling<offset_floor_8){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(6).addClass('current');
        }else if(ling >= offset_floor_8){
            $('#floor_block ul a.link').removeClass('current');
            $('#floor_block ul a.link').eq(7).addClass('current');
        }

        if(ling<offset_floor_0){                
            $('#floor_block').hide();
        }        
    });

    //楼层导航点击事件
    $('#floor_block ul a.link').each(function(i){
        $(this).click(function(){
            if (i == 0){                    
                $('html,body').animate({scrollTop: offset_floor_1 + 1}, 500);                   
            }else if (i == 1){
                $('html,body').animate({scrollTop: offset_floor_2 + 1}, 500);
            }else if (i == 2){
                $('html,body').animate({scrollTop: offset_floor_3 + 1}, 500);
            }else if (i == 3){
                $('html,body').animate({scrollTop: offset_floor_4 + 1}, 500);
            }else if (i == 4){
                $('html,body').animate({scrollTop: offset_floor_5 + 1}, 500);
            }else if (i == 5){
                $('html,body').animate({scrollTop: offset_floor_6 + 1}, 500);
            }else if (i == 6){
                $('html,body').animate({scrollTop: offset_floor_7 + 1}, 500);
            }else if (i == 7){
                $('html,body').animate({scrollTop: offset_floor_8 + 1}, 500);
            }
        });
    });

    //首页--全部分类mouseover和mouseout事件
    $('.J-nav-item').each(function(i){
        $(this).mouseover(function(){
            $(this).find('.J-nav-level2').show();

            if ($('.J-nav__trigger').hasClass('nav-unindex')){
                $('.J-nav__trigger').addClass('nav-unfolded');
                $('.J-nav__list').css('display','block');
            }
        }).mouseout(function(){
            $(this).find('.J-nav-level2').hide();

            if ($('.J-nav__trigger').hasClass('nav-unindex')){
                $('.J-nav__trigger').removeClass('nav-unfolded');
                $('.J-nav__list').css('display','none');
            }
        });
    });

    //搜索框mouseover
    $('.search-box__tab').mouseover(function(){
        $('.search-box__tabs-container').addClass('search-box__tabs-container--over');
    }).mouseout(function(){
        $('.search-box__tabs-container').removeClass('search-box__tabs-container--over');
    }).click(function(){
        if ($(this).hasClass('search-box__tab--current')){
            //                
        }else{
            $(this).prev().removeClass('search-box__tab--current');
            $(this).addClass('search-box__tab--current');
            $('.J-search-box__tabs').prepend(this);
            $('.search-box__tabs-container').removeClass('search-box__tabs-container--over');

            if ($(this).hasClass('J-search-box__tab--deal')){
                $('.J-search-box__input').attr('placeholder','请输入商品名称、地址等');
            }else if ($(this).hasClass('J-search-box__tab--shops')){
                $('.J-search-box__input').attr('placeholder','请输入商家名称、地址等');
            }
        }
    });

    //非首页--全部分类mouseover和mouseout事件
    $('.J-nav__trigger').mouseover(function(){
        if ($(this).hasClass('nav-unindex')){
            $(this).addClass('nav-unfolded');
            $('.J-nav__list').css('display','block');    
        }        
    }).mouseout(function(){
        if ($(this).hasClass('nav-unindex')){
            $(this).removeClass('nav-unfolded');
            $('.J-nav__list').css('display','none');
        }
    });

    //商户APP二维码下载
    $('#mxsh_app_download').mouseover(function(){
        $(this).addClass('dropdown--open dropdown--open-app');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open dropdown--open-app');
    });

    //我的商城
    $('#mxsh_my_home').mouseover(function(){
        $(this).addClass('dropdown--open');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open');
    });

    //最近浏览
    $('#mxsh_scan_history').mouseover(function(){
        $(this).addClass('dropdown--open dropdown--open-history');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open dropdown--open-history');
    });

    //购物车
    $('#J-my-cart').mouseover(function(){
        $(this).addClass('dropdown--open');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open');
    });

    //联系客服
    $('#J-site-help').mouseover(function(){
        $(this).addClass('dropdown--open');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open');
    });

    //我是商家
    $('#J-site-merchant').mouseover(function(){
        $(this).addClass('dropdown--open');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open');
    });

    //更多
    $('#J-my-more').mouseover(function(){
        $(this).addClass('dropdown--open');
    }).mouseout(function(){
        $(this).removeClass('dropdown--open');
    });
});

/*
* 收藏网站
*/
function addFavorite2() {
    //alert('hahahahahah');
    var url = window.location;
    var title = document.title;
    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf("360se") > -1) {
        alert("由于360浏览器功能限制，请使用按键 Ctrl+D 手动收藏！");
    }
    else if (ua.indexOf("msie 8") > -1) {
        window.external.AddToFavoritesBar(url, title); //IE8
    }
    else if (document.all) {
  try{
   window.external.addFavorite(url, title);
  }catch(e){
   alert('抱歉，您的浏览器不支持此操作,请使用按键 Ctrl+D 手动收藏!');
  }
    }
    else if (window.sidebar) {
        window.sidebar.addPanel(title, url, "");
    }
    else {
  alert('抱歉，您的浏览器不支持此操作,请使用按键 Ctrl+D 手动收藏!');
    }
}

//定义加入收藏夹函数
function join_favorite(siteUrl, siteName){  
 //捕获加入收藏过程中的异常       
try        {       
//判断浏览器是否支持document.all        
if(document.all){                     
//如果支持则用external方式加入收藏夹              
  window.external.addFavorite(siteUrl,siteName);                
 }else if(window.sidebar){                      
  //如果支持window.sidebar，则用下列方式加入收藏夹  
                  window.sidebar.addPanel(siteName, siteUrl,'');         
       }          
     }  
 //处理异常       
catch (e)        {          
 alert("抱歉，您的浏览器不支持此操作,请使用按键 Ctrl+D 手动收藏!");   
    }
}