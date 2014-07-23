/**
 * Created by Ruslan Koloskov
 * Date: 22.07.14
 * Time: 13:29
 */
define(['jquery'], function ($) {
	$('body').on('click', '.popup-form__submit', function(e) {
		e.preventDefault();
		var form = $('#price');
		var formData = form.serializeArray();

		$.ajax({
			url: '/price/getPrice',
			method: 'POST',
			data: formData,
			success: function(data) {
				"use strict";
				if (data) {
					console.warn(data);
					try {
						var result = JSON.parse(data);
						if (result.errors) {
							for (var i = 0, length = result.errors.length; i < length; i++) {
								form.append('<p>' + result.errors[i] + '!' + '</p>');
							}
						}
						else if (result.price) {
							form.empty();
							form.append('<a href="' + result.price + '">Скачать прайс-лист</a>');
						}
					}
					catch (exception) {}
				}
			}
		});
	});
});