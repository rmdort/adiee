/*
 * --------------------------------------------------------------------
 *  EDITOR CONFIGURATION
 * --------------------------------------------------------------------
 *
 * Create default configuration settings, to be used by all Wygwam fields.
 * See http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
 *
 */
CKEDITOR.editorConfig = function( config )
{
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
};

/*
 * --------------------------------------------------------------------
 *  OUTPUT FORMATTING
 * --------------------------------------------------------------------
 *
 * You can customize how CKEditor formats your HTML markup by setting
 * custom writer rules. Just uncomment the CKEDITOR.on() block below,
 * and modify the values for intent, breakBeforeOpen, etc.
 * See http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Output_Formatting
 *
 */
//	CKEDITOR.on( 'instanceReady', function( ev )
//	{
//		var blockTags = ['div','h1','h2','h3','h4','h5','h6','p','pre'];
//	
//		for (var i = 0; i < blockTags.length; i++)
//		{
//			ev.editor.dataProcessor.writer.setRules( blockTags[i], {
//				indent : true,
//				breakBeforeOpen : true,
//				breakAfterOpen : true,
//				breakBeforeClose : false,
//				breakAfterClose : true
//			});
//		}
//	});
