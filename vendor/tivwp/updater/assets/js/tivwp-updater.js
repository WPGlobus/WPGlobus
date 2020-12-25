jQuery(function ($) {
	$('.tivwp-updater-action-button').on("click", function () {
		// noinspection ES6ConvertVarToLetConst
		var dataPlugin = $(this).data('tivwp-updater-plugin');
		$(this).css({cursor: "wait", opacity: ".3"});
		$('input[type="checkbox"]').prop("checked", false);
		$('tr[data-plugin="' + dataPlugin + '"]').find('input[type="checkbox"]').prop("checked", true);
	})
});
