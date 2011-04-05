var wygwam_addSettingRow,
	wygwam_settingPrefix;


(function($){

/**
 * Switcht
 */
$.fn.ptSwitch = function(){
	return this.each(function(){
		var $select = $(this).hide(),
			offVal = this[0].value,
			offLabel = this[0].text,
			onVal = this[1].value,
			onLabel = this[1].text,
			$ul = $('<ul class="ptSwitch" tabindex="0" />').insertAfter($select),
			$off = $('<li class="'+offVal+'">'+offLabel+'</li>').appendTo($ul),
			$toggle = $('<li class="toggle" />').appendTo($ul),
			$on = $('<li class="'+onVal+'">'+onLabel+'</li>').appendTo($ul),
			selected = $select.val() == onVal;

		// set initial bg position
		$toggle.css({ backgroundPosition: (selected ? 0 : 100) + '% 0' });

		var select = function(){
			selected = true;
			$select.val('y');
			$toggle.stop().animate({
				backgroundPosition: "0% 0"
			}, 'fast');
		};

		var deselect = function(){
			selected = false;
			$select.val('n');
			$toggle.stop().animate({
				backgroundPosition: '100% 0'
			}, 'fast');
		};

		var toggle = function(){
			if (selected) deselect();
			else select();
		};

		$off.click(deselect);
		$on.click(select);

		$toggle.mousedown(function(event){
			var width = $toggle.width(),
				pageX = event.pageX,
				pageY = event.pageY,
				percent = selected ? 100 : 0;

			$(document).bind('mousemove.ptSwitch', function(event){
				percent = (selected ? 100 : 0) + 100 * (event.pageX - pageX) / width;
				if (percent > 100) percent = 100;
				else if (percent < 0) percent = 0;
				$toggle.css('background-position', (100-percent)+'% 0');
			});

			$(document).bind('mouseup.ptSwitch', function(event){
				$(document).unbind('.ptSwitch');

				// just toggle if it was a single click
				if (pageX == event.pageX && pageY == event.pageY) {
					toggle();
				} else {
					if (percent < 50) deselect();
					else select();
				}
			});
		});

		$ul.keydown(function(event){
			switch(event.keyCode) {
				case 32: toggle(); break;
				case 37: deselect(); break;
				case 39: select(); break;
				default: return;
			}

			event.preventDefault();
		});

		$select.focus(function(){
			$ul.focus();
		});
	});
};

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
$.fn.ptSizedParent = function(){
	var $parent = this;
	do $parent = $parent.parent();
	while ($parent.length && !parseInt($parent[0].style.height));
	return $parent;
};

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
		$positionedParent = $this.ptPositionedParent(),
		$sizedParent = $parent.ptSizedParent();

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
	if ($sizedParent.length) {
		$sizedParent.animate({
			height: $sizedParent.height() + (newParentHeight - $parent.height())
		});
	}

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
			x2 = x1 + $element.width(),
			y2 = y1 + $element.height();
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
							$placeholder.remove();
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


$(document).ready(function(){

	// -------------------------------------------
	//  Advanced Settings
	// -------------------------------------------

	var $tbody = $('#ft_wygwam > table > tbody');

	var settings = {
		// autoUpdateElement, baseFloatZIndex
		baseHref: { desc: 'The base href URL used to resolve relative and absolute URLs in the editor content.' },
		// blockedKeystrokes
		bodyClass: { desc: 'Sets the ‘class’ attribute to be used on body if it doesn’t have one.' },
		bodyId: { desc: 'Sets the ‘id’ attribute to be used on body if it doesn’t have one.' },
		// browserContextMenuOnCtrl, colorButton_backStyle
		colorButton_colors: { desc: 'Defines the colors to be displayed in the color selectors. It’s a string containing the hexadecimal notation for HTML colors, without the “#” prefix.', type: 'textarea', val: '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF' },
		colorButton_enableMore: { desc: 'Whether to enable the “More Colors...” button in the color selectors.', type: 'bool' },
		// colorButton_foreStyle
		contentsCss: { desc: 'The CSS file(s) to be used to apply style to the contents. Put each file on a single line.', type: 'textarea', val: 'contents.css' },
		contentsLangDirection: { desc: 'The writting direction of the language used to write the editor contents.', type: 'select', options: { ltr: 'Left-to-right', rtl: 'Right-to-left' }},
		customConfig: { desc: 'The URL path for the custom configuration file to be loaded. If not overloaded with inline configurations, it defaults to the “config.js” file present in the root of the CKEditor installation directory.' },
		// defaultLanguage, dialog_backgroundCoverColor, dialog_backgroundCoverOpacity, dialog_magnetDistance, disableNativeSpellChecker, disableNativeTableHandles
		disableObjectResizing: { desc: 'Disables the ability of resize objects (image and tables) in the editing area.', type: 'bool' },
		docType: { desc: 'Sets the doctype to be used when loading the editor content as HTML.', val: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' },
		editingBlock: { desc: 'Whether to render or not the editing block area in the editor interface.', type: 'bool', val: 'y' },
		emailProtection: { desc: 'The e-mail address anti-spam protection option.', type: 'emailProtection' },
		enterMode: { desc: 'Sets the behavior for the ENTER key. It also dictates other behaviour rules in the editor, like whether the &lt;br&gt; element is to be used as a paragraph separator when indenting text.', type: 'select', options: { 'CKEDITOR.ENTER_P':'&lt;p&gt;', 'CKEDITOR.ENTER_BR':'&lt;br&gt;', 'CKEDITOR.ENTER_DIV':'&lt;div&gt;' }},
		entities: { desc: 'Whether to use HTML entities in the output.', type: 'bool', val: 'y' },
		entities_additional: { desc: 'An additional list of entities to be used. It’s a string containing each entry separated by a comma. Entities names or number must be used, exclusing the “&” preffix and the “;” termination.', val: '#39' },
		entities_greek: { desc: 'Whether to convert some symbols, mathematical symbols, and Greek letters to HTML entities. This may be more relevant for users typing text written in Greek. The list of entities can be found at the <a href="http://www.w3.org/TR/html4/sgml/entities.html#h-24.3.1" target="_blank">W3C HTML 4.01 Specification, section 24.3.1</a>.', type: 'bool', val: 'y' },
		entities_latin: { desc: 'Whether to convert some Latin characters (Latin alphabet No. 1, ISO 8859-1) to HTML entities. The list of entities can be found at the <a href="http://www.w3.org/TR/html4/sgml/entities.html#h-24.2.1" target="_blank">W3C HTML 4.01 Specification, section 24.2.1</a>.', type: 'bool', val: 'y' },
		entities_processNumerical: { desc: 'Whether to convert all remaining characters, not comprised in the ASCII character table, to their relative numeric representation of HTML entity. For example, the phrase “This is Chinese: 汉语.” is outputted as “This is Chinese: &amp;#27721;&amp;#35821;.”', type: 'bool' },
		extraPlugins: { desc: 'Comma-separated list of additional plugins to be loaded.', val: 'MediaEmbed' },
		// find_highlight
		font_defaultLabel: { desc: 'The text to be displayed in the Font combo if none of the available values matches the current cursor position or text selection.' },
		font_names: { desc: 'The list of fonts names to be displayed in the Font combo in the toolbar. Entries are separated by semi-colons (;), while it’s possible to have more than one font for each entry, in the HTML way (separated by comma). A display name may be optionally defined by prefixing the entries with the name and the slash character. For example, “Arial/Arial, Helvetica, sans-serif” will be displayed as “Arial” in the list, but will be outputted as “Arial, Helvetica, sans-serif”.', type: 'textarea' },
		// font_style
		fontSize_defaultLabel: { desc: 'The text to be displayed in the Font Size combo is none of the available values matches the current cursor position or text selection.' },
		fontSize_sizes: { desc: 'The list of fonts size to be displayed in the Font Size combo in the toolbar. Entries are separated by semi-colons (;). Any kind of “CSS like” size can be used, like “12px”, “2.3em”, “130%”, “larger” or “x-small”. A display name may be optionally defined by prefixing the entries with the name and the slash character. For example, “Bigger Font/14px” will be displayed as “Bigger Font” in the list, but will be outputted as “14px”.', type: 'textarea', val: '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px' },
		// fontSize_style
		forcePasteAsPlainText: { desc: 'Whether to force all pasting operations to insert on plain text into the editor, loosing any formatting information possibly available in the source text.', type: 'bool' },
		forceSimpleAmpersand: { desc: 'Whether to force using “&amp;” instead of “&amp;amp;” in elements attributes values. It‘s not recommended to change this setting for compliance with the W3C XHTML 1.0 standards (<a href="http://www.w3.org/TR/xhtml1/#C_12" target="_blank">C.12, XHTML 1.0</a>).', type: 'bool' },
		// * format_address ... format_pre
		format_tags: { desc: 'The list of tags to be displayed in the Format combo in the toolbar.', type: 'checkboxes', options: 'p;h1;h2;h3;h4;h5;h6;pre;address;div'.split(';'), val: 'p;h1;h2;h3;h4;h5;h6;pre;address;div'.split(';') },
		fullPage: { desc: 'Indicates whether the contents to be edited are being inputted as a full HTML page. A full page includes the &lt;html&gt;, &lt;head&gt; and &lt;body&gt; tags. The final output will also reflect this setting, including the &lt;body&gt; contents only if this setting is disabled.', type: 'bool' },
		height: { desc: 'The height of editing area( content ), in relative or absolute, e.g. 30px, 5em. Note: Percentage unit is not supported yet. e.g. 30%.', type: 'number', val: 200 },
		htmlEncodeOutput: { desc: 'Whether escape HTML when editor update original input element.', type: 'bool' },
		ignoreEmptyParagraph: { desc: 'Whether the editor must output an empty value (“”) if it’s contents is made by an empty paragraph only.', type: 'bool', val: 'y' },
		image_removeLinkByEmptyURL: { desc: 'Whether to remove links when emptying the link URL field in the image dialog.', type: 'bool', val: 'y' },
		language: { desc: 'The user interface language localization to use.', type: 'select', options: {af:'Afrikaans',ar:'Arabic',bg:'Bulgarian',bn:'Bengali/Bangla',bs:'Bosnian',ca:'Catalan',cs:'Czech',da:'Danish',de:'German',el:'Greek',en:'English','en-au':'English (Australia)','en-ca':'English (Canadian)','en-uk':'English (United Kingdom)',eo:'Esperanto',es:'Spanish',et:'Estonian',eu:'Basque',fa:'Persian',fi:'Finnish',fo:'Faroese',fr:'French','fr-ca':'French (Canada)',gl:'Galician',gu:'Gujarati',he:'Hebrew',hi:'Hindi',hr:'Croatian',hu:'Hungarian',is:'Icelandic',it:'Italian',ja:'Japanese',km:'Khmer',ko:'Korean',lt:'Lithuanian',lv:'Latvian',mn:'Mongolian',ms:'Malay',nb:'Norwegian Bokmal',nl:'Dutch',no:'Norwegian',pl:'Polish',pt:'Portuguese (Portugal)','pt-br':'Portuguese (Brazil)',ro:'Romanian',ru:'Russian',sk:'Slovak',sl:'Slovenian',sr:'Serbian (Cyrillic)','sr-latn':'Serbian (Latin)',sv:'Swedish',th:'Thai',tr:'Turkish',uk:'Ukrainian',vi:'Vietnamese',zh:'Chinese Traditional','zh-cn':'Chinese Simplified'}, val: 'en' },
		menu_groups: { desc: 'A comma separated list of items group names to be displayed in the context menu. The items order will reflect the order in this list if no priority has been definted in the groups.', val: 'clipboard,form,tablecell,tablecellproperties,tablerow,tablecolumn,table,anchor,link,image,flash,checkbox,radio,textfield,hiddenfield,imagebutton,button,select,textarea' },
		menu_subMenuDelay: { desc: 'The amount of time, in milliseconds, the editor waits before showing submenu options when moving the mouse over options that contains submenus, like the “Cell Properties” entry for tables.', type: 'number', val: 400 },
		newpage_html: { desc: 'The HTML to load in the editor when the “new page” command is executed.', type: 'textarea' },
		// pasteFromWordCleanupFile
		pasteFromWordNumberedHeadingToList: { desc: 'Whether transform MS-Word Outline Numbered Heading into html list.', type: 'bool' },
		pasteFromWordPromptCleanup: { desc: 'Whether prompt the user about the clean-up of content from MS-Word.', type: 'bool' },
		pasteFromWordRemoveFontStyles: { desc: 'Whether the ignore all font-related format styles, including: - font size; - font family; - font fore/background color;', type: 'bool', val: 'y' },
		pasteFromWordRemoveStyles: { desc: 'Whether remove element styles that can’t be managed with editor, note that this this doesn’t handle the font-specific styles, which depends on how pasteFromWordRemoveFontStyles is configured.', type: 'bool', val: 'y' },
		// * protectedSource
		removeFormatAttributes: { desc: 'A comma separated list of elements attributes to be removed when executing the “remove format” command.', val: 'class,style,lang,width,height,align,hspace,valign' },
		removeFormatTags: { desc: 'A comma separated list of elements to be removed when executing the “remove format” command. Note that only inline elements are allowed.', val: 'b,big,code,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var' },
		removePlugins: { desc: 'A comma separated list of plugins that must not be loaded.' },
		resize_enabled: { desc: 'Whether to enable the resizing feature. If disabed the resize handler will not be visible.', type: 'bool', val: 'y' },
		resize_maxHeight: { desc: 'The maximum editor height, in pixels, when resizing it with the resize handle.', type: 'number', val: 3000 },
		resize_maxWidth: { desc: 'The maximum editor width, in pixels, when resizing it with the resize handle.', type: 'number', val: 3000 },
		resize_minHeight: { desc: 'The minimum editor height, in pixels, when resizing it with the resize handle.', type: 'number', val: 250 },
		resize_minWidth: { desc: 'The minimum editor width, in pixels, when resizing it with the resize handle.', type: 'number', val: 750 },
		// * shiftEnterMode
		skin: { desc: 'The skin to load. It may be the name of the skin folder inside the editor installation path, or the name and the path separated by a comma.', val: 'wygwam' },
		// * smiley_descriptions, smiley_images, smiley_path
		startupFocus: { desc: 'Sets whether the editor should have the focus when the page loads.', type: 'bool' },
		startupMode: { desc: 'The mode to load at the editor startup. It depends on the plugins loaded. By default, the “wysiwyg” and “source” modes are available.', val: 'wysiwyg' },
		startupOutlineBlocks: { desc: 'Whether to automaticaly enable the “show block” command when the editor loads.', type: 'bool' },
		stylesCombo_stylesSet: { desc: 'The “styles definition set” to load into the styles combo. The styles may be defined in the page containing the editor, or can be loaded on demand from an external file when opening the styles combo for the fist time. In the second case, if this setting contains only a name, the styles definition file will be loaded from the “styles” folder inside the stylescombo plugin folder. Otherwise, this setting has the “name:url” syntax, making it possible to set the URL from which loading the styles file.', val: 'default' },
		tabIndex: { desc: 'The editor tabindex value.', type: 'number', val: '0' },
		tabSpaces: { desc: 'Intructs the editor to add a number of spaces (&nbsp;) to the text when hitting the TAB key. If set to zero, the TAB key will be used to move the cursor focus to the next element in the page, out of the editor focus.', type: 'number', val: '0' },
		templates: { desc: 'The templates definition set to use. It accepts a list of names separated by comma. It must match definitions loaded with the templates_files setting.', val: 'default' },
		templates_files: { desc: 'The list of templates definition files to load. Put each file on a single line.', type: 'textarea', val: 'plugins/templates/templates/default.js' },
		templates_replaceContent: { desc: 'Whether the “Replace actual contents” checkbox is checked by default in the Templates dialog.', type: 'bool', val: 'y' },
		// theme
		// * toolbar, toolbar_Basic, toolbar_Full
		// toolbarCanCollapse: { desc: 'Whether the toolbar can be collapsed by the user. If disabled, the collapser button will not be displayed.', type: 'bool' },
		toolbarStartupExpanded: { desc: 'Whether the toolbar must start expanded when the editor is loaded.', type: 'bool', val: 'y' },
		undoStackSize: { desc: 'The number of undo steps to be saved. The higher this setting value the more memory is used for it.', type: 'number', val: 20 },
		width: { desc: 'The editor width in CSS size format or pixel integer.', type: 'number' }
	};

	var lastSettingRow;

	wygwam_addSettingRow = function(initialType, initialVal){
		if (lastSettingRow && !$('> td:first > select:first', lastSettingRow).val()) {
			lastSettingRow.remove();
		}

		var $tr = $('<tr />').appendTo($tbody),
			$th = $('<td />').appendTo($tr),
			$td = $('<td />').appendTo($tr),
			$settingSelect = $('<select />').appendTo($th)
				.append('<option value="">Add an advanced setting...</option>'),
			$desc = $('<p />').appendTo($th).hide(),
			selected = false;

		lastSettingRow = $tr;

		// row class
		if ($tr.attr('rowIndex') % 2) {
			$tr.addClass('even');
			$th.addClass('tableCellOne');
			$td.addClass('tableCellOne');
		} else {
			$tr.addClass('odd');
			$th.addClass('tableCellTwo');
			$td.addClass('tableCellTwo');
		}

		for (var i in settings) {
			$('<option>'+i+'</option>').appendTo($settingSelect);
		}

		var showSetting = function(setting, val, focus){
			// remove last item
			$td.html('');

			if (setting) {
				var settingAttr = settings[setting],
					name = wygwam_settingPrefix+'[config]['+setting+']',
					type = settingAttr.type || 'text';

				// description
				$desc.html(settingAttr.desc+' <a href="http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.'+setting+'" target="_blank">More...</a>').show();

				// input
				switch (type) {
					case 'textarea':
						var $input = $('<textarea name="'+name+'" cols="90" rows="5" spellcheck="false" class="fullfield">').appendTo($td).html(val);
						break;

					case 'bool':
						var $input = $('<select name="'+name+'">' +
						                 '<option value="n">No</option>' +
						                 '<option value="y">Yes</option>' +
						               '</select>').appendTo($td);
						$input.val(val == 'y' ? 'y' : 'n');
						$input.ptSwitch();
						break;

					case 'select':
						var $input = $('<select name="'+name+'" />').appendTo($td);
						for (var i in settingAttr.options) {
							$('<option value="'+i+'">'+settingAttr.options[i]+'</option>').appendTo($input);
						}
						if (val) $input.val(val);
						break;

					case 'checkboxes':
						for (var i in settingAttr.options) {
							var option = settingAttr.options[i],
								$label = $('<label style="margin-right: 10px;"> '+option+'</label>').appendTo($td),
								$checkbox = $('<input type="checkbox" name="'+name+'[]" value="'+option+'" />').prependTo($label);
							if (val.indexOf(option) != -1) {
								$checkbox.attr('checked', 'checked');
							}
						}
						break;

					case 'number':
						$input = $('<input type="number" name="'+name+'" class="field ptNumber" />').appendTo($td).val(val);
						break;

					case 'emailProtection':
						var $input = $('<select name="'+name+'">' +
						                 '<option value="">None</option>' +
						                 '<option value="encode">Encode</option>' +
						                 '<option value="custom">Custom...</option>' +
						               '</select>').appendTo($td),
							$custom = $('<input type="text" name="'+name+'" spellcheck="false" class="field" style="margin-left: 10px;" value="myFunction(NAME,DOMAIN,SUBJECT,BODY)" />').appendTo($td);

						if (val == 'encode') {
							$input.val('encode');
						} else if (val) {
							$input.val('custom');
							$custom.val(val);
						}

						$input.change(function(){
							if ($input.val() == 'custom') {
								$custom.css('visibility', 'visible').removeAttr('disabled');
								if (focus) $custom.focus();
							} else {
								$custom.css('visibility', 'hidden').attr('disabled', 'disabled');
							}
						});

						$input.change();
						break;

					default:
						var $input = $('<input type="text" name="'+name+'" spellcheck="false" class="fullfield" />').appendTo($td).val(val);
				}

				if (focus) if ($input) $input.focus();
				else focus = true;

				// add new row?
				if (!selected) {
					selected = true;
					new wygwam_addSettingRow();
				}
			}
			else {
				$desc.html('').hide();
			}
		};

		$settingSelect.change(function(){
			var setting = $settingSelect.val(),
				val = (setting && settings[setting].val) ? settings[setting].val : '';
			showSetting(setting, val, true);
		});

		if (initialType) {
			$settingSelect.val(initialType);
			showSetting(initialType, initialVal);
		}

		return $tr;
	};

	new wygwam_addSettingRow();

});


})(jQuery);
