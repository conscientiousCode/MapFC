// Load the Visualization API and set callback on load
google.charts.load('current', {'packages':['corechart', 'table']});
google.charts.setOnLoadCallback(initGraphs);

//INIT AND DRAW GRAPHS UTILIZING GOOGLE CHARTS HERE
function initGraphs() {
  getDataShelterSoughtFAKE();
  getDataMostBedsAvailableFAKE();
  getAnalyticsGraphs();
  drawHistogram();
}

//INIT AND DRAW MAPS USING GOOGLE MAPS HERE
function initMaps() {
  drawHeatMaps();
  drawRankMaps();
}