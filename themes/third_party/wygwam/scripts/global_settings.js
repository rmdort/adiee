(function($) {

/**
 * Capture single-clicks
 * 
 * @param mixed   callback   the callback function, or "destroy" to unbind the event
 */
$.fn.ptSingleClick = function(callback){
	if (typeof callback == 'function') {
		return this.each(function(){
			var $this = $(this),
				mousedownEvent;

			$this.bind('mousedown.ptSingleClick', function(event){
				mousedownEvent = event;
			});
			$this.bind('mouseup.ptSingleClick', function(event){
				if (!mousedownEvent) return;
				if (mousedownEvent.pageX == event.pageX && mousedownEvent.pageY == event.pageY) {
					callback.call(this, event);
				}
				mousedownEvent = null;
			});
		});
	}
	if (callback == 'destroy') {
		return this.unbind('.ptSingleClick');
	}
};

/**
 * Position element relative to anothor
 * 
 * @param element   $other   the other jQuery element
 */
$.fn.ptPositionRelativeTo = function($other){
	var thisOffset = this.offset(),
		otherOffset = $other.offset();

	return this.css({
		left: thisOffset.left - otherOffset.left,
		top: thisOffset.top - otherOffset.top
	});
};

/**
 * Find the closest absolutely- or relatively-positioned parent
 */
$.fn.ptPositionedParent = function(){
	var $parent = this;
	do $parent = $parent.parent();
	while ($parent.css('position') == 'static');
	return $parent;
};

/**
 * Find the closest parent with width or height properties set
 */
//$.fn.ptSizedParent = function(){
//	var $parent = this;
//	do $parent = $parent.parent();
//	while ($parent.length && !parseInt($parent[0].style.height));
//	return $parent;
//};

/**
 * Animate an element, as well as the x/y coordinates of its siblings,
 * and the left/top positions of its parent
 * 
 * @param object     params     the CSS properties to be animated
 * @param mixed      duration   duration of the animation
 * @param function   callback   function to be called at the end of the animation
 * @see   http://docs.jquery.com/Effects/animate
 */
$.fn.ptAnimate = function(params, duration, callback){
	var $this = this,
		$siblings = $this.nextAll(),
		$parent = $this.parent(),
		$positionedParent = $this.ptPositionedParent();
		//$sizedParent = $parent.ptSizedParent();

	// get the original css props
	var oldParams = {};
	for (var param in params) {
		oldParams[param] = $this.css(param);
	}

	// get the original sibling offsets
	var oldSiblingOffsets = [];
	$siblings.each(function(index){
		oldSiblingOffsets[index] = $(this).offset();
	});

	// temporarily apply the new css props
	$this.css(arguments[0]);

	// get the new parent dimensions
	var newParentWidth = $parent.width(),
		newParentHeight = $parent.height();

	// get the new sibling offsets
	var newSiblingOffsets = [];
	$siblings.each(function(index){
		newSiblingOffsets[index] = $(this).offset();
	});

	// revert to original css props
	$this.css(oldParams);

	// animate the sized parent, if there is one
	//if ($sizedParent.length) {
	//	$sizedParent.animate({
	//		height: $sizedParent.height() + (newParentHeight - $parent.height())
	//	});
	//}

	// animate the parent's dimensions
	$parent.animate({
		width: newParentWidth,
		height: newParentHeight
	}, duration);

	// animate() automatically sets overflow: hidden (huh?)
	$parent.css({ overflow: 'visible' });

	// animate the siblings' positions
	var parentOffset = $positionedParent.offset();
	$siblings.each(function(index){
		var $sibling = $(this);

		// first position the element absolutely
		$sibling.css({
			position: 'absolute',
			left: oldSiblingOffsets[index].left - parentOffset.left,
			top: oldSiblingOffsets[index].top - parentOffset.top
		});

		// animate it into place
		$sibling.animate({
			left: newSiblingOffsets[index].left - parentOffset.left,
			top: newSiblingOffsets[index].top - parentOffset.top
		}, duration);
	});

	// finally run the main animation
	$this.animate.call($this, params, duration, function(){
		// remove the parent's height and width props
		$parent.css({ width: 'auto', height: 'auto' });

		// make siblings relatively positioned again
		$siblings.css({ position: 'relative', left: 0, top: 0 });

		// call the original callback function
		if (typeof callback == 'function') {
			callback.apply(this, arguments);
		}
	});

	return $this;
};

/**
 * Drop Panes
 * 
 * @param object   settings   $.fn.ptDropPanes.defaults overrides
 */
$.fn.ptDropPanes = function(settings){

	// merge default settings with overrides
	var settings = $.extend({}, $.fn.ptDropPanes.defaults, settings);

	function isCursorOver(event, $element){
		var offset = $element.offset(),
			x1 = offset.left,
			y1 = offset.top,
			x2 = x1 + $element.width() + parseInt($element.css('padding-left')) + parseInt($element.css('padding-right')),
			y2 = y1 + $element.height() + parseInt($element.css('padding-top')) + parseInt($element.css('padding-bottom'));
		return (event.pageX >= x1 && event.pageX < x2 && event.pageY >= y1 && event.pageY < y2);
	}

	function getClosestElement(event, $elements){
		var closestElement, closestXDist, closestYDist;
		$elements.each( function(){
			var $element = $(this),
				offset = $element.offset(),
				xDist = Math.abs(offset.left - event.pageX),
				yDist = Math.abs(offset.top - event.pageY);

			if (!closestElement || (yDist < closestYDist) || (yDist == closestYDist && xDist < closestXDist)) {
				closestElement = this;
				closestXDist = xDist;
				closestYDist = yDist;
			}
		});
		return closestElement;
	}


	return this.each( function(){

		var $this = $(this),
			$optionsContainer = $(settings.optionsContainer, $this),
			$selectionsContainer = $(settings.selectionsContainer, $this);

		var originalMargin,
			$selections,
			closestSelection,
			$insertion = $('<div class="dp-insertion" />');

		// add new selection at the end for last insertion point
		$('<span class="dp-option" />').appendTo($selectionsContainer);

		function onLeaveSelections(){
			if (closestSelection) {
				closestSelection = null;
				$insertion.remove();
			}
			$selectionsContainer.removeClass('dp-hover');
			redrawContainerIfSafari();
		}

		function redrawContainerIfSafari(){
			if ($.browser.safari) $this.css('opacity', ($this.css('opacity') == 1 ? .999 : 1));
		}

		var dragOptions = {
			opacity: .5,
			start: function(event, ui){

				// save the original margin for drag stop
				originalMargin = ui.helper.css('marginRight');
				var negMargin = -ui.helper.width();

				if (ui.helper.hasClass('dp-duplicate') && !ui.helper.hasClass('dp-selected')) {
					ui.helper.clone().css('opacity', 1).insertAfter(ui.helper).draggable(dragOptions);
					ui.helper.css('marginRight', negMargin);
				} else {
					ui.helper.ptAnimate({
						marginRight: negMargin
					}, function(){
						redrawContainerIfSafari();
					});
				}

				ui.helper.addClass('dp-dragging');

				// get the latest list of selections
				$selections = $(settings.options, $selectionsContainer).not(ui.helper);

				// callback
				if (typeof settings.onDragStart == 'function') {
					settings.onDragStart(ui.helper);
				}
			},
			stop: function(event, ui){
				var animateOptions = { marginRight: originalMargin, top: 0, left: 0 };
				var animateCallback = function(){
					redrawContainerIfSafari();
				};

				// selected?
				if ($selectionsContainer.hasClass('dp-hover')) {
					// new selection?
					if (!ui.helper.hasClass('dp-selected')) {
						ui.helper.addClass('dp-selected');

						// hold the option's position with a placeholder
						if (!ui.helper.hasClass('dp-duplicate')) {
							$('<span />').attr('id', ui.helper.attr('id')+'-placeholder').addClass('dp-placeholder').insertAfter(ui.helper);
						}

						// enable inputs
						$('*[name]', ui.helper).removeAttr('disabled');

						// callback
						if (typeof settings.onSelect == 'function') {
							settings.onSelect(ui.helper);
						}
					}

					// replace insertion with option
					ui.helper.ptPositionRelativeTo($insertion);
					$insertion.replaceWith(ui.helper);

				} else {

					if (ui.helper.hasClass('dp-duplicate')) {
						var $placeholder = $('.'+ui.helper.attr('class').split(' ')[0], $optionsContainer).not(ui.helper);

						// place hepler in options pane
						if (ui.helper.hasClass('dp-selected')) {
							ui.helper.removeClass('dp-selected');
							ui.helper.ptPositionRelativeTo($placeholder).insertBefore($placeholder);
						}

						delete animateOptions.marginRight;
						animateCallback = function(){
							ui.helper.remove();
						};
					} else {
						// previously selected?
						if (ui.helper.hasClass('dp-selected')) {
							ui.helper.removeClass('dp-selected');

							// replace placeholder with option
							var $placeholder = $('#'+ui.helper.attr('id')+'-placeholder');
							ui.helper.ptPositionRelativeTo($placeholder);
							$placeholder.replaceWith(ui.helper);

							// disable inputs
							$('*[name]', ui.helper).attr('disabled', true);

							// callback
							if (typeof settings.onDeselect == 'function') {
								settings.onDeselect(ui.helper);
							}
						}
					}
				}

				// slide option into place
				ui.helper.ptAnimate(animateOptions, animateCallback);

				ui.helper.removeClass('dp-dragging');
				onLeaveSelections();
				redrawContainerIfSafari();

				// callback
				if (typeof settings.onDragStop == 'function') {
					settings.onDragStop(ui.helper);
				}
			},
			drag: function(event, ui){
				// cursor over selections?
				var cursorOverSelections = isCursorOver(event, $selectionsContainer);
				if (cursorOverSelections && !$selectionsContainer.hasClass('dp-hover')) {
					$selectionsContainer.addClass('dp-hover');
				}
				else if (!cursorOverSelections && $selectionsContainer.hasClass('dp-hover')) {
					$selectionsContainer.removeClass('dp-hover');
					onLeaveSelections();
				}

				if (cursorOverSelections) {
					// find and place the insertion point
					var _closestSelection = getClosestElement(event, $selections);
					if (_closestSelection != closestSelection) {
						closestSelection = _closestSelection;
						$insertion.insertBefore(closestSelection);
						redrawContainerIfSafari();
					}
				}

				// cursor over options?
				var cursorOverOptions = (!cursorOverSelections && isCursorOver(event, $optionsContainer));
				if (cursorOverOptions && !$optionsContainer.hasClass('dp-hover')) {
					$optionsContainer.addClass('dp-hover');
				}
				else if (!cursorOverOptions && $optionsContainer.hasClass('dp-hover')) {
					$optionsContainer.removeClass('dp-hover');
				}

				redrawContainerIfSafari();
			}
		};

		// setup draggables
		$('.dp-option', $this).draggable(dragOptions);

		// callback for initially selected items
		if (typeof settings.onSelect == 'function') {
			$(settings.options, $selectionsContainer).each( function(){
				settings.onSelect($(this));
			})
		}

	});
};

/**
 * Drop Panes default settings
 * @var object
 */
$.fn.ptDropPanes.defaults = {
	optionsContainer: '.dp-options',
	selectionsContainer: '.dp-selections',
	options: '.dp-option'
};





var $settings = $('#ft_wygwam_settings');

function redrawSettingsIfSafari() {
	if ($.browser.safari) $settings.css('opacity', ($settings.css('opacity') == 1 ? .999 : 1));
}


$.fn.wygnav = function(options) {
	return this.each( function() {
		var $this = $(this),
			$list = $this.find('ul'),
			activeItem,
			activeBlock,
			newPaneIndex = 0;

		$this.options = options || {};
		$this.itemNames = [];

		$this.selectItem = function($item, id) {
			if ($item == activeItem) return;
			var prevActiveItem = activeItem, prevActiveBlock = activeBlock;

			activeItem = $item;
			activeBlock = $('#'+id);
			activeItem.addClass('active');
			activeBlock.show();

			if (prevActiveItem) {
				prevActiveItem.removeClass('active');
				prevActiveBlock.hide();
			}
			redrawSettingsIfSafari();
		}

		$('a', $this).wygnavitem($this);
		$('a:first', $this).click();

		// + - buttons
		if ($this.options.add) {
			$this.options.add.click( function() {
				newPaneIndex++;
				id = $this.options.cloner.attr('id')+newPaneIndex;
				$this.options.cloner.clone().attr('id', id).appendTo($this.options.appendTo);
				$li = $('<li><a href="#'+id+'"></a></li>').appendTo($list).find('a').wygnavitem($this).click().dblclick();
				redrawSettingsIfSafari();
			});
			$this.options.remove.click( function() {
				if ($('a', $this).length > 1) {
					activeItem.parent('li').remove();
					activeItem = null;
					activeBlock.remove();
					$('a:first', $this).click();
					redrawSettingsIfSafari();
				}
			});
		}
	});
};


$.fn.wygnavitem = function($wygnav) {
	return this.each( function() {
		var $this = $(this),
			href = $this.attr('href'),
			id = href.substring(href.indexOf('#')+1),
			$input,
			$pane = $('#'+id);

		function applyLabel(val) {
			// remove record of previous
			if ($this.name) {
				var index = $.inArray($this.name, $wygnav.itemNames);
				if (index != -1) {
					$wygnav.itemNames.splice(index, 1);
				}
			}
			var label = '', name = '';
			for (var i=0; i<val.length; i++) {
				var char = val.charAt(i);
				if (/[\w ]/.test(char)) {
					label += char;
					name += char == ' ' ? '_' : char;
				}
			}
			// ensure it's not empty
			if (!name) {
				label = 'Untitled';
				name = 'Untitled';
			}
			// ensure it's unique
			var count = 0;
			while($.inArray(name+(count ? '_'+count : ''), $wygnav.itemNames) != -1) {
				count ++;
			}
			// apply
			$this.html(label+(count ? ' '+count : ''));
			$this.name = name+(count ? '_'+count : '');
			$wygnav.itemNames.push($this.name);
		}

		applyLabel($this.html());
		$this.removeAttr('href');
		if ($wygnav.options.initializePane) {
			$wygnav.options.initializePane($pane);
		}

		function edit() {
			var val = $this.html();
			if (val.substring(0, 8) == 'Untitled') val = '';
			$input = $('<input type="text">').val(val);
			$this.replaceWith($input);
			$input.focus()
				.keydown( function(e) {
					switch (e.which) {
						case 13: // return
							save();
							return false;
						case 27: // esc
							replaceInput();
					}
				})
				.blur(save);
		}

		function save() {
			var val = $input.val();
			replaceInput();
			applyLabel(val);
			$wygnav.options.onSave($pane, $this.name);
		}

		function replaceInput() {
			$input.replaceWith($this);
			redrawSettingsIfSafari();
			addEvents();
		}

		function addEvents() {
			$this
				.click( function() {
					$wygnav.selectItem($this, id);
				})
				.dblclick(edit);
		}
		addEvents();
	});
};


$.fn.sglclickable = function(callback) {
	return this.each( function() {
		var e1;

		$(this)
			.bind('mousedown.sglclickable', function(e) {
				e1 = e;
			})
			.bind('mouseup.sglclickable', function(e) {
				if (!e1) return;
				if (Math.abs(e1.pageX-e.pageX) < 2 && Math.abs(e1.pageY-e.pageY) < 2) {
					$(this).trigger('sglclick');
				}
				e1 = null;
			});
	});
};


$('#wygnav', $settings).wygnav();


// Toolbars
$('#wyg-toolbars .wygnav').wygnav({
	add: $('#wyg-toolbars .wygbuttons .add'),
	remove: $('#wyg-toolbars .wygbuttons .remove'),
	cloner: $('#wyg-tb-untitled'),
	appendTo: $('#wyg-toolbars'),
	initializePane: function($pane) {
		// ensure IDs are unique
		var rand = Math.floor(Math.random()*1000000000);
		$('*[id]', $pane).each( function() {
			this.id = rand+'-'+this.id;
		});

		// initialize droppanes
		$pane.ptDropPanes({
			onSelect: function(toolgroup) {
				var buttons = $('.cke_button', toolgroup);
				if (buttons.length > 1) {
					buttons.sglclickable().bind('sglclick', function() {
						var button = $(this);
						if (button.hasClass('disabled')) {
							button.removeClass('disabled')
							$('*[name]', button).removeAttr('disabled');
						} else {
							button.addClass('disabled')
							$('*[name]', button).attr('disabled', true);
						}
						redrawSettingsIfSafari();
					});
				}
			},
			onDeselect: function(toolgroup) {
				$('.cke_button', toolgroup).unbind('.sglclickable sglclick').filter('.disabled').removeClass('disabled');
			}
		});
	},
	onSave: function($pane, name) {
		$('*[name]', $pane).attr('name', 'ftype[wygwam][toolbars]['+name+'][]');
	}
});


})(jQuery);
