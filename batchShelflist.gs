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
  menuEntries.push({name: "Update Call Numbers", functionName: "updateCalls"});

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

  
  //bad call number formatting ; update to include default formatting
  var start = sheet.getRange(2,1,1,1).getValues();
  var end = sheet.getRange(2,2,1,1).getValues();
  var location = sheet.getRange(2,3,1,1).getValues();
  //check for empty variables and break if empty
  if (start && end && location){
    Logger.log("vars have data")
  }
  else{
    Logger.log("empty vars")
    Browser.msgBox("empty call number start, end, or location code. check call numbers or locations")
    return
  };//end var check
  

  Logger.log(start);
  Logger.log(end);
  Logger.log(location);


  //continue by calling api call to the Sierra api based on location and call number range
  //logged above in logger values

  var url = 'http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/floyd.php?'
  + 'location=' + location + '&' + 'start=' + start + '&' + 'end=' + end;
  
  url = encodeURI(url);
  Logger.log(url);
  //bad url; problem has to do with formatting of call numbers into variables
  
  var result = UrlFetchApp.fetch(url);
  
  //check for empty result variable
  if (result == '[]'){
    Logger.log("empty vars")
    Browser.msgBox("No data returned. Check to make sure call numbers exist within location")
    return
  }
  else{
    Logger.log("vars have data")
  };//end result var check
  
  var json_data = JSON.parse(result.getContentText());
  var payload = JSON.stringify(json_data); //string representation?
  Logger.log(json_data);

 //new spreadsheet named after the criteria used to create it
  var name = start + '-' + end + ' (' + location + ')';
  Logger.log(name);
  var shelflist = SpreadsheetApp.getActive().insertSheet(name, SpreadsheetApp.getActive().getSheets().length);

  var columns = [["item record","item status","call number","author","title","pub year","last checkout date","last checkin date","checkout total","internal use","renewals","date acquired","decision"]]; 
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
  
  var name = 'location list';
  var list = SpreadsheetApp.getActive().insertSheet(name, SpreadsheetApp.getActive().getSheets().length);
  var columns = [["location code","location labal"]];
  list.getRange(2,1,json_data.length,json_data[0].length).setValues(json_data);
  list.getRange(1,1,1,columns[0].length).setValues(columns);  
   
}//end runLocations


function updateCalls() {
  var calls = 'http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/call_numbers.php';
  
  url = encodeURI(calls)
  var result = UrlFetchApp.fetch(url);
  var json_data = JSON.parse(result.getContentText());
  var payload = JSON.stringify(json_data); //string representation?
  
  Logger.log(json_data);  
  
  var name = 'Call numbers';
  var list = SpreadsheetApp.getActive().insertSheet(name, SpreadsheetApp.getActive().getSheets().length);
  var columns = [["Call Numbers"]];
  list.getRange(2,1,json_data.length,json_data[0].length).setValues(json_data);
  list.getRange(1,1,1,columns[0].length).setValues(columns);  
   
}//end updateCalls



function test(){
  var result = [];
  Logger.log(result);
  
  //check for empty variables
  if (result !== '[]'){
    Logger.log("empty vars")
    Browser.msgBox("")
  }
  else{
    Logger.log("vars have data")
    
  };
  
}
