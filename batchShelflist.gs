//scripts are for integrating with collection development weeding reports
//scripts pull out live information from our Sierra ILS
//scripts are shared with an Apache License. Use at your own risk and without any guarantees.
//Copyright Craig Boman -- Miami University Libraries 2019
//Beta scripts v0.8

function onOpen() {
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var menuEntries = [];

  menuEntries.push({name: "Generate Weeding List", functionName: "batchShelf"});
  menuEntries.push({name: "Update Locations", functionName: "runLocations"});

  spreadsheet.addMenu("Weeding", menuEntries);
} //end function onOpen()



function batchShelf() {
//  var start = 'AY   67 N5 W7  2005';
//  var end = 'PN  171 F56 W35 1998';
//  var location = 'scr';

  var spread_sheet_name = SpreadsheetApp.getActiveSpreadsheet().getName();
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName('weeding_criteria');
  //find all the items that may not have been filled in correctly.
  //get the row number of the last row

  var start = sheet.getRange(2,1,1,1).getValues();
  var end = sheet.getRange(2,2,1,1).getValues();
  var location = sheet.getRange(2,3,1,1).getValues();

  Logger.log(start);
  Logger.log(end);
  Logger.log(location);


  //continue by calling api call to the Sierra api based on location and call number range
  //logged above in logger values

  var url = 'http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/floyd.php?'
  + 'location=' + location + '&' + 'start=' + start + '&' + 'end=' + end;
  url = encodeURI(url)
  Logger.log(url);

  var result = UrlFetchApp.fetch(url);
  var json_data = JSON.parse(result.getContentText());
  var payload = JSON.stringify(json_data); //string representation?
  Logger.log(json_data);

 //new spreadsheet named after the criteria used to create it
  var name = start + '-' + end + ' (' + location + ')';
  Logger.log(name);
  var shelflist = SpreadsheetApp.getActive().insertSheet(name, SpreadsheetApp.getActive().getSheets().length);

  var columns = [["item record","item status","call number","author","title","pub year","last checkout date","last checkin date","checkout total","internal use","renewals","date acquired","Decision"]];
  Logger.log(columns[0].length);

  shelflist.getRange(2,1,json_data.length,json_data[0].length).setValues(json_data);
  shelflist.getRange(1,1,1,columns[0].length).setValues(columns);
  shelflist.setFrozenRows(1);



}//end function batchShelf


function runLocations() {
  var locations = 'http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/locations.php';

  url = encodeURI(locations)
  var result = UrlFetchApp.fetch(url);
  var json_data = JSON.parse(result.getContentText());
  var payload = JSON.stringify(json_data); //string representation?

  Logger.log(json_data);

  var name = 'location list'
  var list = SpreadsheetApp.getActive().insertSheet(name, SpreadsheetApp.getActive().getSheets().length);
  var columns = [["location code","location labal"]]
  list.getRange(2,1,json_data.length,json_data[0].length).setValues(json_data);
  list.getRange(1,1,1,columns[0].length).setValues(columns);

  //add feature to lock/protect list of locations from edits

}//end runLocations

function runLocations() {
  var locations = 'http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/locations.php';

  url = encodeURI(locations)
  var result = UrlFetchApp.fetch(url);
  var json_data = JSON.parse(result.getContentText());
  var payload = JSON.stringify(json_data); //string representation?

  Logger.log(json_data);

  var name = 'location list'
  var list = SpreadsheetApp.getActive().insertSheet(name, SpreadsheetApp.getActive().getSheets().length);
  var columns = [["location code","location labal"]]
  list.getRange(2,1,json_data.length,json_data[0].length).setValues(json_data);
  list.getRange(1,1,1,columns[0].length).setValues(columns);

}//end runLocations
