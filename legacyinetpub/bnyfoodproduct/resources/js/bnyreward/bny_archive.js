(function (window, document, $) {
	'use strict';

	function pad(n) {
		return n < 10 ? '0' + n : '' + n;
	}

	function toYmd(date) {
		return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
	}

	function findPeriodByDate(periodItems, ymd) {
		var i;
		for (i = 0; i < periodItems.length; i++) {
			if (ymd >= periodItems[i].start_date && ymd <= periodItems[i].end_date) {
				return periodItems[i];
			}
		}
		return null;
	}

	$(function () {
		if (typeof bnyArchiveCalendarConfig === 'undefined' || !bnyArchiveCalendarConfig) {
			return;
		}

		var periodItems = $.isArray(bnyArchiveCalendarConfig.periodItems) ? bnyArchiveCalendarConfig.periodItems : [];
		var $dateInput = $('#selected_date');
		var $rewardInput = $('#reward_id');
		var $form = $('#bnyArchiveForm');

		if (!$dateInput.length || !$rewardInput.length || !$form.length || !periodItems.length) {
			return;
		}

		function syncSelectionFromDate(dateObj) {
			if (!dateObj) {
				$rewardInput.val('');
				return null;
			}

			var ymd = toYmd(dateObj);
			var period = findPeriodByDate(periodItems, ymd);
			if (period) {
				$rewardInput.val(period.reward_id);
				return period;
			}

			$rewardInput.val('');
			return null;
		}

		$dateInput.datepicker({
			format: 'dd/mm/yyyy',
			autoclose: true,
			todayHighlight: true,
			language: 'th'
		}).on('changeDate', function (e) {
			syncSelectionFromDate(e.date);
		});

		if (bnyArchiveCalendarConfig.selectedDate) {
			$dateInput.datepicker('setDate', bnyArchiveCalendarConfig.selectedDate);
			syncSelectionFromDate($dateInput.datepicker('getDate'));
		}

		$form.on('submit', function (e) {
			var currentDate = $dateInput.datepicker('getDate');
			if (!currentDate) {
				e.preventDefault();
				return;
			}
			syncSelectionFromDate(currentDate);
		});
	});
})(window, document, jQuery);
