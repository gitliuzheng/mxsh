

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
        //获取cart_id
        var cart_id = $(thisthis).parents("tr").find("input[class='cart_id']").val();

        var url = $("#cart_hidden").find(".editCart_url").text();
        $.ajax({
            type: "GET",
            url: url,
            data: {goods_num : goods_num , goods_id : goods_id ,goods_total : goods_total, cart_id : cart_id},
            dataType: "json",
            success: function(data){

            }
        });
    }
});