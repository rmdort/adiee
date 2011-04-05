/*
	Site:		NLBA
	File:		/js/plugins.js
	Author:		vinay@artminister.com
	Version:  19-02-2010
	
*/

// usage: log('inside coolFunc', this, arguments);
// paulirish.com/2009/log-a-lightweight-wrapper-for-consolelog/
window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};



// Slideshow Plugin
(function($){
  
  $.fn.slideshow=function(options,callback){    
    
    var settings={
      pager:'.pager',
      autoplay:true,
      delay:5000,
      pagerthumbs:false,
      perpage:5,
      pagerid:'#Pager',
      pauseonhover:false,
      ajax:false,
      paginate:true,
      pointer:'.pointer',
      pagerappend:false,
      slide:false,
      perslide:1,
      padding:30
    };
    
    if(options) $.extend(settings,options)
  
  	return this.each(function(){
  	  
  	  // Init Variables
  	  
  	  var $this = $(this),
  	    	$slideshow = $(this).children('ul').eq(0),
  	    	$slidereel = $slideshow,
  	    	$slideimages = $slidereel.find('li'),
  	    	pageli='',
  	    	imageWidth = $slideimages.width(),
          imageSum = $slideimages.size(),
          imageReelWidth = $slideimages.outerWidth(true) * imageSum,
          imageReelHeight =$slideimages.height();
          
          imageWidth = $slideimages.width()+settings.padding;
          
          if(settings.slide){
            $slidereel.css({
              width:imageReelWidth + imageSum*settings.padding,
              height:imageReelHeight,
              position:"relative"
             });
            
          }          
          
          $slideimages.css({
            //width:imageWidth,
            //height:imageReelHeight
          });
  	  
      	  // Append Page Numbers
  	  
      	  $slideimages.each(function(i){
      	    i%settings.perslide==0?pageli += '<li><a href="#">'+(i)+'</a></li>':''; 
      	  });  	  
  	  

    	    if(settings.pagerappend) $(settings.pager).empty().append(pageli);
    	    
    	    $(settings.pager)
    	    .find('li:first').addClass("active").end()      	    
      	  .find("li").live('click',function(e) {	
      	    e.preventDefault();
        		$active = $(this); 
        		if(settings.autoplay) clearInterval(play); 
        		rotate(); 
        		if(settings.autoplay) rotateSwitch();
        		
        		if(!settings.pagerappend){
              $(settings.pointer).animate({
                top:$(this).offset().top - 181 + $(this).height()/2
              },300);
            }
            
        	  });
        	
        	$slidereel.find('li:first').addClass("active");      	  
  	  
  	  
  	  
      	  //Paging + Slider Function
        	function rotate(){	
        		var triggerID = $active.prevAll().length; //Get number of times to slide
        		var image_reelPosition = triggerID * imageWidth * settings.perslide; //Determines the distance the image reel needs to slide        		
        		
        		//Slider Animation
        		if(settings.slide){
        		  $(settings.pager).find("li").removeClass('active'); //Remove all active class
              $active.addClass('active'); //Add active class (the $active is declared in the rotateSwitch function)
              
          		$slidereel.animate({ 
          			left: -image_reelPosition
          		}, 500);
            }else{
              $(settings.pager).find("li").removeClass('active'); //Remove all active class
          		$slidereel.find('li').removeClass("active").end().find('li').eq(triggerID).addClass("active");
          		$active.addClass('active'); //Add active class (the $active is declared in the rotateSwitch function)
            }
        
        	}; 
    	
        	//Rotation + Timing Event
        	function rotateSwitch(){	
        		play = setInterval(function(){ 
        			$active = $(settings.pager).find('.active').next();
        			if ( $active.length === 0) { //If paging reaches the end...
        				$active = $(settings.pager).find('li:first'); //go back to first
        			}
        			rotate(); 
        		}, settings.delay); 
        	};
      
          if(settings.autoplay) rotateSwitch();
            
          //On Hover
          if(settings.pauseonhover){
          	$slideshow.hover(function() {
          		if(settings.autoplay) clearInterval(play); //Stop the rotation
          	}, function() {
          		if(settings.autoplay) rotateSwitch(); //Resume rotation
          	});
          
          }  	    	  
      	    	  
  	});
  	
  }; // End Slideshow
  
})(this.jQuery);


// Paginate Plugin

(function($){
  
  $.fn.paginate=function(options, callback){
    
    var settings = {
      perpage : 5,
      itemwidth:110,
      nextlink:'#Next',
      prevlink:'#Prev'
    };
    
    $.fn.extend(settings,options);
    
    return this.each(function(){
      
      $id = $(this);
	    $items =$id.find('li');
	    var numPages = Math.ceil($items.length / settings.perpage) - 1;
	    var current = 0;
  
	    var $next = $(settings.nextlink);
	    var $back = $(settings.prevlink);
	    
	    $id.css({
	      position:"relative",
	      width:$items.length * settings.itemwidth+"px"
	    });
  
	    $back
        .addClass('paging-disabled')
        .click(function(e) {
          pagination('<');
          e.preventDefault();
        });
    
      $next.click(function(e) {
        pagination('>');
        e.preventDefault();
      });
  
      $items
        .css({
          width:settings.itemwidth
        });
        
      
        pagination = function (direction){
          reveal = function (current){
            $back.removeClass('paging-disabled');
            $next.removeClass('paging-disabled');
            
            _pageWidth = current * settings.perpage * settings.itemwidth;
            $id
              .animate({
                left:- _pageWidth
              },1000);
              
          };
          // 4. Move previous and next  
        	if (direction == '<') { // previous
            if (current > 1) {
              reveal(current -= 1);
            }
            else if (current == 1) {
              reveal (current -= 1);
              $back.addClass('paging-disabled');
            }
          } else {
            if (current < numPages - 1) {
              reveal(current += 1);
            }
            else if (current == numPages - 1) {
              reveal(current += 1);
              $next.addClass('paging-disabled');
            }
          }
        }
        
        
        if (typeof callback == 'function') {
            callback.call(this); 
        }
        
        
    });
    
  };
  
})(jQuery);

/*
 * yaselect
 * is yet-another jquery plugin for making <select> elements stylable by CSS
 * http://github.com/choonkeat/yaselect
 *
 * Copyright (c) 2011 Chew Choon Keat
 * Released under the MIT license
 *
 * Options:
 *  topOffset: pixels, default 0
 *  leftOffset: pixels, default 0
 *  size: number of select options to show, default 5
 *  tag: html tag name, default span
 *  hoverOnly: true / false, default false
 */
 
(function($){ 
  jQuery.fn.yaselect = function(options) {
    options = options || {};
    return this.each(function(index, select) {
      var jselect = jQuery(select).css({position:'absolute',top:0,left:0,zIndex:1}).addClass('yaselect-select'),
          wrap    = jQuery('<div class="yaselect-wrap yaselect-open" tabindex="0"><div class="yaselect-current"></div></div>'),
          anchor  = wrap.wrap('<div class="yaselect-anchor" style="position:relative;"></div>').parent(),
          gettext = function() { return jselect.find('option:nth(' + (select.selectedIndex || 0) + ')').text(); },
          curr    = wrap.find('.yaselect-current').text(gettext()),
          confirm = function(to_focus_wrap) { jselect.blur(); curr.text(gettext()); jselect.css({top: wrap.outerHeight()}); if (to_focus_wrap) wrap.focus(); };
      if (options.hoverOnly || window.navigator && navigator.userAgent.match(/iphone|ipod|ipad/i)) {
        wrap.toggleClass('yaselect-open yaselect-close');
        return jselect /* becomes invisible and is placed above wrapper to receive screen tap -- triggering native <select> */
          .before(anchor)
          .css({opacity: 0.001})
          .change(function(e) { curr.text(gettext()); })
          .appendTo(anchor);
      }
      jselect
        .before(anchor)
        .keydown(function(e) { if (e.which==13||e.which==32) { e.preventDefault(); confirm(true); } })
        .change(function(e) { curr.text(gettext()); })
        .blur(function(e) { jselect.hide(); wrap.toggleClass('yaselect-open yaselect-close'); })
        .click(function(e) {
          if (jselect.is(':hidden')) {
            wrap.toggleClass('yaselect-open yaselect-close');
            jselect.show();
            setTimeout(function() { jselect.focus(); }); /* avoid trampling confusion with triggered blur */
          } else {
            confirm(true);
          }
        })
        .appendTo(anchor);
      wrap
        .mousedown(function(e) { jselect.click(); })
        .keydown(function(e) { if ([13,32,37,38,39,40].indexOf(e.which) != -1) { e.preventDefault(); jselect.click(); } }); /* preventDefault avoid pagescroll */
      select.size = $(this).attr("size")||options.size;
      confirm();
    });
  }
  

})(jQuery);


// Accordion Plugin

/*

Convert ul.accordion > h2 + .content to an accordion list 

*/

(function($){
  
  $.fn.accordion=function(options){
    
    options = options||{};
    
    return this.each(function(){
      
      var $this = $(this),
          $handle= $this.find('h2');      
                    
          $this.find('.content').hide().end().find('.content').eq(0).show();
          $handle.eq(0).addClass("expanded");
          
          $handle.click(function(e){
            e.preventDefault();
            
            var _this = $(this);                        
            
            $handle
              .removeClass("expanded")
              .parent()
              .next()
              .slideUp(400);
              
            _this
              .addClass("expanded")
              .parent()
              .next()
              .slideDown(400);
          });
    });
    
  }
  
})(jQuery);