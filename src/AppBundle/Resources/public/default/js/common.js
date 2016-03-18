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

var w

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
		$('.page').css('-webkit-transform-origin','50% '+(1008-wHeight)/2+'px');
		$('.page4Img4').css('bottom','-'+(1008-wHeight)/3*2+'px');
		
	$('.page5end').on('touchmove',function(e){
		e.preventDefault();
		});
	}
	
	
	var _title="美丽心愿，从头开始";
	var _pageurl=notWechatSharlUrl;
	var _picurl=noWechatShareImg;/*"http://dev.slek.com.cnbundles/app/default/images/share.jpg";*/
	var _sharetext="＃带你和闺蜜去见郭碧婷＃刚刚我许下了一个美丽心愿，听说舒蕾会帮忙实现哦，你的愿望呢？";
	
		$(".douban").click(function(){

			shareToDouban(_title,_pageurl,_sharetext,_picurl);
			// ga('send', 'event', 'Social', 'share','douban')
		});
		$(".shareto .renren").click(function(){

			shareToRenRen(_title,_pageurl,_sharetext,_picurl);
			// ga('send', 'event', 'Social', 'share','Renren')
		});
		$(".shareto .weibo").click(function(){

			shareToSina(_sharetext,_pageurl,_picurl);
			// ga('send', 'event', 'Social', 'share','Sina')
		});
		$(".shareto .tengxun").click(function(){

			shareToTencent(_title,_pageurl,_sharetext);
			// ga('send', 'event', 'Social', 'share','Tencent')
		});
		
		$('.sinaShare').click(function(){
			shareToSina(_sharetext,_pageurl,_picurl);
			});
			
		$('.qqShare').click(function(){
			shareToTencent(_title,_pageurl,_sharetext);
			});
			
		$('.qzoneShare').click(function(){
			shareToQzone(_title,_pageurl,_sharetext,_picurl);
			});
});

var lh=380;
function loadingImg(){
	var images=[];
	images.push("bundles/app/default/images/bg.png");
	
	images.push("bundles/app/default/images/page1Img1.png");
	images.push("bundles/app/default/images/page1Img2.png");
	images.push("bundles/app/default/images/page1Img5.png");
	images.push("bundles/app/default/images/page1Img6.png");
	images.push("bundles/app/default/images/page1Img7.png");
	
	images.push("bundles/app/default/images/page2Img1.png");
	images.push("bundles/app/default/images/page2Img3.png");
	
	images.push("bundles/app/default/images/page3Img1.png");
	images.push("bundles/app/default/images/page3Img3.png");
	
	/*images.push("bundles/app/default/images/page4Img1.png");
	images.push("bundles/app/default/images/page4Img2.png");
	
	images.push("bundles/app/default/images/page5Award0.png");
	images.push("bundles/app/default/images/page5Award1.png");
	images.push("bundles/app/default/images/page5Award2.png");
	images.push("bundles/app/default/images/page5Award3.png");
	images.push("bundles/app/default/images/page5Award4.png");
	images.push("bundles/app/default/images/page5Award5.png");
	images.push("bundles/app/default/images/page5Award6.png");
	images.push("bundles/app/default/images/page5Img2.png");
	images.push("bundles/app/default/images/page5Img3.png");
	images.push("bundles/app/default/images/page5Img4.png");*/
	
	images.push("bundles/app/default/images/rule1.png");
	images.push("bundles/app/default/images/rule2.png");
	images.push("bundles/app/default/images/rule3.png");
	images.push("bundles/app/default/images/rule4.png");
	images.push("bundles/app/default/images/rule5.png");
	
	images.push("bundles/app/default/images/share1.png");
	images.push("bundles/app/default/images/share2.png");
	
	images.push("bundles/app/default/images/topImg1.png");
	
	
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


function loadingShare(){
	var images=[];
	images.push("bundles/app/default/images/bg2.png");
	
	images.push("bundles/app/default/images/page6Img1.png");
	
	images.push("bundles/app/default/images/rule1.png");
	images.push("bundles/app/default/images/rule2.png");
	images.push("bundles/app/default/images/rule3.png");
	
	images.push("bundles/app/default/images/topImg1.png");
	
	
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
											goPage6();
										},500);
									}
								});
}

function loadingTop(){
	var images=[];
	images.push("bundles/app/default/images/bg.png");
	
	images.push("bundles/app/default/images/topBg.png");
	
	images.push("bundles/app/default/images/rule1.png");
	images.push("bundles/app/default/images/rule2.png");
	images.push("bundles/app/default/images/rule3.png");
	
	images.push("bundles/app/default/images/topImg1.png");
	
	
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
											goPage7();
										},500);
									}
								});
}

function loadSuccess(){
	var images=[];
	images.push("bundles/app/default/images/bg.png");
	
	images.push("bundles/app/default/images/page4Img1.png");
	images.push("bundles/app/default/images/page4Img2.png");
	
	images.push("bundles/app/default/images/page5Award0.png");
	images.push("bundles/app/default/images/page5Award1.png");
	images.push("bundles/app/default/images/page5Award2.png");
	images.push("bundles/app/default/images/page5Award3.png");
	images.push("bundles/app/default/images/page5Award4.png");
	images.push("bundles/app/default/images/page5Award5.png");
	images.push("bundles/app/default/images/page5Award6.png");
	images.push("bundles/app/default/images/page5Award7.png");
	images.push("bundles/app/default/images/page5Img2.png");
	/*images.push("bundles/app/default/images/page5Img3.png");*/
	images.push("bundles/app/default/images/page5Img4.png");
	
	images.push("bundles/app/default/images/rule1.png");
	images.push("bundles/app/default/images/rule2.png");
	images.push("bundles/app/default/images/rule3.png");
	
	images.push("bundles/app/default/images/share1.png");
	images.push("bundles/app/default/images/share2.png");
	
	images.push("bundles/app/default/images/topImg1.png");
	
	
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
											goPage4();
										},500);
									}
								});
}

function goPage1(){
	$('.topBtn1').show();
	$('.page0').hide();
	$('.topBar').fadeIn(500);
	$('.page1').fadeIn(500);
	$('body').css('background','url(bundles/app/default/images/bg.png) top center no-repeat');
	$('input').val('');
}

var wish1=['想和闺蜜一起去见郭碧婷','想亲眼见到郭碧婷的黑长直头发','想和闺蜜去见郭碧婷不加修饰的美','想和闺蜜去见郭碧婷的纯真美'];
var wish2=['一起去旅行，是闺蜜间都曾有过的约定。来许愿吧，那些年欠下的旅行，让舒蕾心愿团队助你实现。','想要保持苗条？别只羡慕别人了。来许愿，让舒蕾心愿团队助你实现。','想在心情不好的时候打“飞旳”去喂鸽子吗？来许愿，舒蕾心愿团队给你报销打的费。','每一个空着的心愿瓶，都在等你。快来定制一个独家心愿，用你的创意让心愿被看见。','每一个用心许下的愿望，都值得被实现。快来吧，心愿瓶里的心愿，还差你的一个。'];

var wishArr=[0,1,2,3];
var ranNumb1=parseInt(randomNumb(0,2));
var ranNumb2=parseInt(randomNumb(0,4));

function changeFirstWish(){
	var aIndex=$('.page2Q li').index($('.page2Q li.on'));
	ranNumb1=parseInt(randomNumb(0,2));
	var selIndex=wishArr[ranNumb1];
	wishArr.push(tempSelIndex);
	tempSelIndex=selIndex;
	wishArr.splice(ranNumb1,1);
	$('.firstWish').html(wish1[selIndex]);
	if(aIndex==0){
		$('#wishText').val(wish1[selIndex]);
		}
	}
	
var tempSelIndex;
function goPage2(){
	var selIndex=wishArr[ranNumb1];
	tempSelIndex=selIndex;
	wishArr.splice(ranNumb1,1);
	$('.firstWish').html(wish1[selIndex]);
	$('.wish').html(wish2[ranNumb2]);
	$('.wish').val(wish2[ranNumb2]);
	$('.topBtn1').hide();
	$('.page1').fadeOut(500);
	$('.page2').fadeIn(500);
	
	wx.checkJsApi({
		jsApiList: [
		'chooseImage'
		],
		success: function (res) {
			isWechat=true;
		}
	});
}

function choseQ(e){
	var aIndex=$('.page2Q li').index($(e).parents('li'));
	$('.page2Q li').removeClass('on');
	$(e).parents('li').addClass('on');
	if(aIndex==0){
		$('#wishText').val($(e).text());
		}
		else{
			$('#wishText').val($('.wish').val());
			}
}	

var isWechat=false;//是否加载完js-sdk
var imgId;

function backPage2(){
	$('.page3').fadeOut(500);
	$('.page2').fadeIn(500);
	$('.backPage2').hide();
	}

function goPage3(){
	var wish=$('#wishText').val();
	if(wish==''){
		alert('请选择或者填写心愿');
		return false;
	}
	if(isWechat){
		$('.wechatPhoto').show();
		$('.wechatPhoto').click(function(){
			wx.chooseImage({
				count: 1, // 默认9
				sizeType: ['original'], // 可以指定是原图还是压缩图，默认二者都有
				sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
				success: function (res) {
					$('.upLoadImg').attr('src',res.localIds);
					$('#preview').show();
					setTimeout(function(){
						resizePhoto();
						},200);
					isSelectedImg=true;
					imgId = res.localIds.pop();
					
					wx.uploadImage({
						  localId: imgId, // 需要上传的图片的本地ID，由chooseImage接口获得
						  isShowProgressTips: 1, // 默认为1，显示进度提示
						  success: function (res) {
							  var serverId = res.serverId; // 返回图片的服务器端ID
							  $('#imageId').val(res.serverId);
						  }
					  });
				},
				/*cancel:function(res){
					alert('请允许');
					window.location.reload();
				},*/
				fail:function(res){
					alert('选择照片失败，请重试');
					window.location.reload();
				}
			});
		});
	}
	else{
		$('.fileBtn').show();
	}
	$('.page2').fadeOut(500);
	$('.page3').fadeIn(500);
	$('.backPage2').show();
}

function resizePhoto(){
	var oW=148;
	var oH=188;
	var pw=148;
	var ph=$('#preview').height();
	var obl=148/188;
	var pbl=pw/ph;
	if(pbl>obl){
		$('#preview').height(188);
		$('#preview').width(pw/ph*188);
		$('#preview').css('left',-(pw/ph*188-148)/2+'px');
		$('#preview').css('top','0');
		}
		else{
			$('#preview').width(148);
			$('#preview').height(148*ph/pw);
			$('#preview').css('top',-(148*ph/pw-188)/2+'px');
			$('#preview').css('left','0');
			}
	}


/*图片上传*/
//全局变量
var isSelectedImg=false;//是否选择图片
var originalImgWidth;//原图宽度
var originalImgHeight;//原图高度

//图片预览
function setImagePreview() {
	var docObj = document.getElementById("headImg");
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
					setTimeout(function(){
						resizePhoto();
						},200);
					return true;
				}
				

function go4(){
	submitInfo();
}	

var p4Time;
function goPage4(){
	p4Time=setInterval(function(){
		wx.checkJsApi({
			jsApiList: [
				'onMenuShareTimeline',
				'onMenuShareAppMessage',
				'chooseImage',
				'previewImage',
				'uploadImage',
				'downloadImage',
				'getNetworkType'
			],
			success: function (res) {
				isWechat=true;
				clearInterval(p4Time);
			}
		},500);
	});
	
	$('.page0').hide();
	$('.topBar').fadeIn(500);
	$('body').css('background','url(bundles/app/default/images/bg.png) top center no-repeat');
	setTimeout(function(){
		goPage4b();
	},250);
}

function goPage4b(){
	clearInterval(p4Time);
	$('.page3').fadeOut(500);
	setTimeout(function(){
		$('.page4').fadeIn(1000);
		$('.page4b').fadeIn(1000);
		$('.page4Img4').fadeIn(500);
		setTimeout(function(){
			$('.page4Img5').addClass('page4Img5Act');
		},500);
	},500);
}

function showShareNote(){
	clearInterval(p4Time);
	$('.popBg').fadeIn(500);
	if(isWechat){
		//alert('isWechat:'+ua);
		$('.popShare2').hide();
		$('.popShare1').fadeIn(500);
	}
	else{
		if (/iPhone|iPod|iPad/i.test(ua)){//ios
			if(/MicroMessenger/.test(ua)){//微信
				//alert('iOS_MicroMessenger:'+ua);
				$('.popShare2').hide();
				$('.popShare1').fadeIn(500);
				}
				else{//非微信
					//alert('iOS_非MicroMessenger:'+ua);
					$('.popShare1').hide();
					$('.popShare2').fadeIn(500);
					}
			}
			else{//android
				if(/MicroMessenger/.test(ua)){//微信
					//alert('MicroMessenger:'+ua);
					$('.popShare2').hide();
					$('.popShare1').fadeIn(500);
					}
					else{//非微信
						//alert('非MicroMessenger:'+ua);
						$('.popShare1').hide();
						$('.popShare2').fadeIn(500);
						}
				}
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
}

function showRule(){
	ga('send', 'pageview', '/page_mechanics ');
	$('.ruleBg').fadeIn(500);
	$('.popRule').fadeIn(500);
	setTimeout(function(){
		var swiper = new Swiper('.swiper-container', {
			direction: 'vertical'
		});
	},550);
}

var endPrizeNumb;
var isLotteryed=false;
function getLottery(){
	if(!isLotteryed){
		ga('send', 'pageview', '/page_luckydraw');
		closePop();
		$('.page4').fadeOut(500);
		$('.page4b').fadeOut(500);
		$('.page4Img4').fadeOut(500);
		$('.page5').fadeIn(500);
		setTimeout(function(){
			$('.page5Img2').addClass('page5Img2Act');
			//$('.page5Img3').addClass('page5Img3Act');
		},500);
		
		//请求抽奖接口
		$.ajax({
			url: "http://dev.slek.com.cn/lottery",
			type: "GET",
			dataType: 'jsonp',
			cache: false,
			jsonp: 'callback',
			success: function (json) {
				if(json.ret == 0){
					//请求成功后
					//var aNumb=1;//奖项 0-6 0未中奖 1一等奖 2二等奖……
					isLotteryed=true;
					$('.prize0').hide();
					$('.page5endImg1').show();
					$('.page5endImg1').css('background-image','url(bundles/app/default/images/page5Award'+json.data.prize+'.png)');
					endPrizeNumb=json.data.prize;
					setTimeout(function(){
						$('.page5Img2').removeClass('page5Img2Act');
						$('.page5').hide();
						$('.page5end').fadeIn(500);
						ga('send', 'pageview', '/page_result_'+endPrizeNumb);
					},6000);
				}
				else{
					//alert(json.msg);
					isLotteryed=true;
					$('.prize0').show();
					$('.page5endImg1').hide();
					$('.page5endImg1').css('background-image','url(bundles/app/default/images/page5Award0.png)');
					setTimeout(function(){
						$('.page5Img2').removeClass('page5Img2Act');
						$('.page5').hide();
						$('.page5end').fadeIn(500);
						ga('send', 'pageview', '/page_result_none');
					},6000);
				}
			}
		});
	}
	else{
		//分享过再分享
		}
}

function closeRule(){
	$('.ruleBg').fadeOut(500);
	$('.popRule').fadeOut(500);
}

function goPage6(){
	$('.page0').hide();
	$('.topBar').fadeIn(500);
	$('.page6').fadeIn(500);
	$('body').css('background','url(bundles/app/default/images/bg2.png) top center no-repeat');
}

function goPage7(){
	$('.page0').hide();
	$('.topBar').fadeIn(500);
	$('.page7').fadeIn(500);
	$('body').css('background','#f9ecbe');
	var tLen=$('.topBlock').length;
	for(var i=0;i<tLen;i++){
		$('.topBlock').eq(i).find('.swiper-container3').attr('id','swiper-container3'+i);
		$('.topBlock').eq(i).find('.tBtn1').attr('id','tBtn1'+i);
		$('.topBlock').eq(i).find('.tBtn2').attr('id','tBtn2'+i);
	}
	setTimeout(function(){
		var swiper = new Swiper('.swiper-container2', {
			direction: 'vertical',
			slidesPerView: 'auto'
		});			
		setTimeout(function(){
			var tLen2=$('.topBlock').length;
			for(var i=0;i<tLen2;i++){
				var swiper = new Swiper('#swiper-container3'+i, {
					nextButton: '#tBtn1'+i,
					prevButton: '#tBtn2'+i,
					spaceBetween: 1,
					loop: true
				});
			}
		},500);
	},510);
}

	

	var target_str='_blank';
	//打开窗口的大小
	var window_size="scrollbars=no,width=600,height=450,"+"left=75,top=20,status=no,resizable=yes";	

			//分享到新浪网
	function shareToSina(sharetext, pageurl, picUrl) {
		window.open("http://v.t.sina.com.cn/share/share.php?title=" + encodeURIComponent(sharetext) + "&url=" + encodeURIComponent(pageurl)+"&pic="+encodeURIComponent(picUrl), target_str,window_size)}
	
	
	//分享到腾讯微博
	function shareToTencent(title, pageurl, sharetext) {
		window.open('http://share.v.t.qq.com/index.php?c=share&a=index&title='+encodeURIComponent(sharetext)+'&url='+encodeURIComponent(pageurl), target_str,window_size)}
	
	//分享到豆瓣网
	function shareToDouban(title, pageurl, sharetext,picurl) {
		window.open('http://www.douban.com/recommend/?title=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(pageurl) +"&image="+encodeURIComponent(picurl), target_str,window_size);}
		
	//分享到QQ空间
	function shareToQzone(title, pageurl, sharetext,picurl) {	
		window.open("http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url="+encodeURIComponent(pageurl)+"&title="+encodeURIComponent(title)+"&pics="+encodeURIComponent(picurl)+"&summary="+encodeURIComponent(sharetext), target_str,window_size);
	}
	
	
	//share to renren
	function shareToRenRen(title, pageurl,sharetext,picurl){
		
	window.open("http://widget.renren.com/dialog/share?url="+encodeURIComponent(pageurl)+"&title="+encodeURIComponent(title)+"&content="+encodeURIComponent(sharetext)+"&pic="+encodeURIComponent(picurl)+"&message="+encodeURIComponent(sharetext), target_str,window_size);
	}