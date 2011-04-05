var Wygwam;


(function($) {


Wygwam = function(id, config, defer) {

	config = (Wygwam.configs[config] || Wygwam.configs['default']);

	var initCKEditor = function(){
		CKEDITOR.replace(id, config);
	};

	if (! defer) {
		initCKEditor();
	}
	else {
		$(document).ready(function(){
			var css = (config.contentsCss ? config.contentsCss : Wygwam.themeUrl+'lib/ckeditor/contents.css'),
				width = (config.width ? config.width.toString() : '100%'),
				height = (config.height ? config.height.toString() : '200');

			if (width.match(/\d$/)) width += 'px';
			if (height.match(/\d$/)) height += 'px';

			var $textarea = $('#'+id).hide(),
				html = $textarea.val(),
				$iframe = $('<iframe class="wygwam" style="width:'+width+'; height:'+height+';" frameborder="0" src="about:blank" />').insertAfter($textarea);

			var populateIframe = function(){

				// get the iframe document
				var doc = ($iframe[0].contentWindow || $iframe[0].contentDocument);
				if (doc.document) doc = doc.document;

				var $ihtml = $('html', doc).css({ margin: 0, padding: 0, height: '100%' }),
					$ibody = $(doc.body);

				$ibody.html(html);

				if ($ibody.html() != html) {
					setTimeout(populateIframe, 100);
				}

				$ihtml.attr('title', 'Click to edit');

				$ihtml.click(function(){
					$iframe.remove();
					initCKEditor();
				});

				$ibody.append($('<link rel="stylesheet" type="text/css" href="'+css+'" />'));
				$ibody.append($('<style type="text/css">* { cursor: pointer !important; }</style>'));
			};

			setTimeout(populateIframe, 100);
		});
	}
};


Wygwam.configs = {};


})(jQuery);
