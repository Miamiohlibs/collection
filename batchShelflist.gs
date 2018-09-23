function batchShelf() {
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
 
  
  //count the most frequent location ; https://medium.com/@AmJustSam/how-to-find-most-frequent-item-of-an-array-12015df68c65
  var counts = {};
  var compare = 0;
  var mostFrequent;
  (function(array){
    for(var i = 0, len = array.length; i < len; i++){
      var word = array[i];
      
      if(counts[word] === undefined){
        counts[word] = 1;
      }else{
        counts[word] = counts[word] + 1;
      }
      if(counts[word] > compare){
        compare = counts[word];
        location = range[i];
      }
    }
    Logger.log(location);
  })(range);
  //end of count most frequenct location 
  
  //continue by calling api call to the Sierra api based on location and call number range
  //logged above in logger values
  
  var url = 'http://ulblwebt02.lib.miamioh.edu/~bomanca/collection/shelflist.php?'
  + 'location=' + location + '&' + 'start=' + start + '&' + 'end=' + end;
  url = encodeURI(url)
  Logger.log(url);
  
  var result = UrlFetchApp.fetch(url);  
  var json_data = JSON.parse(result.getContentText());
  Logger.log(json_data[0]);
  
  
  //continue trying to send the location and return json content from shelflist.php on ulblweb
  
  
  
}//end function batchShelf


