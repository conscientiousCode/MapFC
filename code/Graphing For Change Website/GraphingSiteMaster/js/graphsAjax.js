//HEATMAP GLOBALS
var maleHeatJson;
var femaleHeatJson;
var transHeatJson;
var heatMaps = [];
var numHeatMaps = 3;
var heatMapsLoaded = 0;
//RANK MAP GLOBALS
var rankMapJSON;
var histoJSON;
// Load the Visualization API and set callback on load
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(initGraphs);

//INIT AND DRAW GRAPHS HERE
function initGraphs() {
  drawHistogram();
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
    
    for(var i = 0; i < Object.keys(rankMapJSON).length; i++) {
      var score = rankMapJSON[i]['score'];
      if(score <= 0.5) {
        var color = "#FFFFFF";
      } else if(score > 0.5 && score <= 1) {
        var color = "#9999ff";
      } else if(score > 1 && score <= 2) {
        var color = "#5b5bff";
      } else if(score > 2) {
        var color = "#000088";
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
function drawHistogram(){
      $.ajax({
    type: "POST",
    url: "/api/linearRankedComboGraph.php",
    data: {},
    dataType: "json",
    success: function(res){
      console.log("Rank Map Loaded");
      histoJSON = res;
      initHisto();
    },
    error: function(e) {
      console.log("HistoGraph Error: " + e);
    },
    async: true
  }).responseText;
    
}

function initHisto(){
      try {
    if(typeof histoJSON != 'object' || histoJSON == null) {
      throw "Rank Data is not JSON";
    }
    var no = 0;
    var min = 0;
    var med = 0;
    var strong = 0;
    var very = 0;
    var data = [];
    var dataname = ['Amount of Support','Count'];
    data.push(dataname);
    for(var i =0; i< Object.keys(histoJSON).length;i++){
        var score = histoJSON[i]['score'];
      if(score == 0) {
        no++;
      } else if( score <= 1) {
        min++;
      } else if( score <= 2) {
        med++;
      } else if(score<=3) {
        strong++;
      }else{
        very++;
      }
    }
    data.push(['No Support',no]);
    data.push(['Minimal Support',min]);
    data.push(['Medium Support',med]);
    data.push(['Strong Support',strong]);
    data.push(['Very Strong Support',very]);
    var googledata = google.visualization.arrayToDataTable(data);
    var options ={
      legend: {position:'none'},
      vAxis: {title:'Count'}
    };
    var chart = new google.visualization.ColumnChart(document.getElementById('rankedHisto'));
    chart.draw(googledata,options);
      }catch(e){
        console.log("Ranked Histo Error: "+e);
      }
}