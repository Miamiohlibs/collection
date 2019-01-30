function batchShelf() {
  //test variables delete in production
  var start = 'AY   67 N5 W7  2005';
  var end = 'PN  171 F56 W35 1998';
  var location = 'scr';


  var spread_sheet_name = SpreadsheetApp.getActiveSpreadsheet().getName();
  var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName('inventory');
  //find all the items that may not have been filled in correctly.
  //get the row number of the last row
  
  var first = sheet.getRange(1,2).getValues();
  var start = first[0];
  Logger.log(first[0]);
  
  
  var lastRow = sheet.getLastRow();
  
  var last = sheet.getRange(lastRow,2).getValues();
  var end = last[0];
  Logger.log(last[0]);
    
  var range = sheet.getSheetValues(1,4,lastRow,1);
  //Logger.log(range[3]);
 
 
  
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
  var shelflist = SpreadsheetApp.getActive().insertSheet('shelflist', SpreadsheetApp.getActive().getSheets().length);
  
  //var shelflist = SpreadsheetApp.getActiveSpreadsheet().getSheetByName('shelflist');
  shelflist.getRange(1,1,json_data.length,7).setValues(json_data);
    
  //try to automatically create spreadsheet named shelflist
  
  
  
}//end function batchShelf

