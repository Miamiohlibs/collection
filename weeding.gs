//scripts are for integrating with collection development weeding reports
//scripts pull out live information from our Sierra ILS 
//scripts are shared with an Apache License. Use at your own risk and without any guarantees.
//Copyright Craig Boman -- Miami University Libraries 2019
//Beta scripts v0.8

//to update beta scripts, edit source at https://script.google.com/a/miamioh.edu/ for Craig Boman
//this allows for a shared library between all of the weeding collection tools

function onOpen() {
  var spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
  var menuEntries = [];
  
  menuEntries.push({name: "Generate Weeding List", functionName: "batchShelfDup"});
  menuEntries.push({name: "Update Locations", functionName: "runLocationsDup"});
  menuEntries.push({name: "Update Call Numbers", functionName: "updateCallsDup"});

  spreadsheet.addMenu("Weeding", menuEntries);
} //end function onOpen()


function batchShelfDup(){
  weedingCollections.batchShelf();
}


function runLocationsDup() {
  weedingCollections.runLocations(); 
   
}//end runLocations


function updateCallsDup() {
  weedingCollections.updateCalls();
   
}//end updateCalls
