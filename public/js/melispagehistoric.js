// cache body
var $body = $("body");

// listeners
$("body").on("click", ".melis-savepage, .melis-publishpage, .melis-unpublishpage", function() {
	var pageId = $(this).data("pagenumber");
	//melisHelper.zoneReload(pageId+"_id_meliscms_center_page_tabs_historic_table", "melispagehistoric_table"); 
	//melisHelper.zoneReload("_id_meliscms_center_page_tabs_historic_table", "melispagehistoric_table");
	//$(".melis-refreshPageTable").trigger("click"); 
});


window.initHistoric = function(data, tblSettings) {
	// add events here if you want to do something when initializing page historic
	
	// remove the sort icon in the table head
	$(document).on("init.dt", function(e, settings) {
		var thUserId = $(".tableHistoric thead tr").find("th:nth-child(1)");
		thUserId.removeClass("sorting_asc");
	});
	
	// get the current page ID
	var pageId = $("#" + tblSettings.sTableId ).data("pagenumber");
	
	// pass what page ID to be used when displaying page historic
	data.pageId = pageId;
}
window.initDashboardPageHistoric = function() {
	setTimeout(function() {
		var historic = $("#id_melispagehistoric_dashboard_recent_activity_pages");
		if(historic.length < 1) {
            var nextLi = $("a[href='#id_melispagehistoric_dashboard_recent_activity_pages']").parents("li").nextAll("li");
            $("a[href='#id_melispagehistoric_dashboard_recent_activity_pages']").parents("li").remove();
            nextLi.addClass("active");

            var nextTab = $("div.widget-body > div.tab-content > div#id_melispagehistoric_dashboard_recent_activity_pages").next()
            $("div.widget-body > div.tab-content > div#id_melispagehistoric_dashboard_recent_activity_pages").remove();
            nextTab.addClass("active");
		}
	}, 100);
}

// OPEN HISTORIC FROM DASHBOARD WIDGET
function openHitoricFromDashboard(){
    var data = $(this).data();
    melisHelper.tabOpen( data.pageTitle, data.pageIcon, data.zoneId, data.melisKey,  { idPage: data.pageId } );
}

//open historic event
$body.on("click", '.melis-openrecenthistoric', openHitoricFromDashboard );


$(function() {
    if($("div[data-hasCms='false']").length === 1) {
        if(typeof window.initDashboardPageHistoric !== 'undefined') {
            initDashboardPageHistoric();
        }
    }
});




