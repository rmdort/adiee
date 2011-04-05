/*
	Site:		NLBA
	File:		/js/script.js
	Author:		vinay@artminister.com
	Version:  26-01-2010
	
*/

var NlbAcademy = {
  loader:'<img src="/images/site/loader.gif" alt="Loading">',
  
  init:function(){
    
    var cc=this;
    // Initialize SlideShow

    if($('#NlbaHero').length) $('#NlbaHero').slideshow({
      autoplay:false,
      pager:'.nlbaHeroNav'
    });
    
    // Initialize Testimonials Slideshow
    
    var perslide=1;
    if($('body').hasClass('home')) perslide=2;
    
    if($('.testimonials').length) $('.testimonials .content').slideshow({
      autoplay:false,
      pagerappend:true,
      slide:true,
      perslide:perslide
    });
    
    // Invoke Accordion
    
    if(jQuery('.accordion').length) $('.accordion').accordion();
    
    // Print Page 
    
    jQuery('#PrintPage').click(function(e){
      window.print();
      e.preventDefault();
    });
    
    // External Links
    
    $('a[rel^=external]').click(function(e){
      window.open(this.href)
      e.preventDefault();
    });
    
    // Init Facebox
    
    jQuery('a[rel*=facebox]').live("mousedown", function() { 
      $(this).unbind('click');
        $(this).facebox();
    });    
    
    // Invoke Yaselect
    jQuery('#SelectLevel')
    .yaselect({
      size:4
    })
    .change(function(){
      cc.getcourses();
    });
    
    jQuery('#SearchForm select').yaselect();
    
    // Get Courses on Page Load    
    if(jQuery('#CourseFinder').length) cc.getcourses();
    
    // Course Finder - Graceful degradation of submit button
    jQuery('#CourseFinder').find('input').hide();    
    
    // Search Blur
    
    jQuery('#keywords').each(function(){
    	jQuery(this).focus(function() {
    	if( this.value == this.defaultValue ) {
    		this.value = "";
    	}
    	}).blur(function() {
    		if( !this.value.length ) {
    			this.value = this.defaultValue;
    		}
    	});
    });
    
    // Empty Facebox html for IE. Bug fix for : Flash continues to play even after facebox is closed
    if(jQuery.browser.msie){
      $(document).bind('close.facebox', function() { jQuery('#facebox .content').html("");});
    }
    
    // Gallery 
    jQuery('.thumbs li:first').addClass("active");
    jQuery('.thumbs a').click(function(e){
      var $this = $(this);
      $this.parent().addClass("active").siblings().removeClass("active");
      $('#Gallery img').attr("src",this.href);
      $('#Gallery span').text($this.find('img').attr("alt"));
      e.preventDefault();
    });

  },
  getcourses:function(){
    var $coursefinder = jQuery('#CourseFinder'),
        _action = $coursefinder.attr("action"),
        $results = $coursefinder.find('.courseresults').find('ul');
        
    // Show Loader        
    $results.html('<li>'+this.loader+'<li>');
    //log($coursefinder.offset().top);
    jQuery.post(_action,$coursefinder.serialize(),function(data){
      $results.html(data);            
    });    
          
  }
    
  
};


$(window).load(function(){
  
  // Init
  NlbAcademy.init();
    
  
});