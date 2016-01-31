//找到url中匹配的字符串
function findInUrl(str){
	url = location.href;
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
var loadingStep=3;
var loadingInterval;

$(document).ready(function(){
	wHeight=$(window).height();
	if(wHeight<1008){
		wHeight=1008;
		}
	$('.pageOuter').height(wHeight);
	$('.page').height(wHeight);
	});
	
function indexLoad(){
	loadingInterval=setInterval(function(){loadingAct();},500);
	
	var images=[];
    images.push("bundles/app/default/images/photoA1.png");
	images.push("bundles/app/default/images/photoA2.png");
	images.push("bundles/app/default/images/photoB1.png");
	images.push("bundles/app/default/images/photoB2.png");
	images.push("bundles/app/default/images/photoB3.png");
	images.push("bundles/app/default/images/photoB4.png");
	images.push("bundles/app/default/images/photoB5.png");
	images.push("bundles/app/default/images/photoB6.png");
	
	images.push("bundles/app/default/images/page1Img1.png");
	images.push("bundles/app/default/images/page1Img2.png");
	images.push("bundles/app/default/images/page1Img3.png");
	images.push("bundles/app/default/images/page1Img4.png");
	images.push("bundles/app/default/images/page1Img5.png");
	images.push("bundles/app/default/images/page1Img6.png");
	images.push("bundles/app/default/images/page1Img7.png");
	images.push("bundles/app/default/images/page1Img8.png");
	
	images.push("bundles/app/default/images/page2Img1.png");
	images.push("bundles/app/default/images/page2Img2.png");
	
	images.push("bundles/app/default/images/page4Img1.png");
	images.push("bundles/app/default/images/page4Img2.png");
	
	images.push("bundles/app/default/images/page5Img1.png");
	images.push("bundles/app/default/images/page5Img2.png");
	
	images.push("bundles/app/default/images/page6Img1.png");
	images.push("bundles/app/default/images/page6Img2.png");
	images.push("bundles/app/default/images/page6Img3.png");
	
    /*图片预加载*/
    var imgNum=0;
    $.imgpreload(images,
            {
                each: function () {
                    var status = $(this).data('loaded') ? 'success' : 'error';
                    if (status == "success") {
                        var v = (parseFloat(++imgNum) / images.length).toFixed(2);
						$('.pageLoadingImg1').stop().animate({left:1134*v-567,top:96-127*v},100);
                        //$("#percentShow").html('已加载:'+Math.round(v * 100) + "%");
                    }
                },
                all: function () {

                    //$("#percentShow").html("已加载:100%");
                    //图片加载完成 加载动画
					clearInterval(loadingInterval);
					page1Act();
                }
            });
	}
	
function loadingAct(){
	if(loadingStep<3){
		loadingStep++;
		}
		else{
			loadingStep=0;
			}
			
	if(loadingStep==0){
		$('.loadingTxt span').html('');
		}
		else if(loadingStep==1){
			$('.loadingTxt span').html('. ');
			}
			else if(loadingStep==2){
				$('.loadingTxt span').html('. . ');
				}
				else if(loadingStep==3){
					$('.loadingTxt span').html('. . . ');
					}
	}
	
	
function page1Act(){
	$('.pageLoading').fadeOut(500);
	$('.page1').fadeIn(500);
	$('.page1Img2').addClass('lazyShow1').show();
	$('.page1Img3').addClass('lazyShow2').show();
	$('.page1Img4').addClass('lazyShow3').show();
	$('.page1Img5').addClass('lazyShow4').show();
	$('.page1Img6').addClass('lazyShow5').show();
	$('.page1Img7').addClass('lazyShow6').show();
	$('.page1Img8').addClass('lazyShow7').show();
	setTimeout(function(){
		$('.downArrow2').show();
		$(".page1").swipe({
			swipe:function(event, direction, distance, duration, fingerCount){
				if(direction=='up'){
					goPage2();
					}
				},
			});
		},4000);
	}
	
function goPage2(){
	$('.page1').fadeOut(500);
	$('.page2').fadeIn(500);
	}
	
var selANumb=0;
function goPage3(){
	$('.page2').fadeOut(500);
	$('.page3').fadeIn(500);
	var swiper = new Swiper('.swiper-container', {
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 0,
        loop: true,
		onSlideChangeStart:function(e){
			if(e.activeIndex==1){
				selANumb=1;
				}
			else if(e.activeIndex==0){
				selANumb=2;
				}
			else if(e.activeIndex==2){
				selANumb=2;
				}
			else if(e.activeIndex==3){
				selANumb=1;
				}
			}
    	});
	}
	
var selBNumb=0;
function goPage4(){
	$('.page2').fadeOut(500);
	$('.page4').fadeIn(500);
	setTimeout(function(){
		$('.page4Img1').addClass('titleShow').show();
		setTimeout(function(){
			$('.page4Img2').fadeIn(500);
			$('.page4Img1').removeClass('titleShow')
			},600);
		},500);
	selBNumb=1;
	$(".sebBlock2").swipe({
		swipe:function(event, direction, distance, duration, fingerCount){
			if(direction=='left'){
				goLeft();
				}
				else if(direction=='right'){
					goRight();
					}
			},
		});
	
	}
	

function goPage5b(){
	$('.page4Img1').addClass('titleHide');
	$('.page4Img2').fadeOut(500);
	setTimeout(function(){
		$('.page4').fadeOut(500);
		$('.page5').fadeIn(500);
		setTimeout(function(){
			page5bAct();
			},500);
		},600);
	}
	
function page5bAct(){
	$('.page5Img1').addClass('titleShow').show();
	setTimeout(function(){
		$('.page5Img2').fadeIn(500);
		$('.page5Img1').removeClass('titleShow')
		},600);
	}
	
function goPage6(){
	var selImgSrc=$('#preview').attr('src');
	$('.selFile').attr('src',selImgSrc);
	
	$('.page5Img1').addClass('titleHide');
	$('.page5Img2').fadeOut(500);
	
	setTimeout(function(){
		$('.page5').fadeOut(500);
		$('.page6').fadeIn(500);
		setTimeout(function(){
			page6Act();
			},500);
		},600);
	}
	
function page6Act(){
	$('.page6Img2').addClass('titleShow').show();
	setTimeout(function(){
		$('.page6Img3').fadeIn(500);
		$('.page6Img2').removeClass('titleShow')
		},600);
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
        imgObjPreview.style.width = '100%';
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
            localImagId.style.width = "100%";
            //localImagId.style.height = "100%";
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
        $('.upLoadImg').css('webkitTransform','');
		$('.selFile').css('webkitTransform','');
        posX = 0, posY = 0, lastposX = 0, lastposY = 0, scale = 1, last_scale = 1, rotation = 0, last_rotation = 0;
        
        move_picture();
        goPage5b();
        window.scroll(0,0);
        return true;
    }
    
//图片移动
var posX = 0, posY = 0, lastposX = 0, lastposY = 0, scale = 1, last_scale = 1, rotation = 0, last_rotation = 0;
var lock=false,slock=true;  

function move_picture() {
    if(!Hammer.HAS_TOUCHEVENTS && !Hammer.HAS_POINTEREVENTS) {
        Hammer.plugins.fakeMultitouch();
    }

    var hammertime = Hammer(document.getElementById('preImg'), {
        preventDefault      : true,
        transformMinScale   : 1,
        dragBlockHorizontal : true,
        dragBlockVertical   : true,
        dragMinDistance     : 0
    });

    var rect = document.getElementById('localImag');

    hammertime.on('touch drag transform', function(ev) {
        if(lock){
            return false;
        }
        
        switch(ev.type) {
            case 'touch':
            last_scale = scale;
            lastposX=posX;
            lastposY=posY
            break;

            case 'drag':
            posX = lastposX+ev.gesture.deltaX;
            posY = lastposY+ev.gesture.deltaY;

            break;

            case 'transform':
            rotation = last_rotation + ev.gesture.rotation;
            scale = Math.max(1, Math.min(last_scale * ev.gesture.scale, 5));

            break;
        }
        var transform =
        "translate(" + posX + "px," + posY + "px) " +
        "scale(" + scale + "," + scale + ") " ;
		var transform2 =
        "translate(" + posX/352*640 + "px," + posY/352*640 + "px) " +
        "scale(" + scale + "," + scale + ") " ;

        $('.upLoadImg').css({'transform':transform,'-webkit-transform':transform,'-moz-transform':transform});
		$('.selFile').css({'transform':transform2,'-webkit-transform':transform2,'-moz-transform':transform2});

    });
}

function showShareNote(){
	$('.shareBg').fadeIn(500);
	$('.shareNote').fadeIn(500);
	}
	
function closeShare(){
	$('.shareBg').fadeOut(500);
	$('.shareNote').fadeOut(500);
	}
	
function showRule(){
	$('.ruleBg').fadeIn(500);
	$('.ruleBlockBg').fadeIn(500);
	$('.ruleBlock').fadeIn(500);
	}
	
function closeRule(){
	$('.ruleBg').fadeOut(500);
	$('.ruleBlockBg').fadeOut(500);
	$('.ruleBlock').fadeOut(500);
	}
	
var isGoing=false;
var oldSelBNumb;
function goLeft(){
	if(!isGoing){
		isGoing=true;
		oldSelBNumb=selBNumb;
		selBNumb=selBNumb-1;
		if(selBNumb<=0){
			selBNumb=6;
			}
		$('.sel2Img'+oldSelBNumb).addClass('goLeftHide');
		$('.sel2Img'+selBNumb).addClass('goLeftShow').show();
		setTimeout(function(){
			$('.sel2Img'+oldSelBNumb).removeClass('goLeftHide').hide();
			$('.sel2Img'+selBNumb).removeClass('goLeftShow');
			isGoing=false;
			},1100);
		}
	}
	
function goRight(){
	if(!isGoing){
		isGoing=true;
		oldSelBNumb=selBNumb;
		selBNumb=selBNumb+1;
		if(selBNumb>6){
			selBNumb=1;
			}
		$('.sel2Img'+oldSelBNumb).addClass('goRightHide');
		$('.sel2Img'+selBNumb).addClass('goRightShow').show();
		setTimeout(function(){
			$('.sel2Img'+oldSelBNumb).removeClass('goRightHide').hide();
			$('.sel2Img'+selBNumb).removeClass('goRightShow');
			isGoing=false;
			},1100);
		}
	}


