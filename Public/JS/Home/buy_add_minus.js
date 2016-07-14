function decrease(){
    var goods_num = $("#dealbuy-quantity").find("input[name='quantity']").val();
    goods_num = parseInt(goods_num) - 1;
    if(goods_num < 1){
        goods_num = 1;
    }

    $("#dealbuy-quantity").find("input[name='quantity']").val(goods_num);

    change_buy_total();
}


function increase(max_goods_num){
    var goods_num = $("#dealbuy-quantity").find("input[name='quantity']").val();
    goods_num = parseInt(goods_num) + 1;
    if(goods_num > max_goods_num){
        goods_num = max_goods_num;
        alert("最多购买" + max_goods_num + "个");
    }

    $("#dealbuy-quantity").find("input[name='quantity']").val(goods_num);
    change_buy_total();
}

function change_buy_total(){
    var goods_num = $("#dealbuy-quantity").find("input[name='quantity']").val();
    var goods_price = $("#deal-buy-price").html();

    html = goods_num * goods_price;
    $("#J-deal-buy-total").html(html);
}
/*
$(document).ready(function(){
    //商品数量+-
    $("button[for='J-cart-add']").click(function(){
        var goods_num = $(this).parent().find("input[goods_num='goods_num']").val();
        goods_num = parseInt(goods_num) + 1;
        J_cart_add_and_minus_goods_num($(this),goods_num);
    });

    $("button[for='J-cart-minus']").click(function(){
        var goods_num = $(this).parent().find("input[goods_num='goods_num']").val();
        goods_num = parseInt(goods_num) - 1;
        if(goods_num < 1){
            goods_num = 1;
        }
        J_cart_add_and_minus_goods_num($(this),goods_num);
    });
});

//加减操作之后要做的事情
function J_cart_add_and_minus_goods_num(thisthis,goods_num){
    //修改数量
    $(thisthis).parent().find("input[goods_num='goods_num']").val(goods_num);
    //
    $("#deal_hidden").find(".goods_num").text(goods_num);
}

    */