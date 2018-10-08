$(function(){
	// cache body
	var $body = $("body");
	
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
	};
	
	//open historic event
	$body.on("click", '.melis-openrecenthistoric', function(){
	    var data = $(this).data();
	    //OPEN HISTORIC FROM DASHBOARD WIDGET
	    melisHelper.tabOpen( data.pageTitle, data.pageIcon, data.zoneId, data.melisKey,  { idPage: data.pageId } );
	});
});