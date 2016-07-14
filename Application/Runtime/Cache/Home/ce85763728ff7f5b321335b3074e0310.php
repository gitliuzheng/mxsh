<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="renderer" content="webkit">
    <meta name="applicable-device" content="pc">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta http-equiv="Cache-Control" content="no-transform" >
    <meta name="application-name" content="美团网－美团一次 美一次，精品生活">
    <meta name="baidu-site-verification" content="TiWCN4h5v3" />
    <link rel="alternate" href="http://suzhousz.meituan.com/feed" title="订阅更新" type="application/rss+xml">
    <link rel="apple-touch-icon" href="//s0.meituan.net/bs/file/?f=fewww:/www/img/apple-touch-icon-ipad.png@db943c9">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" href="/favicon.ico" sizes="16x16 32x32">
    <link rel="canonical" href="http://www.meituan.com/deal/buy/27625177">
    <title>美团网</title>
    <script src="/mxsh/Public/Scripts/jquery.js"></script>
    <script src="/mxsh/Public/Scripts/jquery.validate.min.js"></script>
    <script src="/mxsh/Public/Scripts/messages_zh.js"></script>
    <link rel="stylesheet" type="text/css" href="//s0.meituan.net/bs/css/?f=fewww:/www/css/common.css,/www/css/base.css@db943c9" />
    <link rel="stylesheet" type="text/css" href="//s0.meituan.net/bs/css/?f=fewww:/www/css/table-section.css,/www/css/buy.css,/www/css/deal.css,/www/css/calendar.css,/www/css/buy-process.css,/www/css/buyprocessbar.css@db943c9" />
    <script src="/mxsh/Public/JS/Home/buy_add_minus.js"></script>
    <script>


        // 手机号码验证
        jQuery.validator.addMethod("isMobile", function(value, element) {
            var length = value.length;
            var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
            return this.optional(element) || (length == 11 && mobile.test(value));
        }, "请正确填写您的手机号码");

        $().ready(function() {
            $("#dealbuy-quantity").find("input[name='quantity']").blur(function(){
                change_buy_total();
            });
            $("#dealbuy-quantity").find("input[name='quantity']").focus(function(){
                change_buy_total();
            });


            $("#deal-buy-form").validate({
                rules:{
                    buyer_phone : {
                        required : true,
                        isMobile : true
                    },
                    quantity : {
                        required : true,
                        min : 1,
                        max: <?php echo ($goods_info["virtual_limit"]); ?>
                    }
                },messages: {
                    quantity : {
                        min: "最小1",
                        max: "最多<?php echo ($goods_info["virtual_limit"]); ?>"

                    }
                }
            });
        });
    </script>

    <style>
        .error{
            color:red;
        }
    </style>

</head>
<body  id="deal-buy"   class="pg-buy pg-buy-process" >
<!-- 顶部公共部分 -->
<header id="site-mast"  class="site-mast" >
    <div class="site-mast__user-nav-w" id="J-site-mast__user-nav-w">
        <div class="site-mast__user-nav cf">
            <ul class="basic-info">
                <li class="site-mast__keep">
                    <a rel="nofollow" class="fav" id="J-favorite" gaevent="header/addFavorite" data-mod="fa" href="javascript:;">
                        <i class="F-glob F-glob-star-border icon-keep"></i>
                        收藏美团
                    </a>
                </li>
                <li class="user-info cf"  data-comboajax-uri='/combo/userinfo' data-comboajax-onsuccess='this.setHTML($response.html);' data-comboajax-state='0'>
                    <a  class="user-info__login"  id="J-login" href="https://passport.meituan.com/account/unitivelogin?service=www&amp;continue=http%3A%2F%2Fwww.meituan.com%2Faccount%2Fsettoken%3Fcontinue%3Dhttp%253A%252F%252Fwww.meituan.com%252Fdeal%252Fbuy%252F27625177" gaevent="top/login">登录</a>
                    <a class="user-info__signup" href="https://passport.meituan.com/account/unitivesignup?service=www&amp;continue=http%3A%2F%2Fwww.meituan.com%2Faccount%2Fsettoken%3Fcontinue%3Dhttp%253A%252F%252Fwww.meituan.com%252Fdeal%252Fbuy%252F27625177%253Fq%253D7%2526dealgoodsid%253D0%2526mtt%253D1.deal%25252Fdefault.0.0.iqiq4jtl" gaevent="top/signup">注册</a>
                </li>
                <li data-uix="dropdown" class="dropdown dropdown--msg"  data-comboajax-uri='/index/message' data-comboajax-onsuccess='$request.listen("www-tips", "www.MsgCenter");' data-comboajax-state='0' style="display:none;">
                    <a id="J-my-msg" rel="nofollow" class="dropdown__toggle" href="http://www.meituan.com/message/" gaevent="nav/mymsg">
                        <i class="vertical-bar vertical-bar--left"></i>
                        <span class="J-title">消息</span>
                        <i class="tri tri--dropdown"></i>
                        <i class="vertical-bar"></i>
                    </a>
                </li>
                <li data-uix="dropdown" data-params='{"classname": "dropdown--open dropdown--open-app"}' class="mobile-info__item dropdown">
                    <a class="dropdown__toggle" href="javascript:;"><i class="icon-mobile F-glob F-glob-phone"></i>手机美团<i class="tri tri--dropdown"></i></a>
                    <div class="dropdown-menu dropdown-menu--app">
                        <a class="app-block" href="http://i.meituan.com/mobile/down/meituan" target="_blank">
                            <span class="app-block__title">免费下载美团手机版</span>
                            <span class="app-block__content"></span>
                            <i class="app-block__arrow F-glob F-glob-caret-right"></i>
                        </a>
                        <a class="app-block app-block--last app-block--maoyan" href="http://www.maoyan.com/" target="_blank">
                            <span class="app-block__title">免费下载猫眼电影手机版</span>
                            <span class="app-block__content"></span>
                            <i class="app-block__arrow F-glob F-glob-caret-right"></i>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="site-mast__user-w">
                <li class="user-orders">
                    <a href="http://www.meituan.com/orders/">我的订单</a>
                </li>
                <li data-uix="dropdown" class="dropdown dropdown--account">
                    <a id="J-my-account-toggle" rel="nofollow" class="dropdown__toggle" href="http://www.meituan.com/orders/" gaevent="nav/my" data-mttcode="Amymeituan">
                        <span>我的美团</span>
                        <i class="tri tri--dropdown"></i>
                        <i class="vertical-bar"></i>
                    </a>
                    <ul id="J-my-account-menu" class="dropdown-menu dropdown-menu--text dropdown-menu--account account-menu" data-mtnode="Amymeituan">
                        <li><a class="dropdown-menu__item first " rel="nofollow" gaevent="nav/my/orders" href="http://www.meituan.com/orders/" data-mttcode="Borders">我的订单</a></li>
                        <li><a class="dropdown-menu__item  " rel="nofollow" gaevent="nav/my/feedback" href="http://www.meituan.com/rates/" data-mttcode="Brates">我的评价</a></li>
                        <li><a class="dropdown-menu__item  " rel="nofollow" gaevent="nav/my/collections" href="http://www.meituan.com/collections/" data-mttcode="Bcollections">我的收藏</a></li>
                        <li><a class="dropdown-menu__item  " rel="nofollow" gaevent="nav/my/points" href="http://www.meituan.com/account/points/" data-mttcode="Bpoints">我的积分</a></li>
                        <li><a class="dropdown-menu__item  " rel="nofollow" gaevent="nav/my/card" href="http://www.meituan.com/card/list" data-mttcode="Bvoucher">抵用券</a></li>
                        <li><a class="dropdown-menu__item  " rel="nofollow" gaevent="nav/my/credit" href="http://www.meituan.com/account/credit" data-mttcode="Bcredit">我的余额</a></li>
                        <li><a class="dropdown-menu__item  last" rel="nofollow" gaevent="nav/my/settings" href="http://www.meituan.com/account/settings" data-mttcode="Bsettings">账户设置</a></li>
                    </ul>
                </li>
                <li data-uix="dropdown" data-params='{"classname": "dropdown--open dropdown--open-history"}' class="dropdown dropdown--history"  data-comboajax-uri='/index/rvd' data-comboajax-config='www.History.nav' data-comboajax-state='0'>
                    <a id="J-my-history-toggle" rel="nofollow" class="dropdown__toggle" href="javascript:;" gaevent="nav/history">
                        <span>最近浏览</span>
                        <i class="tri tri--dropdown"></i>
                        <i class="vertical-bar"></i>
                    </a>
                    <div id="J-my-history-menu" class="dropdown-menu dropdown-menu--deal dropdown-menu--history"></div>
                </li>
                <li data-uix="dropdown" id="J-my-cart" class="dropdown dropdown--cart"  data-comboajax-uri='/index/navcart' data-comboajax-onsuccess='Y.mt.www.CartEx.update($response.data);' data-comboajax-state='0'>
                    <a id="J-my-cart-toggle" rel="nofollow" class="dropdown__toggle" href="http://www.meituan.com/cart/" gaevent="nav/cart">
                        <i class="icon icon-cart F-glob F-glob-cart"></i>
                        <span>购物车<em class="badge" data-newIndex="true"><strong class="cart-count">0</strong>件</em></span>
                        <i class="tri tri--dropdown"></i>
                        <i class="vertical-bar"></i>
                    </a>
                    <div id="J-my-cart-menu" class="dropdown-menu dropdown-menu--deal dropdown-menu--cart"></div>
                </li>
                <li data-uix="dropdown" id="J-site-help" class="dropdown dropdown--help">
                    <a class="dropdown__toggle" href="http://www.meituan.com/help/selfservice" gaevent="top/service">
                        <span>联系客服</span>
                        <i class="tri tri--dropdown"></i>
                        <i class="vertical-bar"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu--text dropdown-menu--help">
                        <ul class="site-help-info">
                            <li><a rel="nofollow" class="J-selfservice-tab dropdown-menu__item" data-tab="0" href="http://www.meituan.com/help/selfservice" gaevent="top/service/refund">申请退款</a></li>
                            <li><a rel="nofollow" class="J-selfservice-tab dropdown-menu__item" data-tab="1" href="http://www.meituan.com/help/selfservice?tab=1" gaevent="top/service/returns">申请退换货</a></li>
                            <li><a rel="nofollow" class="J-selfservice-tab dropdown-menu__item" data-tab="2" href="http://www.meituan.com/help/selfservice?tab=2" gaevent="top/service/code">查看美团券</a></li>
                            <li><a rel="nofollow" class="J-selfservice-tab dropdown-menu__item" data-tab="3" href="http://www.meituan.com/help/selfservice?tab=3" gaevent="top/service/tel">换绑手机号</a></li>
                            <li><a rel="nofollow" class="dropdown-menu__item" href="http://www.meituan.com/help/faq" gaevent="top/service/faq">常见问题</a></li>
                        </ul>
                    </div>
                </li>
                <li data-uix="dropdown" id="J-site-merchant" class="dropdown dropdown--merchant">
                    <a class="dropdown__toggle dropdown__toggle--merchant" href="javascript:;" gaevent="top/merchant">
                        <span>我是商家</span>
                        <i class="tri tri--dropdown"></i>
                        <i class="vertical-bar"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu--text dropdown-menu--merchant">
                        <ul>
                            <li><a rel="nofollow" class="dropdown-menu__item" href="http://e.meituan.com/" gaevent="top/merchant/ecom">登录商家中心</a></li>
                            <li><a rel="nofollow" class="dropdown-menu__item" href="http://pmc.e.meituan.com/public/register/choose" gaevent="top/merchant/cooperation">我想合作</a></li>
                            <li><a rel="nofollow" class="dropdown-menu__item" href="http://e.meituan.com/mobile/?fr=tuantop" target="_blank">手机免费开店</a></li>
                            <li><a rel="nofollow" class="dropdown-menu__item" href="http://zhaoshang.meituan.com" gaevent="top/merchant/mmp">商家营销平台</a></li>
                        </ul>
                    </div>
                </li>
                <li data-uix="dropdown" id="J-my-more" class="dropdown dropdown--more dropdown--last">
                    <a id="J-my-more-toggle" class="dropdown__toggle dropdown__toggle--my-more" href="javascript:;" gaevent="nav/more">
                        <span>更多</span>
                        <i class="tri tri--dropdown"></i>
                    </a>
                    <div id="J-my-more-menu" class="dropdown-menu dropdown-menu--text dropdown-menu--more">
                        <ul>
                            <li>
                                <a rel="nofollow" class="mobile dropdown-menu__item" href="http://www.meituan.com/mobile/" target="_blank" gaevent="header/mobile"><span></span>手机版</a>
                            </li>
                            <li>
                                <a rel="nofollow" id="J-subscribe" class="subscribe dropdown-menu__item" gaevent="header/subscribe" href="http://www.meituan.com/account/subscription"><span></span>邮件订阅</a>
                            </li>
                            <li class="last">
                                <a rel="nofollow" class="refer dropdown-menu__item" href="http://www.meituan.com/account/referrals" target="_blank" gaevent="header/refferals">邀请好友</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>



    <div class="site-mast__branding cf" id="J-site-mast__branding">
        <h1><a class="site-logo" href="http://suzhousz.meituan.com" gaevent="header/logo">宿州团购</a></h1>

        <div data-component="buyprocessbar" class="component-buyprocessbar" mt-scope='[]'  data-component-config='{"lazyRender":false}'><img src='data:image/png,' onerror='M._autoinit.push(this.parentNode);this.parentNode.removeChild(this);' width=1 height=1 style='position:absolute;'><div class="buy-process-bar-container">
            <ol class="buy-process-desc steps-desc">
                <li class="step current" style="width:33.333333333333%">1. 提交订单</li>
                <li class="step" style="width:33.333333333333%">2. 选择支付方式</li>
                <li class="step" style="width:33.333333333333%">3. 购买成功</li>
            </ol>
            <div class="progress">
                <div class="progress-bar" style="width:33.333333333333%"></div>
            </div>
        </div>
        </div>
        <a class="site-commitment" gaevent="top/commitment" href="http://www.meituan.com/commitment/" target="_blank">
            <span class="commitment-item"><i class="F-glob F-glob-commitment-retire"></i>随时退</span>
            <span class="commitment-item"><i class="F-glob F-glob-commitment-free"></i>不满意免单</span>
            <span class="commitment-item"><i class="F-glob F-glob-commitment-expire"></i>过期退</span>
        </a>
    </div>
</header>

<script>
    $(document).ready(function(){
        if(<?php echo ($buy_step); ?> == 2){
            $("div[class='progress-bar']").css("width","66.66%");
        }else if(<?php echo ($buy_step); ?> == 3){
            $("div[class='progress-bar']").css("width","99.99%");
        }

    });
</script>
<div data-component="system-message" class="component-system-message" mt-scope='["messages"]'  data-component-config='{"lazyRender":false,"afterLoad":false}'><img src='data:image/png,' onerror='M._autoinit.push(this.parentNode);this.parentNode.removeChild(this);' width=1 height=1 style='position:absolute;'><div class="sysmsgw common-tip" mt-bind-attr="{id: 'sysmsg-' + type}" mt-bind-show="isDisplay()" style="display:none">
    <div class="sysmsg">
        <span class="J-msg-content">
            <span
                    mt-bind-class="{
                'J-tip-status':true,  
                'tip-status': true, 
                'tip-status--success': type == 'success', 
                'tip-status--info': type == 'info',  
                'tip-status--error': type == 'error'}"></span>
            </span>
        <span mt-bind-html="message"></span>
        <span class="close common-close" mt-bind-onclick="hide()">关闭</span>
    </div>
</div>
</div>
<div id="bdw" class="bdw">
    <div id="bd" class="cf">
        <form data-component action="<?php echo U('Buy/buy_step2');?>" method="post" id="deal-buy-form" class="common-form form J-wwwtracker-form">
            <div class="table-section summary-table">

                <table cellspacing="0" class="buy-table">
                    <tr class="order-table-head-row">
                        <th class="desc">项目</th>
                        <th class="unit-price">单价</th>
                        <th class="amount">数量</th>
                        <th class="col-total">总价</th>
                    </tr>

                    <tr>
                        <td class="desc">
                            <a href="http://www.meituan.com/deal/27625177.html" target="_blank">
                                <?php echo ($goods_info["goods_name"]); ?>
                            </a>
                        </td>

                        <td class="money J-deal-buy-price">
                            ¥<span id="deal-buy-price"><?php echo ($goods_info["goods_price"]); ?></span>
                        </td>

                        <td class="deal-component-quantity">
                            <div data-component="dealbuy-quantity" class="component-dealbuy-quantity" mt-scope='["quantity","onchange=onQuantityChange"]'  data-component-params='{"dealid":27625177,"minNumPerOrder":1,"maxNumPerOrder":0,"remain":100,"totalRemain":null}' data-component-config='{"lazyRender":false}'>
                                <div class="dealbuy-quantity" id="dealbuy-quantity">
                                <button class="minus"  type="button" onclick="decrease()">-</button>
                                    <input type="text"   class="f-text J-quantity J-cart-quantity"  maxlength="4"  name="quantity"    value="<?php echo ($goods_info["quantity"]); ?>" />
                                    <button for="J-cart-add" class="item plus"  type="button" onclick="increase(<?php echo ($goods_info["virtual_limit"]); ?>)">+</button>

                                <input type="hidden" name="goods_id" value="<?php echo ($goods_info["goods_id"]); ?>" />
                            </div>
                            </div>
                        </td>
                        <td class="money total rightpadding col-total">
                            ¥<span id="J-deal-buy-total" ><?php echo ($goods_info["goods_total"]); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" class="extra-fee total-fee rightpadding">
                            <strong>应付金额</strong>：
                                <span class="inline-block money">
                                    ¥<strong id="deal-buy-total-t" ><?php echo ($goods_info["goods_total"]); ?></strong>
                                </span>
                        </td>
                    </tr>
                </table>
            </div>
            <input id="J-deal-buy-cardcode" type="hidden" name="cardcode" maxlength="8" value="" />
            <div mt-bind-show="orderTotal >= 500" style="display:none">
                <div data-component="bigdeal-prompt" class="component-bigdeal-prompt" mt-scope='[]'  data-component-config='{"lazyRender":false,"afterLoad":false}'><img src='data:image/png,' onerror='M._autoinit.push(this.parentNode);this.parentNode.removeChild(this);' width=1 height=1 style='position:absolute;'><div class="blk-item big-deal">
                    <h3>大额单购买提示</h3>
                    <p class="text tip">* 本单总价超过500元，已超出工行口令卡、招行大众版等的单次支付限额。查看<a href="http://help.alipay.com/lab/help_detail.htm?help_id=211661" target="_blank">更多银行支付限额详情</a></p>
                    <p class="text tip">* 您也可以先<a  href="http://www.meituan.com/account/charge" gaevent="buy/charge" target="_blank">为美团账户充值，</a>方便您的购买</p>
                </div>
                </div>
            </div>
            <div data-component="dealbuy-mobile" class="component-dealbuy-mobile" mt-scope='[]'  data-component-config='{"lazyRender":false}'><img src='data:image/png,' onerror='M._autoinit.push(this.parentNode);this.parentNode.removeChild(this);' width=1 height=1 style='position:absolute;'>
            <div class="J-common-normal-mobile" style="margin:10px 0;font-size:12px;">
                <p>
                    将发送美团券密码至手机号：
                    <span class="mobile">
                        <input type="text" name="buyer_phone" value="<?php echo ($member_info["member_mobile"]); ?>" />
                    </span>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    买家留言：
                    <span class="mobile">
                        <input style="width:250px;" type="text" name="buyer_msg" value="" />
                    </span>
                </p>
            </div>
            </div>
            <div class="form-submit">
                <input type="submit" class="btn btn-large btn-buy" name="submit" value="提交订单"   />
            </div>
        </form>





    </div>
</div>



<footer class="site-info-w J-br" style="min-height:298px;" id="yui_3_16_0_1_1465960232707_803">

    <div class="site-outer">
        <div class="site-info">
            <div class="site-info__item">
                <h3>获取更新</h3>
                <ul>
                    <li><a rel="nofollow" href="http://www.meituan.com/maillist/subscribe">邮件订阅</a></li>
                    <li><a href="http://www.meituan.com/mobile/">iPhone/Android</a></li>
                    <li><a rel="nofollow" href="http://user.qzone.qq.com/97231705" target="_blank">美团QQ空间</a></li>
                    <li><a rel="nofollow" href="http://t.sina.com.cn/meituan" target="_blank">美团新浪微博</a></li>
                    <li><a href="http://www.meituan.com/sitemap/citysitemap.php" target="_blank">sitemap</a></li>
                    <li><a rel="nofollow" href="http://suzhousz.meituan.com/feed/suzhousz" target="_blank">RSS订阅</a></li>
                </ul>
            </div>
            <div class="site-info__item">
                <h3>商务合作</h3>
                <ul>
                    <li><a rel="nofollow" href="http://pmc.e.meituan.com/public/register/choose" gaevent="footer/seller">商家合作</a></li>
                    <li><a rel="nofollow" href="http://zhaoshang.meituan.com/" gaevent="footer/mmp">美团商家营销平台</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/about#contact">市场合作</a></li>
                    <li><a rel="nofollow" href="http://union.meituan.com/" target="_blank">美团联盟</a></li>
                    <li><a rel="nofollow" target="_blank" href="http://mos.meituan.com/">美团云</a></li>
                    <li><a rel="nofollow" target="_blank" href="mailto:lianzheng@meituan.com">廉正邮箱</a></li>
                </ul>
            </div>
            <div class="site-info__item">
                <h3>公司信息</h3>
                <ul>
                    <li><a rel="nofollow" href="http://www.meituan.com/about/">关于美团</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/commitment/">美团承诺</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/about/press">媒体报道</a></li>
                    <li><a rel="nofollow" href="http://zhaopin.meituan.com/" gaevent="footer/job">加入我们</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/about/law">法律声明</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/about/terms">用户协议</a></li>
                </ul>
            </div>
            <div class="site-info__item">
                <h3>用户帮助</h3>
                <ul>
                    <li><a rel="nofollow" class="J-selfservice-tab" data-tab="0" href="http://www.meituan.com/help/selfservice">申请退款</a></li>
                    <li><a rel="nofollow" class="J-selfservice-tab" data-tab="2" href="http://www.meituan.com/help/selfservice?tab=2">查看美团券密码</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/help/faq">常见问题</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/help/api">开放API</a></li>
                    <li><a rel="nofollow" href="http://www.meituan.com/about/anticheat">反诈骗公告</a></li>
                </ul>
            </div>
            <div class="site-info__item site-info__item--service">
                <div class="site-info-service-content">
                    <i class="hotline"></i>
                    <p class="contact-name">客服电话</p>
                    <p class="contact-info">
                        <span class="contact-numbers">10107888</span>
                        <span class="contact-time">(9:00-23:00)</span>
                    </p>
                    <p class="desc">退款、退换货、查看美团券</p>
                    <p><a href="http://www.meituan.com/help/selfservice" class="selfservice-link" gaevent="footer/selfservice">参考教程，轻松搞定!</a></p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <!-- 版权信息 -->
    <div class="copyright">
        <p>©<span title="I:2; Q:2; S:2; C:16; F:3; T:367.01; H:com">2016</span><a href="http://www.mxhhw.com/" target="_blank">梦想换货网</a> mxhhw.com <a href="http://www.beianbeian.com/beianxinxi/5e40326e-16f0-4c22-b329-b40104fd8e4e.html" target="_blank">皖ICP备14012689号-2</a>
        </p>
        <div style="width:300px;margin:0 auto; padding:20px 0;">
            <a target="_blank" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=11010502025545" style="display:inline-block;text-decoration:none;height:20px;line-height:20px;"><img src="/Public/Images/wangan.png" style="float:left;"><p style="float:left;height:20px;line-height:20px;margin: 0px 0px 0px 5px; color:#939393;">京公网安备11010502025545号</p></a>
        </div>
    </div>
    <ul class="cert cf">
        <li class="cert__item"><a class="sp-ft sp-ft--record" title="备案信息" href="http://www.meituan.com/about/openinfo" hidefocus="true" target="_blank">备案信息</a></li>
        <li class="cert__item"><a class="sp-ft sp-ft--knet" href="http://t.knet.cn/index_new.jsp" target="_blank" title="可信网站认证" rel="nofollow">可信网站</a></li>
        <li class="cert__item"><a class="sp-ft sp-ft--12315" href="http://www.bj315.org/xfwq/lstd/201209/t20120910_3344.shtml?dnrpluojqxbceiqq" target="_blank" title="12315消费争议" rel="nofollow">12315消费争议</a></li>
    </ul>
</footer>

</body>
</html>