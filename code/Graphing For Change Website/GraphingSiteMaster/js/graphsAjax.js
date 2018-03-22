//HEATMAP GLOBALS
var servicesHeatJson;
var femaleHeatJson;
var transHeatJson;
var heatMaps = [];
var numHeatMaps = 3;
var heatMapsLoaded = 0;
//RANK MAP GLOBALS
var rankMapJSON;

// Load the Visualization API and set callback on load
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(initGraphs);

//INIT AND DRAW GRAPHS HERE
function initGraphs() {
  
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
    data: { req:"all" },
    dataType: "json",
    success: function(res){
      console.log("Services HeatMap Loaded");
      servicesHeatJson = res;
      countHeatmaps();
    },
    error: function(e) {
      console.log("Heatmap Error: " + e);
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
    heatMaps.push([servicesHeatJson, "servicesHeatmap"]);
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