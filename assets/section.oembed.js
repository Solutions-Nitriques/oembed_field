/*
	Copyight: Solutions Nitriques 2011
	License: MIT, see the LICENCE file
*/

/**
 * Client code for managing the oEmbed Field on the
 * blueprint page.
 * 
 * @author nicolasbrassard
 */
(function ($, undefined) {
	
	var FIELD = 'oembed-params-sets',
		FIELD_CLASS = '.' + FIELD;
	
	function hookOne() {
		// create a local scope
		var t = $(this);
	};
	
	function init() {
		$(FIELD_CLASS).each(hookOne);
	};
	
	$(init);
	
})(jQuery);