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