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
		var f = $(this);
		var containers = $('span.frame', f);
		
		containers.each(function () {
			var t = $(this);
			var container = t.parent();
			var input = $('input[name^=fields].irrelevant', container);
			var change = $('a.change', container);

			function switchToEdit() {
				input.removeClass('irrelevant');
				input.next().removeClass('irrelevant');
				if ($(this).hasClass('remove')) {
					t.remove();
					input.val('');
				}
			};

			change.click(switchToEdit);
		});
	};

	function init() {
		$(FIELD_CLASS).each(hookOne);
		$(CONTENT_CLASS).each(hookOne);
	};

	$(init);

})(jQuery);