//HEATMAP GLOBALS
var maleHeatJson;
var femaleHeatJson;
var transHeatJson;
var fakeYearlyViolenceJson;
var shelterSoughtFAKEJson;
var mostBedsAvailableFAKEJson;
var heatMaps = [];
var numHeatMaps = 3;
var heatMapsLoaded = 0;
//RANK MAP GLOBALS
var rankMapJSON;

// Load the Visualization API and set callback on load
google.charts.load('current', {'packages':['corechart', 'table']});
google.charts.setOnLoadCallback(initGraphs);

//INIT AND DRAW GRAPHS HERE
function initGraphs() {
  //getDataViolenceGraph();
  getDataShelterSoughtFAKE();
  getDataMostBedsAvailableFAKE();
}

function getDataViolenceGraph() {
  $.ajax({
      type: "POST",
      url: "/api/violenceOverYearFAKE.php",
      data: { req:"null" },
      dataType: "json",
      success: function(res){
          console.log("Fake Yearly Violence Loaded");
          fakeYearlyViolenceJson = res;
          drawViolenceGraph();
      },
      error: function(e) {
          console.log("Fake Yearly Violence Error: " + e);
      },
      async: true
  }).responseText;
}


function drawViolenceGraph() {
  console.log(fakeYearlyViolenceJson);
  var data = new google.visualization.DataTable(fakeYearlyViolenceJson);

  //Chart options
  var options = {
      title: "Violent Incidents (FAKE DATA)",
      vAxis: { title: "Number of Violent Events" },
      hAxis: { title: "Day" },
      legend: { position: "none" },
      colors: ["#000"]
  };

  // Instantiate and draw our chart, passing in some options.
  var chart = new google.visualization.LineChart(document.getElementById('fakeViolenceGraph'));

  chart.draw(data, options);
}
function getDataShelterSoughtFAKE(){
    $.ajax({
        type: "POST",
        url: "/api/ShelterSoughtFAKE.php",
        data: { req:"null" },
        dataType: "json",
        success: function(res){
            console.log("Fake Yearly Shelter Sought Loaded");
            shelterSoughtFAKEJson = res;
            drawShelterSoughtGraphFAKE();
        },
        error: function(e) {
            console.log("Fake Yearly Shelter Sought Error: " + e);
        },
        async: true
    }).responseText;
}

function drawShelterSoughtGraphFAKE(){
    var data = new google.visualization.DataTable(shelterSoughtFAKEJson);

    //Chart options
    var options = {
        title: "Number of People Seeking Shelter vs Waitlist (FAKE DATA)",
        vAxis: { title: "Shelter Beds" },
        hAxis: { title: "Day" },
        legend: { position: "bottom" },
        series:{
            0: { color: '#000000' },
            1: { color: '#e7711b' }}
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.LineChart(document.getElementById('shelterSoughtFAKEGraph'));

    chart.draw(data, options);
}

function getDataMostBedsAvailableFAKE(){
    $.ajax({
        type: "POST",
        url: "/api/MostBedsAvailableFAKE.php",
        data: { req:"null" },
        dataType: "json",
        success: function(res){
            console.log("Fake Beds Availability Loaded");
            mostBedsAvailableFAKEJson = res;
            drawMostBedsAvailableFAKE();
        },
        error: function(e) {
            console.log("Fake Beds Availability Error: " + e);
        },
        async: true
    }).responseText;
}

function drawMostBedsAvailableFAKE(){
    var data = new google.visualization.DataTable(mostBedsAvailableFAKEJson);

    //Chart options
    var options = {
        title: "Service Providrs and Bed Availability",
        sortColumn: 2,
        sortAscending: false,
        width: 800
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.Table(document.getElementById('mostBedsAvailableFAKEGraph'));

    chart.draw(data, options);
}


//INIT AND DRAW MAPS HERE
function initMaps() {
  drawHeatMaps();
  drawRankMaps();
}

function drawHeatMaps() {
  $.ajax({
    type: "POST",
    url: "/api/ServicesForGenders.php",
    data: { req:"female" },
    dataType: "json",
    success: function(res){
      console.log("Female HeatMap Loaded");
      femaleHeatJson = res;
      countHeatmaps();
    },
    error: function(e) {
      console.log("Female Heatmap Error: " + e);
    },
    async: true
  }).responseText;

  $.ajax({
    type: "POST",
    url: "/api/ServicesForGenders.php",
    data: { req:"male" },
    dataType: "json",
    success: function(res){
      console.log("Male HeatMap Loaded");
      maleHeatJson = res;
      countHeatmaps();
    },
    error: function(e) {
      console.log("Male Heatmap Error: " + e);
    },
    async: true
  }).responseText;

  $.ajax({
    type: "POST",
    url: "/api/ServicesForGenders.php",
    data: { req:"transgender" },
    dataType: "json",
    success: function(res){
      console.log("Transgender HeatMap Loaded");
      transHeatJson = res;
      countHeatmaps();
    },
    error: function(e) {
      console.log("Transgender Heatmap Error: " + e);
    },
    async: true
  }).responseText;
}

function countHeatmaps() {
  
  heatMapsLoaded++
  if(heatMapsLoaded == numHeatMaps) {
    heatMaps.push([femaleHeatJson, "femaleHeatmap"]);
    heatMaps.push([maleHeatJson, "maleHeatmap"]);
    heatMaps.push([transHeatJson, "transHeatmap"]);

    initHeatMaps();
  }
}

function initHeatMaps() {
  try {
    if(heatMaps.length < 1) {
      throw "Heatmaps not loaded"
    }
    
    var map, heatmap;
    for(var i = 0; i < heatMaps.length; i++) {
      if(heatMaps[i][0] == "" || heatMaps[i][0] == null) {
        console.log("HeatMap " + heatMaps[i][1] + " not loaded");
      } else {
        if(typeof heatMapJSON == 'string') {
          var heatMapJSON = JSON.parse(heatMaps[i][0]);
        } else {
          var heatMapJSON = heatMaps[i][0];
        }
        
        map = new google.maps.Map(document.getElementById(heatMaps[i][1]), {
          zoom: 12,
          center: {lat: 49.886553, lng: -119.469810},
          gestureHandling: 'cooperative',
          mapTypeId: google.maps.MapTypeId.HYBRID
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
          data: getGeoPoints(heatMapJSON),
          map: map,
          radius: 30
        });
        
      }
    }
  } catch(e) {
    console.log("HEATMAP ERROR: " + e);
  }
}

function getGeoPoints(graphJson) {
  var geoPoints = [];
  for(var i=0; i<Object.keys(graphJson).length; i++)
  {
    geoPoints[i] = new google.maps.LatLng(graphJson[i]['lon'], graphJson[i]['lat']);
  }
  return geoPoints;
}

function drawRankMaps() {
  $.ajax({
    type: "POST",
    url: "/api/linearRankedComboGraph.php",
    data: {},
    dataType: "json",
    success: function(res){
      console.log("Rank Map Loaded");
      rankMapJSON = res;
      initRankMap();
    },
    error: function(e) {
      console.log("Female Heatmap Error: " + e);
    },
    async: true
  }).responseText;
}

function initRankMap() {
  try {
    if(typeof rankMapJSON != 'object' || rankMapJSON == null) {
      throw "Rank Data is not JSON";
    }
    
    if(typeof heatMapJSON == 'string') {
      rankMapJSON = JSON.parse(heatMaps[i][0]);
    }
    
    var map = new google.maps.Map(document.getElementById('serviceRank'), {
      zoom: 12,
      center: {lat: 49.886553, lng: -119.469810},
      gestureHandling: 'cooperative',
      mapTypeId: google.maps.MapTypeId.HYBRID
    });
    
    for(var i = 0; i < Object.keys(rankMapJSON).length; i++) {
      var score = rankMapJSON[i]['score'];
      if(score <= 2.5) {
        var color = "#FF0000";
      } else if(score > 2.5 && score <= 5) {
        var color = "#FF8000";
      } else if(score > 5 && score <= 7.5) {
        var color = "#FFFF00";
      } else if(score > 7.5) {
        var color = "#00FF00";
      }
      
      console.log(score + " " + color);
      
      var siteCircle = new google.maps.Circle({
        strokeColor: color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: color,
        fillOpacity: 1.0,
        map: map,
        center: {lat:rankMapJSON[i]['lon'], lng:rankMapJSON[i]['lat']},
        radius: 100
      });
    }
  } catch(e) {
    console.log("RANK MAP INIT ERROR: " + e);
  }
}