
window.log=function(){log.history=log.history||[];log.history.push(arguments);if(this.console)console.log(Array.prototype.slice.call(arguments));};(function($){$.fn.slideshow=function(options,callback){var settings={pager:'.pager',autoplay:true,delay:5000,pagerthumbs:false,perpage:5,pagerid:'#Pager',pauseonhover:false,ajax:false,paginate:true,pointer:'.pointer',pagerappend:false,slide:false,perslide:1,padding:30};if(options)$.extend(settings,options)
return this.each(function(){var $this=$(this),$slideshow=$(this).children('ul').eq(0),$slidereel=$slideshow,$slideimages=$slidereel.find('li'),pageli='',imageWidth=$slideimages.width(),imageSum=$slideimages.size(),imageReelWidth=$slideimages.outerWidth(true)*imageSum,imageReelHeight=$slideimages.height();imageWidth=$slideimages.width()+settings.padding;if(settings.slide){$slidereel.css({width:imageReelWidth+imageSum*settings.padding,height:imageReelHeight,position:"relative"});}
$slideimages.css({height:imageReelHeight});$slideimages.each(function(i){i%settings.perslide==0?pageli+='<li><a href="#">'+(i)+'</a></li>':'';});if(settings.pagerappend)$(settings.pager).append(pageli);$(settings.pager).find('li:first').addClass("active").end().find("li").live('click',function(e){e.preventDefault();$active=$(this);if(settings.autoplay)clearInterval(play);rotate();if(settings.autoplay)rotateSwitch();if(!settings.pagerappend){$(settings.pointer).animate({top:$(this).offset().top-176.5+$(this).height()/2},300);}});$slidereel.find('li:first').addClass("active");function rotate(){var triggerID=$active.prevAll().length;var image_reelPosition=triggerID*imageWidth*settings.perslide;if(settings.slide){$(settings.pager).find("li").removeClass('active');$active.addClass('active');$slidereel.animate({left:-image_reelPosition},500);}else{$(settings.pager).find("li").removeClass('active');$slidereel.find('li').removeClass("active").end().find('li').eq(triggerID).addClass("active");$active.addClass('active');}};function rotateSwitch(){play=setInterval(function(){$active=$(settings.pager).find('.active').next();if($active.length===0){$active=$(settings.pager).find('li:first');}
rotate();},settings.delay);};if(settings.autoplay)rotateSwitch();if(settings.pauseonhover){$slideshow.hover(function(){if(settings.autoplay)clearInterval(play);},function(){if(settings.autoplay)rotateSwitch();});}});};})(this.jQuery);(function($){$.fn.paginate=function(options,callback){var settings={perpage:5,itemwidth:110,nextlink:'#Next',prevlink:'#Prev'};$.fn.extend(settings,options);return this.each(function(){$id=$(this);$items=$id.find('li');var numPages=Math.ceil($items.length/settings.perpage)-1;var current=0;var $next=$(settings.nextlink);var $back=$(settings.prevlink);$id.css({position:"relative",width:$items.length*settings.itemwidth+"px"});$back.addClass('paging-disabled').click(function(e){pagination('<');e.preventDefault();});$next.click(function(e){pagination('>');e.preventDefault();});$items.css({width:settings.itemwidth});pagination=function(direction){reveal=function(current){$back.removeClass('paging-disabled');$next.removeClass('paging-disabled');_pageWidth=current*settings.perpage*settings.itemwidth;$id.animate({left:-_pageWidth},1000);};if(direction=='<'){if(current>1){reveal(current-=1);}
else if(current==1){reveal(current-=1);$back.addClass('paging-disabled');}}else{if(current<numPages-1){reveal(current+=1);}
else if(current==numPages-1){reveal(current+=1);$next.addClass('paging-disabled');}}}
if(typeof callback=='function'){callback.call(this);}});};})(jQuery);(function($){jQuery.fn.yaselect=function(options){options=options||{};return this.each(function(index,select){var jselect=jQuery(select).css({position:'absolute',top:0,left:0,zIndex:1}).addClass('yaselect-select'),wrap=jQuery('<div class="yaselect-wrap yaselect-open" tabindex="0"><div class="yaselect-current"></div></div>'),anchor=wrap.wrap('<div class="yaselect-anchor" style="position:relative;"></div>').parent(),gettext=function(){return jselect.find('option:nth('+(select.selectedIndex||0)+')').text();},curr=wrap.find('.yaselect-current').text(gettext()),confirm=function(to_focus_wrap){jselect.blur();curr.text(gettext());jselect.css({top:wrap.outerHeight()});if(to_focus_wrap)wrap.focus();};if(options.hoverOnly||window.navigator&&navigator.userAgent.match(/iphone|ipod|ipad/i)){wrap.toggleClass('yaselect-open yaselect-close');return jselect.before(anchor).css({opacity:0.001}).change(function(e){curr.text(gettext());}).appendTo(anchor);}
jselect.before(anchor).keydown(function(e){if(e.which==13||e.which==32){e.preventDefault();confirm(true);}}).change(function(e){curr.text(gettext());}).blur(function(e){jselect.hide();wrap.toggleClass('yaselect-open yaselect-close');}).click(function(e){if(jselect.is(':hidden')){wrap.toggleClass('yaselect-open yaselect-close');jselect.show();setTimeout(function(){jselect.focus();});}else{confirm(true);}}).appendTo(anchor);wrap.mousedown(function(e){jselect.click();}).keydown(function(e){if([13,32,37,38,39,40].indexOf(e.which)!=-1){e.preventDefault();jselect.click();}});select.size=options.size||5;confirm();});}})(jQuery);(function($){$.fn.accordion=function(options){options=options||{};return this.each(function(){var $this=$(this),$handle=$this.find('h2');$handle.next().hide();$handle.eq(0).addClass("expanded").next().show();$handle.click(function(e){e.preventDefault();var _this=$(this);$handle.next().slideUp(400).end().removeClass("expanded");_this.next().slideDown(400).end().addClass("expanded");});});}})(jQuery);
(function($){$.facebox=function(data,klass){$.facebox.loading()
if(data.ajax)fillFaceboxFromAjax(data.ajax,klass)
else if(data.image)fillFaceboxFromImage(data.image,klass)
else if(data.div)fillFaceboxFromHref(data.div,klass)
else if($.isFunction(data))data.call($)
else $.facebox.reveal(data,klass)}
$.extend($.facebox,{settings:{opacity:0.6,overlay:true,loadingImage:'/images/site/loader.gif',closeImage:'/images/site/closelabel.png',imageTypes:['png','jpg','jpeg','gif'],faceboxHtml:'\
      <div id="facebox" style="display:none;"> \
            <div class="popup"> \
              <table> \
                <tbody> \
                  <tr> \
                    <td class="body"> \
                      <div class="content"> \
                      </div> \
                      <div class="footer"> \
                        <a href="#" class="close"><img src="/facebox/closelabel.png" title="close" class="close_image" /></a> \
                      </div> \
                    </td> \
                  </tr> \
                </tbody> \
              </table> \
            </div> \
          </div>'},loading:function(){init()
if($('#facebox .loading').length==1)return true
showOverlay()
$('#facebox .content').empty()
$('#facebox .body').children().hide().end().append('<div class="loading"><img src="'+$.facebox.settings.loadingImage+'"/></div>')
$('#facebox').css({top:getPageScroll()[1]+(getPageHeight()/10),left:$(window).width()/2-305}).show()
$(document).bind('keydown.facebox',function(e){if(e.keyCode==27)$.facebox.close()
return true})
$(document).trigger('loading.facebox')},reveal:function(data,klass){$(document).trigger('beforeReveal.facebox')
if(klass)$('#facebox .content').addClass(klass)
$('#facebox .content').append(data)
$('#facebox .loading').remove()
$('#facebox .body').children().fadeIn('normal')
$('#facebox').css('left',$(window).width()/2-($('#facebox .popup').width()/2))
$(document).trigger('reveal.facebox').trigger('afterReveal.facebox')},close:function(){$(document).trigger('close.facebox')
return false}})
$.fn.facebox=function(settings){if($(this).length==0)return
init(settings)
function clickHandler(){$.facebox.loading(true)
var klass=this.rel.match(/facebox\[?\.(\w+)\]?/)
if(klass)klass=klass[1]
fillFaceboxFromHref(this.href,klass)
return false}
return this.bind('click.facebox',clickHandler)}
function init(settings){if($.facebox.settings.inited)return true
else $.facebox.settings.inited=true
$(document).trigger('init.facebox')
makeCompatible()
var imageTypes=$.facebox.settings.imageTypes.join('|')
$.facebox.settings.imageTypesRegexp=new RegExp('\.('+imageTypes+')$','i')
if(settings)$.extend($.facebox.settings,settings)
$('body').append($.facebox.settings.faceboxHtml)
var preload=[new Image(),new Image()]
preload[0].src=$.facebox.settings.closeImage
preload[1].src=$.facebox.settings.loadingImage
$('#facebox').find('.b:first, .bl').each(function(){preload.push(new Image())
preload.slice(-1).src=$(this).css('background-image').replace(/url\((.+)\)/,'$1')})
$('#facebox .close').click($.facebox.close)
$('#facebox .close_image').attr('src',$.facebox.settings.closeImage)}
function getPageScroll(){var xScroll,yScroll;if(self.pageYOffset){yScroll=self.pageYOffset;xScroll=self.pageXOffset;}else if(document.documentElement&&document.documentElement.scrollTop){yScroll=document.documentElement.scrollTop;xScroll=document.documentElement.scrollLeft;}else if(document.body){yScroll=document.body.scrollTop;xScroll=document.body.scrollLeft;}
return new Array(xScroll,yScroll)}
function getPageHeight(){var windowHeight
if(self.innerHeight){windowHeight=self.innerHeight;}else if(document.documentElement&&document.documentElement.clientHeight){windowHeight=document.documentElement.clientHeight;}else if(document.body){windowHeight=document.body.clientHeight;}
return windowHeight}
function makeCompatible(){var $s=$.facebox.settings
$s.loadingImage=$s.loading_image||$s.loadingImage
$s.closeImage=$s.close_image||$s.closeImage
$s.imageTypes=$s.image_types||$s.imageTypes
$s.faceboxHtml=$s.facebox_html||$s.faceboxHtml}
function fillFaceboxFromHref(href,klass){if(href.match(/#/)){var url=window.location.href.split('#')[0]
var target=href.replace(url,'')
if(target=='#')return
$.facebox.reveal($(target).html(),klass)}else if(href.match($.facebox.settings.imageTypesRegexp)){fillFaceboxFromImage(href,klass)}else{fillFaceboxFromAjax(href,klass)}}
function fillFaceboxFromImage(href,klass){var image=new Image()
image.onload=function(){$.facebox.reveal('<div class="image"><img src="'+image.src+'" /></div>',klass)}
image.src=href}
function fillFaceboxFromAjax(href,klass){$.get(href,function(data){$.facebox.reveal(data,klass)})}
function skipOverlay(){return $.facebox.settings.overlay==false||$.facebox.settings.opacity===null}
function showOverlay(){if(skipOverlay())return
if($('#facebox_overlay').length==0)
$("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')
$('#facebox_overlay').hide().addClass("facebox_overlayBG").css('opacity',$.facebox.settings.opacity).click(function(){$(document).trigger('close.facebox')}).fadeIn(200)
return false}
function hideOverlay(){if(skipOverlay())return
$('#facebox_overlay').fadeOut(200,function(){$("#facebox_overlay").removeClass("facebox_overlayBG")
$("#facebox_overlay").addClass("facebox_hide")
$("#facebox_overlay").remove()})
return false}
$(document).bind('close.facebox',function(){$(document).unbind('keydown.facebox')
$('#facebox').fadeOut(function(){$('#facebox .content').removeClass().addClass('content')
$('#facebox .loading').remove()
$(document).trigger('afterClose.facebox')})
hideOverlay()})})(jQuery);