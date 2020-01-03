/**
 * Execute audit
 */
function o3DrupalComponentAudit(url,user,pass) {
  var headers = {
    "Authorization" : "Basic " + Utilities.base64Encode(user + ':' + pass)
  };

  var params = {
    "method":"GET",
    "headers":headers
  };
  var response = UrlFetchApp.fetch(url + "/api/structure-relations?base_entity_type=node&target_entity_type=paragraph&sort_by=TargetBundle", params);
  var data = JSON.parse(response.getContentText());
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var masterSheetName = 'Master: Component-Content Type Map';
  var masterSheet = ss.getSheetByName(masterSheetName);
  if (!masterSheet) {
    var masterSheet = ss.insertSheet(masterSheetName);
  }
  masterSheet.getRange(1,1,1,5).setValues([['Component Name','Component Machine Name','Content Type Name', 'Content Type Machine Name','Field Machine Name to Target Component']]);
  // Master Sheet.
  for (i = 0; i < data.length; i++) {
       var datum = data[i];
       var rowId = i+2;
       masterSheet.getRange(rowId,1,1,5).setValues([[datum.TargetBundleName, datum.TargetBundle, datum.BundleName, datum.Bundle,datum.FieldName]]);
  }
  // Load data & set cells in new sheets.
  var dataCollector = [];
  for (i = 0; i < data.length; i++) {
    if (dataCollector.indexOf(data[i].TargetBundle) == -1) {
      var count = 0;
      dataCollector.push(data[i].TargetBundle);
      var newSheetName = 'Component Detail: ' + data[i].TargetBundleName;
      var ss = SpreadsheetApp.getActiveSpreadsheet();
      var newSheet = ss.getSheetByName(newSheetName);
      if (!newSheet) {
        var newSheet = ss.insertSheet(newSheetName);
      }
      newSheet.getRange(1,1,1,7).setValues([['Component Name', 'Component Machine Name', 'Content Type Name', 'Content Type Machine Name', 'Field Machine Name to Target Component', 'Example URLs', 'Example Images']]);
      newSheet.getRange(2,1,1,2).setValues([[data[i].TargetBundleName, data[i].TargetBundle]]);
      newSheet.getRange(2,3,1,3).setValues([[data[i].BundleName, data[i].Bundle, data[i].FieldName]]);
    }
    else {
      count++;
      newSheet.getRange(count+2,3,1,3).setValues([[data[i].BundleName, data[i].Bundle, data[i].FieldName]]);
    }
    newSheet.getRange('A1:G1').setFontWeight('bold');
  }
  ss.getSheetByName(masterSheetName);
}

/**
 * Handle worksheet open event
 *
 * - Add open dialog button to UI menu.
 */
function onOpen() {
  var ui = SpreadsheetApp.getUi();
  ui.createMenu('Component Audit')
      .addItem('Execute Audit', 'openDialog')
      .addToUi();
}

/**
 * Open dialog box with credentials form
 */
function openDialog() {
  var html = HtmlService.createHtmlOutputFromFile('sidebarForm');
  SpreadsheetApp.getUi() // Or DocumentApp or SlidesApp or FormApp.
      .showModalDialog(html, 'Configure website & credentials');
}
