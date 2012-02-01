(function( $ ){
	$.fn.defaultText = function( options ) {

		var settings = $.extend( {
			'fade_class': 'fade_text',
			'default_text_attr': 'data-default_text'
		}, options);

		return this.each(function() {
			var form_field = '';

			$(this).val($(this).attr(settings.default_text_attr));

			$(this).on('focus', function(event) {
				form_field = $(event.target);

				if (form_field.val() == form_field.attr(settings.default_text_attr)) {
					form_field.val('').removeClass(settings.fade_class);
				}
			});

			$(this).on('blur', function(event) {
				form_field = $(event.target);

				if (form_field.val() == '') {
					form_field.val(form_field.attr(settings.default_text_attr)).addClass(settings.fade_class);
				}
			})
		});

	};
})( jQuery );