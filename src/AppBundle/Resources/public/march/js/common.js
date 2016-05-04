//找到url中匹配的字符串
function findInUrl(str) {
    var url = location.href;
    return url.indexOf(str) == -1 ? false : true;
}
//获取url参数
function queryString(key) {
    return (document.location.search.match(new RegExp("(?:^\\?|&)" + key + "=(.*?)(?=&|$)")) || ['', null])[1];
}

//产生指定范围的随机数
function randomNumb(minNumb, maxNumb) {
    var rn = Math.round(Math.random() * (maxNumb - minNumb) + minNumb);
    return rn;
}

var wHeight;
var tempHeight;
$(document).ready(function () {
    wHeight = $(window).height();
    $('.pageOuter').height(wHeight);
    $('.page').height(wHeight);
    $('.h1008').css('padding-top', (wHeight - 1008) / 2 + 'px');
    if (wHeight < 1008) {
        var bli = wHeight / 1008;
        $('.page').height(1008);
        $('.h1008').css('padding-top', '0px');
        $('.page').css('-webkit-transform', 'scale(' + bli + ')');
        $('.page').css('-webkit-transform-origin', '50% ' + (1008 - wHeight) / 2 + 'px');
        $('.page4Img4').css('bottom', '-' + (1008 - wHeight) / 3 * 2 + 'px');

        $('.page').on('touchmove', function (e) {
            e.preventDefault();
        });
    }
});

var lh = 380;
var session_id = '';
function loadingImg() {
    var images = [];
    images.push("bundles/app/march/images/bg.png");

    images.push("bundles/app/march/images/page1Img1.png");
    images.push("bundles/app/march/images/page1Img2.png");

    images.push("bundles/app/march/images/page2Img1.png");
    images.push("bundles/app/march/images/page2Img2.png");

    images.push("bundles/app/march/images/page3Img1.png");

    images.push("bundles/app/march/images/page4Img1.png");
    images.push("bundles/app/march/images/page4Award1.png");
    images.push("bundles/app/march/images/page4Award2.png");
    images.push("bundles/app/march/images/page4Award3.png");
    images.push("bundles/app/march/images/page4Award4.png");
    images.push("bundles/app/march/images/page4Award5.png");
    images.push("bundles/app/march/images/page4Award6.png");
    images.push("bundles/app/march/images/page4Award7.png");
    images.push("bundles/app/march/images/page4Award8.png");

    images.push("bundles/app/march/images/page5Img1.png");
    images.push("bundles/app/march/images/page5Img2.png");

    images.push("bundles/app/march/images/page6Img1.png");

    images.push("bundles/app/march/images/rule1.png");
    images.push("bundles/app/march/images/rule2.png");
    images.push("bundles/app/march/images/rule3.png");
    images.push("bundles/app/march/images/rule4.png");

    images.push("bundles/app/march/images/topImg1.png");


    /*图片预加载*/
    var imgNum = 0;
    $.imgpreload(images,
        {
            each: function () {
                var status = $(this).data('loaded') ? 'success' : 'error';
                if (status == "success") {
                    var v = (parseFloat(++imgNum) / images.length).toFixed(2);
                    //$("#percentShow").html('已加载:'+Math.round(v * 100) + "%");
                    $('.loadingProgress').stop().animate({height: v * lh}, 100, 'linear');
                }
            },
            all: function () {

                //$("#percentShow").html("已加载:100%");
                //图片加载完成 加载动画
                setTimeout(function () {
                    goPage1();
                }, 500);
            }
        });
}

function goPage1() {
    $('.topBtn1').show();
    $('.page0').hide();
    $('.topBar').fadeIn(500);
    $('.page1').fadeIn(500);
    $('body').css('background', 'url(bundles/app/march/images/bg.png) top center no-repeat');
    $('input').val('');
}


function goPage2(url) {
    var iCode = $.trim($('.page1Txt').val());
    if (iCode == '') {
        alert('请输入你的幸运心愿码');
        return false;
    }
    else {
        $.support.cors = true;
        $.ajax({
            url:url,
            data:{code:iCode},
            method:'post',
            dataType:'json',
            xhrFields:{withCredentials: true},
            success:function (json) {
                if (json.ret == 0){
					$('.topBtn1').fadeOut(500);
					$('.page1').fadeOut(500);
					$('.page2').fadeIn(500);
					ga('send','pageview','/luckydraw_request');
					setTimeout(function () {
						$('.page2Img2').addClass('page2Img2Act');
					}, 1000);

                    session_id = json.session_id;
                    if (json.prize == 0) {
                        setTimeout(function () {
                            goPage6();
							ga('send','pageview','/winner_fail');
                        }, 5500);
                    }
                    else {
                        $('.page4Res').addClass('page4Res' + json.prize);
                        setTimeout(function () {
                            goPage4();
							ga('send','pageview','/winner_'+json.prize);
                        }, 5500);
                    }
                }
                else{
                    alert(json.msg);
                }
            },
            error:function () {
                alert('亲，你输入的幸运心愿码有误哦，请仔细检查重新输入')
                /*
                setTimeout(function () {
                    goPage3();
                }, 7000);
                */
            }
        });

        //var lotteryNumb = 0;//0未中奖 1见郭碧婷 2平板电脑 3空气净化器 4运动手表 5金项链 6格瓦拉 7笔记本 8洗漱包
        //提交ajax
        //提交成功
        //lotteryNumb = randomNumb(0, 8);//赋值到奖品号 静态页面随机0-8
    }
}


function goPage3() {
    $('.page2').hide();
    $('.page3').fadeIn(1000);
}

function goPage4() {
    $('.page2').hide();
    $('.page4').fadeIn(1000);
}

function submitInfo(url) {
    var iName = $.trim($('.page4Txt1').val());
    var iTel = $.trim($('.page4Txt2').val());
    var iAddress = $.trim($('.page4Txt3').val());
    var pattern = /^1[3456789]\d{9}$/;
    if (iName == '') {
        alert('请输入姓名');
        return false;
    }
    else if (iTel == '' || !pattern.test(iTel)) {
        alert('请输入正确的手机号码');
        return false;
    }
    else if (iAddress == '') {
        alert('请输入地址');
        return false;
    }
    else {
        //ajax提交信息
        $.support.cors = true;
        $.ajax({
            url:url,
            data:{username:iName,mobile:iTel,address:iAddress,session_id:session_id},
            method:'post',
            dataType:'json',
            xhrFields:{withCredentials: true},
            success: function (json) {
                if (json.ret == 0){
                    goPage5();
					ga('send','event','button','click','redirect_to_makewish');
                }
                else{
                    alert(json.msg);
                }
            },
            error:function () {
                alert('提交失败,请稍候重试~');
            }
        })
        //ajax成功
    }
}

function goPage5() {
    $('.page4').fadeOut(500);
    $('.page5').fadeIn(500);
}

function goPage6() {
    $('.page2').fadeOut(500);
	$('.page3').fadeOut(500);
    $('.page6').fadeIn(500);
}

function closePop() {
    $('.pop').fadeOut(500);
    $('.popBg').fadeOut(500);
}

function showRule() {
    $('.ruleBg').fadeIn(500);
    $('.popRule').fadeIn(500);
    setTimeout(function () {
        var swiper = new Swiper('.swiper-container', {
            direction: 'vertical'
        });
    }, 550);
}

function closeRule() {
    $('.ruleBg').fadeOut(500);
    $('.popRule').fadeOut(500);
}

