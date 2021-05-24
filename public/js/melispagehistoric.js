$(function() {
	// cache body
	var $body = $("body"),
		tableId;
	// selectedUser = "";
	// historicBackOfficeUsers = [],

	window.initHistoric = function(data, tblSettings) {
		// add events here if you want to do something when initializing page historic
		// getBackofficeUsers();

		// remove the sort icon in the table head
		$(document).on("init.dt", function(e, settings) {
			var thUserId = $(".tableHistoric thead tr").find("th:nth-child(1)");

			thUserId.removeClass("sorting_asc");
			initDateRangePickerFilter();
		});

		// get the current page ID
		var pageId = $("#" + tblSettings.sTableId).data("pagenumber");

		// pass what page ID to be used when displaying page historic
		data.pageId = pageId;

		var userFilter = $(
			"#id_mcph_search_user_form_" + pageId + " #id_mcph_user_search"
		);
		data.user_name = userFilter.length > 0 ? userFilter.val() : "";

		initSelectedActionData(data);

		/** Get current datepicker >> check for start & end dates >> include dates in AJaX request */
		var pckr = $(
			"#" + tblSettings.sTableId + "_wrapper .melisCmsPageHistoricDatePicker"
		);

		if (pckr.length && pckr.attr("data-is-init") == "false") {
			// datepicker is not in initial state (date should be empty if initial state): a workaround for this kind
			// of set-up where dataTable data is from a javascript function
			pckr = pckr.data("daterangepicker");

			if (pckr.startDate !== null && pckr.endDate !== null) {
				// Set dates
				data.startDate = pckr.startDate.format(melisDateFormat);
				data.endDate = pckr.endDate.format(melisDateFormat);
			}
		}
	};

	//cancel daterange picker
	$body.on(
		"cancel.daterangepicker",
		"#" + activeTabId + " .tableHistoric",
		function(ev, picker) {
			var $this = $(this);

			$this.find('input[name="datefilter"]').val("");
		}
	);

	//refresh table content after selecting an action
	$body.on("change", ".melisCmsPageHistoricSelectAction", function() {
		var $this = $(this);

		tableId = $this
			.closest(".filter-bar")
			.siblings(".bottom")
			.find("table")
			.attr("id");
		$("#" + tableId)
			.DataTable()
			.ajax.reload();
	});

	// Refresh table content when date(range) is selected
	$body.on(
		"apply.daterangepicker",
		".melisCmsPageHistoricDatePicker",
		function() {
			var $this = $(this);
			$this.attr("data-is-init", "false");

			var tableId = $this
				.closest(".filter-bar")
				.siblings(".bottom")
				.find("table")
				.attr("id");
			$("#" + tableId)
				.DataTable()
				.ajax.reload();
		}
	);

	/** User filter event handler */
	$body.on("change", ".mcph-user-search", function() {
		tableId = $(this)
			.closest(".filter-bar")
			.siblings(".bottom")
			.find("table")
			.attr("id");
		$("#" + tableId)
			.DataTable()
			.ajax.reload();
	});

	//get all BO users present in the pagehistoric database
	// window.getBackofficeUsers = function() {
	// 	historicBackOfficeUsers = [];
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "/melis/MelisCmsPageHistoric/PageHistoric/getBackOfficeUsers",
	// 	})
	// 		.done(function(data) {
	// 			$.each(data.users, function(key, value) {
	// 				historicBackOfficeUsers.push(value.fullname);
	// 			});
	// 		})
	// 		.fail(function() {
	// 			alert(translations.tr_meliscore_error_message);
	// 		});
	// };

	//initialize date range picker
	window.initDateRangePickerFilter = function() {
		var sToday = translations.tr_meliscore_datepicker_today,
			sYesterday = translations.tr_meliscore_datepicker_yesterday,
			sLast7Days = translations.tr_meliscore_datepicker_last_7_days,
			sLast30Days = translations.tr_meliscore_datepicker_last_30_days,
			sThisMonth = translations.tr_meliscore_datepicker_this_month,
			sLastMonth = translations.tr_meliscore_datepicker_last_month;

		function callback(start, end) {
			dStartDate = start.format(melisDateFormat);
			dEndDate = end.format(melisDateFormat);

			//default display upon initialization of date picker
			var icon = '<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>';

			if (dStartDate == "") {
				$body
					.find(".melisCmsPageHistoricDatePicker .dt_dateInfo")
					.html(
						translations.tr_meliscore_datepicker_select_date +
							" " +
							icon +
							' <strong class="caret"></strong>'
					);
			} else {
				$body
					.find(".melisCmsPageHistoricDatePicker .dt_dateInfo")
					.html(
						translations.tr_meliscore_datepicker_select_date +
							" " +
							icon +
							" " +
							dStartDate +
							" - " +
							dEndDate +
							' <b class="caret"></b>'
					);
			}
		}

		var rangeStringParam = {};

		rangeStringParam[sToday] = [moment(), moment()];
		rangeStringParam[sYesterday] = [
			moment().subtract(1, "days"),
			moment().subtract(1, "days"),
		];
		rangeStringParam[sLast7Days] = [moment().subtract(6, "days"), moment()];
		rangeStringParam[sLast30Days] = [moment().subtract(29, "days"), moment()];
		rangeStringParam[sThisMonth] = [
			moment().startOf("month"),
			moment().endOf("month"),
		];
		rangeStringParam[sLastMonth] = [
			moment()
				.subtract(1, "month")
				.startOf("month"),
			moment()
				.subtract(1, "month")
				.endOf("month"),
		];

		$body.find(".melisCmsPageHistoricDatePicker").daterangepicker(
			{
				locale: {
					format: melisDateFormat,
					applyLabel: translations.tr_meliscore_datepicker_apply,
					cancelLabel: translations.tr_meliscore_datepicker_cancel,
					customRangeLabel: translations.tr_meliscore_datepicker_custom_range,
				},
				ranges: rangeStringParam,
			},
			callback
		);
	};

	//this will get the value of the select and add it into the data so it will be passed in the backend
	window.initSelectedActionData = function(data) {
		var actionField = $("#" + tableId)
			.closest(".bottom")
			.siblings(".filter-bar")
			.find(".melisCmsPageHistoricSelectAction");

		if (actionField.length && actionField.val() != "") {
			data.action = $("#" + tableId)
				.closest(".bottom")
				.siblings(".filter-bar")
				.find(".melisCmsPageHistoricSelectAction")
				.val();
		}
	};

	//open historic event
	$body.on("click", ".melis-openrecenthistoric", function() {
		var $this = $(this),
			data = $this.data();

		//OPEN HISTORIC FROM DASHBOARD WIDGET
		melisHelper.tabOpen(
			data.pageTitle,
			data.pageIcon,
			data.zoneId,
			data.melisKey,
			{ idPage: data.pageId }
		);
	});
});
