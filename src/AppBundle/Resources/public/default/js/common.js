//找到url中匹配的字符串
function findInUrl(str){
	var url = location.href;
	return url.indexOf(str) == -1 ? false : true;
}
//获取url参数
function queryString(key){
    return (document.location.search.match(new RegExp("(?:^\\?|&)"+key+"=(.*?)(?=&|$)"))||['',null])[1];
}

//产生指定范围的随机数
function randomNumb(minNumb,maxNumb){
	var rn=Math.round(Math.random()*(maxNumb-minNumb)+minNumb);
	return rn;
	}
	
var wHeight;
var tempHeight;
$(document).ready(function(){
	wHeight=$(window).height();
	$('.pageOuter').height(wHeight);
	$('.page').height(wHeight);
	$('.h1008').css('padding-top',(wHeight-1008)/2+'px');
	if(wHeight<1008){
		var bli=wHeight/1008;
		$('.page').height(1008);
		$('.h1008').css('padding-top','0px');
		$('.page').css('-webkit-transform','scale('+bli+')');
		$('.page').css('-webkit-transform-origin','50% 0');
		}
	});
	
var lh=380;
function loadingImg(){
	var images=[];
    images.push("/bundles/app/default/images/bg.png");
	
	images.push("/bundles/app/default/images/page1Img1.png");
	images.push("/bundles/app/default/images/page1Img2.png");
	images.push("/bundles/app/default/images/page1Img5.png");
	images.push("/bundles/app/default/images/page1Img6.png");
	images.push("/bundles/app/default/images/page1Img7.png");
	
	images.push("/bundles/app/default/images/page2Img1.png");
	images.push("/bundles/app/default/images/page2Img3.png");
	
	images.push("/bundles/app/default/images/page3Img1.png");
	images.push("/bundles/app/default/images/page3Img3.png");
	
	images.push("/bundles/app/default/images/page4Img1.png");
	images.push("/bundles/app/default/images/page4Img2.png");
	
	images.push("/bundles/app/default/images/page5Award0.png");
	images.push("/bundles/app/default/images/page5Award1.png");
	images.push("/bundles/app/default/images/page5Award2.png");
	images.push("/bundles/app/default/images/page5Award3.png");
	images.push("/bundles/app/default/images/page5Award4.png");
	images.push("/bundles/app/default/images/page5Award5.png");
	images.push("/bundles/app/default/images/page5Award6.png");
	images.push("/bundles/app/default/images/page5Img2.png");
	images.push("/bundles/app/default/images/page5Img3.png");
	images.push("/bundles/app/default/images/page5Img4.png");
	
	images.push("/bundles/app/default/images/rule1.png");
	images.push("/bundles/app/default/images/rule2.png");
	images.push("/bundles/app/default/images/rule3.png");
	
	images.push("/bundles/app/default/images/share1.png");
	images.push("/bundles/app/default/images/share2.png");
	
	images.push("/bundles/app/default/images/topImg1.png");
	
	
    /*图片预加载*/
    var imgNum=0;
    $.imgpreload(images,
            {
                each: function () {
                    var status = $(this).data('loaded') ? 'success' : 'error';
                    if (status == "success") {
                        var v = (parseFloat(++imgNum) / images.length).toFixed(2);
                        //$("#percentShow").html('已加载:'+Math.round(v * 100) + "%");
						$('.loadingProgress').stop().animate({height:v*lh},100,'linear');
                    }
                },
                all: function () {

                    //$("#percentShow").html("已加载:100%");
                    //图片加载完成 加载动画
					setTimeout(function(){
						goPage1();
						},500);
                }
            });
	}
	
function goPage1(){
	$('.page0').fadeOut(500);
	$('.topBar').fadeIn(500);
	$('.page1').fadeIn(500);
	$('body').css('background','url(/bundles/app/default/images/bg.png) top center no-repeat');
	}
	
function goPage2(){
	$('.page1').fadeOut(500);
	$('.page2').fadeIn(500);
	}
	
function choseQ(e){
	$('.page2Q li').removeClass('on');
	$(e).parents('li').addClass('on');
	$('#wishText').val($(this).text());
	}	
	
var isWechat=false;//是否加载完js-sdk
function goPage3(){
	if(isWechat){
		$('.wechatPhoto').show();
		}
		else{
			$('.fileBtn').show();
			}
	$('.page2').fadeOut(500);
	$('.page3').fadeIn(500);
	}

/*图片上传*/
//全局变量
var isSelectedImg=false;//是否选择图片
var originalImgWidth;//原图宽度
var originalImgHeight;//原图高度

//图片预览
function setImagePreview() {
    var docObj = document.getElementById("uploadBtn");
    var fileName = docObj.value;
    if (!fileName.match(/.jpg|.jpeg|.gif|.png/i)) {
        alert('您上传的图片格式不正确，请重新选择！');
        isSelectedImg=false;
        return false;
    }

    var imgObjPreview = document.getElementById("preview");
    var upBtnImg = document.getElementById("upBtnImg");
    if (docObj.files && docObj.files[0]) {
        var localImagId = document.getElementById("localImag");
        localImagId.style.display='none';
        upBtnImg.style.display='none';
        //火狐下，直接设img属性
        imgObjPreview.style.display = 'inline';
        //imgObjPreview.style.width = '144';
        //imgObjPreview.src = docObj.files[0].getAsDataURL();
        if (window.navigator.userAgent.indexOf("Chrome") >= 1 || window.navigator.userAgent.indexOf("Safari") >= 1) {
            imgObjPreview.src = window.webkitURL.createObjectURL(docObj.files[0]);
            var oimg=new Image();
            oimg.src=imgObjPreview.src;
            oimg.onload=function(){
                originalImgWidth=oimg.width;
                originalImgHeight=oimg.height;
            }
        }
        else {
            imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]);
            var oimg=new Image();
            oimg.src=imgObjPreview.src;
            oimg.onload=function(){
                originalImgWidth=oimg.width;
                originalImgHeight=oimg.height;
            }
        }
    }
    else{
            //IE下，使用滤镜
            docObj.select();
            docObj.blur();
            var imgSrc = document.selection.createRange().text;
            var localImagId = document.getElementById("localImag");
            imgObjPreview.style.display='none';
            upBtnImg.style.display='none';
            //必须设置初始大小
            //localImagId.style.width = "144";
            //localImagId.style.height = "184";
            //图片异常的捕捉，防止用户修改后缀来伪造图片
            try {
                localImagId.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
                localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
                var oimg=new Image();
                oimg.src=imgSrc;
                oimg.onload=function(){
                    originalImgWidth=oimg.width;
                    originalImgHeight=oimg.height;
                }
            } catch (e) {
                alert("您上传的图片格式不正确，请重新选择！");
                isSelectedImg=false;
                return false;
            }
            imgObjPreview.style.display = 'none';
            document.selection.empty();
        }
        isSelectedImg=true;
        return true;
    }
	
function goPage4(){
	$('.page3Img1').addClass('page3Img1Act');
	$('.page3Con').fadeOut(500);
	setTimeout(function(){
		goPage4b();
		},250);
	}
	
function goPage4b(){
	$('.page3').fadeOut(500);
	setTimeout(function(){
		$('.page4').fadeIn(1000);
		$('.page4b').fadeIn(1000);
		$('.page4Img4').fadeIn(500);
		},500);
	}
	
function showShareNote(){
	$('.popBg').fadeIn(500);
	if(isWechat){
		$('.popShare1').fadeIn(500);
		}
		else{
			$('.popShare2').fadeIn(500);
			}
	}
	
function closePop(){
	$('.pop').fadeOut(500);
	$('.popBg').fadeOut(500);
	}
	
function goLotteryPage(){
	closePop();
	$('.page4Btn1').hide();
	$('.page4Btn2').show();
	$('.page4Img5').addClass('page4Img5Act');
	}
	
function showRule(){
	$('.ruleBg').fadeIn(500);
	$('.popRule').fadeIn(500);
	setTimeout(function(){
		var swiper = new Swiper('.swiper-container', {
			direction: 'vertical'
			});
		},510);
	}
	
function getLottery(){
	$('.page4').fadeOut(500);
	$('.page4b').fadeOut(500);
	$('.page4Img4').fadeOut(500);
	$('.page5').fadeIn(500);
	setTimeout(function(){
		$('.page5Img2').addClass('page5Img2Act');
		$('.page5Img3').addClass('page5Img3Act');
	},500);
	
	//请求抽奖接口
	$.ajax({
		url: "http://luckydraw2015dec.himyweb.com/lottery",
    type: "GET",
    dataType: 'jsonp',
    cache: false,
    jsonp: 'callback',
    success: function (json) {
    	if(json.ret == 0){
    		//请求成功后
				//var aNumb=1;//奖项 0-6 0未中奖 1一等奖 2二等奖……
				$('.page5endImg1').css('background-image','url(/bundles/app/default/images/page5Award'+json.data.prize+'.png)');
				setTimeout(function(){
					$('.page5').hide();
					$('.page5end').fadeIn(500);
				},3000);
    	}
    	else{
    		//alert(json.msg);
    		$('.page5endImg1').css('background-image','url(/bundles/app/default/images/page5Award0.png)');
				setTimeout(function(){
					$('.page5').hide();
					$('.page5end').fadeIn(500);
				},3000);
    	}
    }
	});
}
	
function closeRule(){
	$('.ruleBg').fadeOut(500);
	$('.popRule').fadeOut(500);
}