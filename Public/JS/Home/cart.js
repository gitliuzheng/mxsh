
//添加到购物车
function addCart(url){

    var goods_id = $("#goods_data").find(".goods_id").text();
    var url = url;
    var cookie_cart = "";
    $.ajax({
        type: "GET",
        url: url,
        data: {goods_id : goods_id},
        dataType: "json",
        success: function(data){
            changeCart(data);
        }
    });


}

//修改购物车显示
function changeCart(data){
    //修改顶部购物车件数
    var count = data.length;
    $(".cart-count").html(count);

    //
    var html = "<ul class='list-wrapper' >";
    for(var i = 0; i < data.length; i++){
        html += '<li class="dropdown-menu__item">';
        html += '<a href="' + data[i]['url'] + '" title="' + data[i]['goods_name'] + '" target="_blank" rel="nofollow" ';
        html += 'class="deal-link" gaevent="nav/cart/1/photo">    <img class="deal-cover" src="http://p0.meituan.net/80';
        html+='.50/deal/959dba837e9a0fb672d35a82b3845ffe31221.jpg@0_21_702_425a%7C388h_640w_2e_100Q" width="80" height="50">';
        html += '</a>';
        html += '<h5 class="deal-title"><a href="' + data[i]['url'] + '" title="' + data[i]['goods_name'] + '" ';
        html += 'target="_blank" rel="nofollow" class="deal-link" gaevent="nav/cart/1/title">' + data[i]['goods_name'] + '</a></h5>';
        html+='       <p class="deal-price-w"><a href="javascript:void(0);" data-id="37687495" data-calendarid="0" ';
        html += 'data-dealgoodsid="0" class="delete link--black__green" onclick="delCart(' + i + ');">删除</a><em class="deal-price">¥' + data[i]['goods_price'] + '</em>';
        html += '</p>';
        html += '</li>';
    }
    html += "</ul>";

    //$("#J-my-cart-menu").find(".list-wrapper").html(html);
    $("#J-my-cart-menu").html(html);

}


//删除购物车某项商品
function delCart(i){
    var url = $("#deal_hidden").find(".delCart_url").text();
    $.ajax({
        type: "GET",
        url: url,
        data: {cookie_cart_index : i},
        dataType: "json",
        success: function(data){
            changeCart(data);
        }
    });
}






