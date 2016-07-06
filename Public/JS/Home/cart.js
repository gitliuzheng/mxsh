
//添加到购物车
function addCart(){
    var goods_id = $("#deal_hidden").find(".goods_id").text();
    var url = $("#deal_hidden").find(".addCart_url").text();
    var goods_num = $("#deal_hidden").find(".goods_num").text();
    var goods_name = $("#deal_hidden").find(".goods_name").text();
    var goods_price = $("#deal_hidden").find(".goods_price").text();

    $.ajax({
        type: "GET",
        url: url,
        data: {goods_id : goods_id , goods_num : goods_num ,goods_name : goods_name, goods_price : goods_price},
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
function delCart(del_type,i){
    var url = $("#deal_hidden").find(".delCart_url").text();
    $.ajax({
        type: "GET",
        url: url,
        data: {del_type : del_type,cart_index : i},
        dataType: "json",
        success: function(data){
            window.location.href="";
            //changeCart(data);
        }
    });
}




$(document).ready(function(){
    //数量+-
    $("button[for='J-cart-add']").click(function(){

        var goods_num = $(this).parent().find("input[goods_num='goods_num']").val();
        goods_num = parseInt(goods_num) + 1;

        var goods_price = $(this).parents("tr").find("td[class='price']").find("span[class='J-price']").html();
        var goods_total = goods_price * goods_num;

        J_cart_add_and_minus_goods_num($(this),goods_num,goods_total);
    });

    $("button[for='J-cart-minus']").click(function(){
        var goods_num = $(this).parent().find("input[goods_num='goods_num']").val();
        goods_num = parseInt(goods_num) - 1;
        if(goods_num < 1){
            goods_num = 1;
        }

        var goods_price = $(this).parents("tr").find("td[class='price']").find("span[class='J-price']").html();
        var goods_total = goods_price * goods_num;

        J_cart_add_and_minus_goods_num($(this),goods_num,goods_total);
    });

    //加减操作之后要做的事情
    function J_cart_add_and_minus_goods_num(thisthis,goods_num,goods_total,goods_id){
        //获取商品的ID
        var goods_id = $(thisthis).parents("tr").find("input[class='goods_id']").val();
        //修改数量
        $(thisthis).parent().find("input[goods_num='goods_num']").val(goods_num);
        //修改小计
        $(thisthis).parents("tr").find("span[class='J-total']").html(goods_total);

        var url = $("#cart_hidden").find(".editCart_url").text();
        $.ajax({
            type: "GET",
            url: url,
            data: {goods_num : goods_num , goods_id : goods_id},
            dataType: "json",
            success: function(data){

            }
        });
    }
});








