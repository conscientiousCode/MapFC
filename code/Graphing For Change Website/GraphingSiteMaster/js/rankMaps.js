//JSON GLOBALS
var rankMapJSON;
var histoJSON;

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
          position: {lat:rankMapJSON[i]['lat'], lng:rankMapJSON[i]['lon']},
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