//scripts are for integrating with collection development weeding reports

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

  //have the name of the new spreadsheet be a concat of TODAY+weeding
  var today = Utilities.formatDate(new Date(), "GMT-5", "yyyy-MM-dd");
  Logger.log(today);
  var shelflist = SpreadsheetApp.getActive().insertSheet(today, SpreadsheetApp.getActive().getSheets().length);

  var columns = JSON.parse(["item record","item status"]);
  Logger.log(columns);

  shelflist.getRange(2,1,json_data.length,json_data[0].length).setValues(json_data);
  shelflist.getRange(1,1,1,columns.length).setValues(columns);

  //try to automatically create spreadsheet named shelflist



}//end function batchShelf
