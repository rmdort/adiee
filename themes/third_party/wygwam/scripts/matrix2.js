(function($) {


Wygwam.matrixColConfigs = {};


/**
 * Display
 */
var onDisplay = function(cell){
	
	var $textarea = $('textarea', cell.dom.$td),
		config = Wygwam.matrixColConfigs[cell.col.id],
		id = cell.field.id+'_'+cell.row.id+'_'+cell.col.id+'_'+Math.floor(Math.random()*100000000);

	$textarea.attr('id', id);

	new Wygwam(id, config[0], config[1]);
};

Matrix.bind('wygwam', 'display', onDisplay);

/**
 * Before Sort
 */
Matrix.bind('wygwam', 'beforeSort', function(cell){
	var $textarea = $('textarea', cell.dom.$td),
		html = $('iframe:first', cell.dom.$td)[0].contentDocument.body.innerHTML;
	$textarea.val(html);
});

/**
 * After Sort
 */
Matrix.bind('wygwam', 'afterSort', function(cell) {
	$textarea = $('textarea', cell.dom.$td);
	cell.dom.$td.empty().append($textarea);
	onDisplay(cell);
});


})(jQuery);
