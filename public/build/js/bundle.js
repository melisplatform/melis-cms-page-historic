$(function(){
	// cache body
	var $body = $("body");
    var tableId;
    var historicDateFilterStart = "";
    var historicDateFilterEnd = "";
    var historicBackOfficeUsers = [];
    var selectedUser = "";
	
	window.initHistoric = function(data, tblSettings) {
		// add events here if you want to do something when initializing page historic
        getBackofficeUsers();
		
		// remove the sort icon in the table head
		$(document).on("init.dt", function(e, settings) {
			var thUserId = $(".tableHistoric thead tr").find("th:nth-child(1)");
			thUserId.removeClass("sorting_asc");
            initSearchUserAutoCompleteInput();
            initDateRangePickerFilter();
		});
		
		// get the current page ID
		var pageId = $("#" + tblSettings.sTableId ).data("pagenumber");
		
		// pass what page ID to be used when displaying page historic
		data.pageId = pageId;

		data.user_name = selectedUser;
        initSelectedActionData(data);

        if (historicDateFilterStart != "") {
            data.startDate = historicDateFilterStart;
        }

        if (historicDateFilterEnd != "") {
            data.endDate = historicDateFilterEnd;
        }
	};

	//cancel daterange picker
    $body.on('cancel.daterangepicker', '#'+activeTabId+' .tableHistoric', function(ev, picker) {
        $(this).find('input[name="datefilter"]').val('');
    })

    //refresh table content after selecting an action
    $("body").on('change', '.melisCmsPageHistoricSelectAction',function(){
        tableId = $(this).closest('.filter-bar').siblings('.bottom').find('table').attr('id');
        $("#"+tableId).DataTable().ajax.reload();
    });

    //refresh table content when date is selecterd
    $("body").on('apply.daterangepicker', ".melisCmsPageHistoricDatePicker", function(){
        tableId = $(this).closest('.filter-bar').siblings('.bottom').find('table').attr('id');
        $("#"+tableId).DataTable().ajax.reload();
    });

    //initialize autocomplete input for BO users
    window.initSearchUserAutoCompleteInput = function(){
        $("body").find('.melisCmsPageHistoricSearchUserText').autocomplete({
            source: historicBackOfficeUsers,
            select: function (event, ui) {
                //on select get the value
                selectedUser = ui.item.value;
                tableId = $(this).closest('.filter-bar').siblings('.bottom').find('table').attr('id');
                $("#"+tableId).DataTable().ajax.reload();
            }
        });
    }

    //get all BO users present in the pagehistoric database
    window.getBackofficeUsers = function (){
        historicBackOfficeUsers = [];

        $.ajax({
            type        : 'POST',
            url         : '/melis/MelisCmsPageHistoric/PageHistoric/getBackOfficeUsers',
        }).done(function(data){
            $.each(data.users, function(key, value) {
                historicBackOfficeUsers.push(value.fullname);
            });
        });
    }

    //initialize date range picker
    window.initDateRangePickerFilter = function() {
        historicDateFilterStart = "";
        historicDateFilterEnd = "";

        var sToday = translations.tr_meliscore_datepicker_today;
        var sYesterday = translations.tr_meliscore_datepicker_yesterday;
        var sLast7Days = translations.tr_meliscore_datepicker_last_7_days;
        var sLast30Days = translations.tr_meliscore_datepicker_last_30_days;
        var sThisMonth = translations.tr_meliscore_datepicker_this_month;
        var sLastMonth = translations.tr_meliscore_datepicker_last_month;

        function callback(start, end) {
            dStartDate = start.format(melisDateFormat);
            dEndDate   = end.format(melisDateFormat);


            //default display upon initialization of date picker
            var icon = '<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>';

            if (dStartDate == "") {
                $("body").find(".melisCmsPageHistoricDatePicker .dt_dateInfo").html(translations.tr_meliscore_datepicker_select_date + ' ' + icon + ' <b class="caret"></b>');

            } else {
                $("body").find(".melisCmsPageHistoricDatePicker .dt_dateInfo").html(translations.tr_meliscore_datepicker_select_date + ' ' + icon + ' ' + dStartDate + ' - ' + dEndDate + ' <b class="caret"></b>');
            }

            historicDateFilterStart = dStartDate;
            historicDateFilterEnd = dEndDate;

            //$("#"+tableId).DataTable().ajax.reload();
        }
        var rangeStringParam = {};
        rangeStringParam[sToday] = [moment(), moment()];
        rangeStringParam[sYesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
        rangeStringParam[sLast7Days] = [moment().subtract(6, 'days'), moment()];
        rangeStringParam[sLast30Days] = [moment().subtract(29, 'days'), moment()];
        rangeStringParam[sThisMonth] = [moment().startOf('month'), moment().endOf('month')];
        rangeStringParam[sLastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

        $("body").find(".melisCmsPageHistoricDatePicker").daterangepicker({
            locale : {
                format: melisDateFormat,
                applyLabel: translations.tr_meliscore_datepicker_apply,
                cancelLabel: translations.tr_meliscore_datepicker_cancel,
                customRangeLabel: translations.tr_meliscore_datepicker_custom_range,
            },
            ranges: rangeStringParam,
        }, callback);
    }

    //this will get the value of the select and add it into the data so it will be passed in the backend
    window.initSelectedActionData = function(data){
        var actionField = $('#'+tableId).closest('.bottom').siblings('.filter-bar').find('.melisCmsPageHistoricSelectAction');

        if(actionField.length && actionField.val() != ""){
            data.action = $('#'+tableId).closest('.bottom').siblings('.filter-bar').find('.melisCmsPageHistoricSelectAction').val();
        }
    }
	
	//open historic event
	$body.on("click", '.melis-openrecenthistoric', function(){
	    var data = $(this).data();
	    //OPEN HISTORIC FROM DASHBOARD WIDGET
	    melisHelper.tabOpen( data.pageTitle, data.pageIcon, data.zoneId, data.melisKey,  { idPage: data.pageId } );
	});
});