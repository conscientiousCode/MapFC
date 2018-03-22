//HEATMAP GLOBALS
var maleHeatJson;
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

    var map = new google.maps.Map(document.getElementById('serviceRank'), {
      zoom: 12,
      center: {lat: 49.886553, lng: -119.469810},
      gestureHandling: 'cooperative',
      mapTypeId: google.maps.MapTypeId.HYBRID
    });
    var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var markers = [[],[],[],[]];
    for(var i = 0; i < Object.keys(rankMapJSON).length; i++) {
      var score = rankMapJSON[i]['score'];
      if(score <= 0.5) {
        var color = "#FFFFFF";
        var marker = 0;
      } else if(score > 0.5 && score <= 1) {
        var color = "#9999ff";
        var marker = 1;
      } else if(score > 1 && score <= 2) {
        var color = "#5b5bff";
        var marker = 2;
      } else if(score > 2) {
        var color = "#000088";
        var marker = 3;
      }

      console.log(score + " " + color);

      markers[marker].push(new google.maps.Marker({
          position: {lat:rankMapJSON[i]['lon'], lng:rankMapJSON[i]['lat']},
          map: map,
          icon: 'imgs/icon_'+ (marker+1) + '.png'
      }));
    }
    for(var k = 0; k < markers.length; k++) {
      var clusterStyle = [{
        url: 'http://localhost/imgs/cluster_'+ (k+1) +'.png',
        width: 53,
        height: 52
      }];

      if((k+1) == 4)
        clusterStyle[0]['textColor'] = 'white';

      var markerCluster = new MarkerClusterer(map, markers[k],
            {styles: clusterStyle});
    }

    var legendIcons = {
        1: {
          name: 'Fewest Resources',
          url: 'imgs/icon_1.png'
        },
        2: {
          name: 'Few Resources',
          url: 'imgs/icon_2.png'
        },
        3: {
          name: 'Moderate Resources',
          url: 'imgs/icon_3.png'
        },
        4: {
          name: 'Most Resources',
          url: 'imgs/icon_4.png'
        }
      };

    var legend = document.getElementById('category-legend');
    for(var item in legendIcons) {
        var div = document.createElement('div');
        div.innerHTML = '<img src="'+ legendIcons[item].url +'" alt="'+ legendIcons[item].name +'"> ' + legendIcons[item].name;
        legend.appendChild(div);
    }

    map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend);
  } catch(e) {
    console.log("RANK MAP INIT ERROR: " + e);
  }
}