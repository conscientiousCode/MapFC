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