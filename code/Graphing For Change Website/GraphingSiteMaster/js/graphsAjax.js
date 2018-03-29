
//HEATMAP GLOBALS
var servicesHeatJson;
//JSON GLOBALS
var femaleHeatJson;
var transHeatJson;
var fakeYearlyViolenceJson;
var shelterSoughtFAKEJson;
var mostBedsAvailableFAKEJson;
var heatMaps = [];
var numHeatMaps = 3;
var heatMapsLoaded = 0;
var rankMapJSON;
var histoJSON;

// Load the Visualization API and set callback on load
google.charts.load('current', {'packages':['corechart', 'table']});
google.charts.setOnLoadCallback(initGraphs);

//INIT AND DRAW GRAPHS HERE
function initGraphs() {
  //getDataViolenceGraph();
  getDataShelterSoughtFAKE();
  getDataMostBedsAvailableFAKE();
  getAnalyticsGraphs();
  drawHistogram();
}

//INIT AND DRAW MAPS HERE
function initMaps() {
  drawHeatMaps();
  drawRankMaps();
}

function getAnalyticsGraphs() {
  drawWordCloud();
  drawLineChart();
  drawPieChart();
}

function drawWordCloud() {
  color = d3.scale.linear().domain([-1, 0, 1]).range(["#777777", "#c7dff9", "#4896ec"]);

  d3.wordcloud()
  .size([800, 400])
  .selector('#wordCloud')
  .rotate(0)
  .scale('log')
  .font("sans-serif")
  .words([{text: 'shelter', size: 8},
          {text: 'shared', size: 6}, 
          {text: 'women', size: 8}, 
          {text: 'transitional', size: 7}, 
          {text: 'care', size: 7}, 
          {text: 'support', size: 5}, 
          {text: 'youth', size: 7}, 
          {text: 'the', size: 5}])
  .start();
}

function drawLineChart() {
  var data = google.visualization.arrayToDataTable([
    ['Month', 'Views'],
    ['April',  1000],
    ['May',  1170],
    ['June',  660],
    ['July',  1030],
    ['August',  930],
    ['September',  1200],
    ['November',  1356],
    ['December',  1732],
    ['January', 1654]
  ]);

  var options = {
    hAxis: {title: 'Month',  titleTextStyle: {color: '#333'}},
    vAxis: {minValue: 0},
    pointsVisible: true,
    colors: ['rgb(33,150,243)']
  };

  var chart = new google.visualization.AreaChart(document.getElementById('viewCount'));
  chart.draw(data, options);
}

function drawPieChart() {
  var data = google.visualization.arrayToDataTable([
    ['Visitor Type', 'Percentage'],
    ['Returning Visitor', 26.09],
    ['New Visitor', 73.91]
  ]);

  var options = {
    colors: ['#b3d4fc', 'rgb(33, 150, 243)']
  };

  var chart = new google.visualization.PieChart(document.getElementById('pieChart'));

  chart.draw(data, options);
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

    var map = new google.maps.Map(document.getElementById('serviceRank'), {
      zoom: 12,
      center: {lat: 49.886553, lng: -119.469810},
      gestureHandling: 'cooperative',
      mapTypeId: google.maps.MapTypeId.HYBRID
    });
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

      markers[marker].push(new google.maps.Marker({
          position: {lat:rankMapJSON[i]['lon'], lng:rankMapJSON[i]['lat']},
          map: map,
          icon: 'imgs/icon_'+ (marker+1) + '.png'
      }));
    }
    for(var k = 0; k < markers.length; k++) {
      var clusterStyle = [{
        url: 'imgs/cluster_'+ (k+1) +'.png',
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
