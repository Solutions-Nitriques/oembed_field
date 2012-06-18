/*
	Copyight: Solutions Nitriques 2011
	License: MIT, see the LICENCE file
*/

/**
 * Client code for managing the oEmbed Field on the
 * publish page of sections
 *
 * @author nicolasbrassard
 */
(function ($, undefined) {

	var FIELD = 'field-oembed',
		FIELD_CLASS = '.' + FIELD,
		CONTENT = 'content-type-oembed',
		CONTENT_CLASS = '.' + CONTENT;

	function hookOne() {
		// create a local scope
		var f = $(this),
			container = $('span.frame', f),
			input = $('input[name^=fields].irrelevant', f),
			change = $('a.change', container);

		function switchToEdit() {
			input.removeClass('irrelevant');
			input.next().removeClass('irrelevant');
			if ($(this).hasClass('remove')) {
				container.remove();
				input.val('');
			}
		};

		change.click(switchToEdit);
	};

	function init() {
		$(FIELD_CLASS).each(hookOne);
		$(CONTENT_CLASS).each(hookOne);
	};

	$(init);

})(jQuery);