/*******************************************************/
//Repurpose or Erase the example code below
/*******************************************************/

// Load the Visualization API and the piechart package.
google.charts.load('current', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);

//This is the other, more robust method for utilizing the callback process
//The function name is supplied in the AJAX request. When it gets data back,
//the AJAX function calls this function and passes the data in.
//Feel free to use the other method if you've got a shorter bit of code.  
function drawChart() {
  //Another version of an AJAX POST Request. For docs on JSON formatting for dataTables:
  //https://developers.google.com/chart/interactive/docs/reference#dataparam
  //https://developers.google.com/chart/interactive/docs/php_example
  var jsonData = $.ajax({
    type: "POST",
    url: "/api/exampleRestRequest.php",
    data: { req:"teamPLevel" },
    dataType: "json",
    async: false //IMPORTANT: Means the code will wait for this to complete
    }).responseText;
  try {
    var jsonData = JSON.parse(jsonData);
  } catch(e) {
    console.log("JSON PARSE ERROR: " + e);
    jsonData = null;
  }

  if(jsonData != null) {
    //WARNING: ARRAY HELL FOR THIS SECTION. DO NOT COPY.
    //Had to reform the JSON into arrays due to poor planning in the PHP.
    //PLEASE FORMAT YOUR JSON INTO THE GOOGLE CHARTS JSON FORMAT ON THE REST REQUEST
    var count = Object.keys(jsonData).length;
    var dataArray = [];
    var header = ["Members", "Power Level"];
    dataArray.push(header);
    for(var i = 0; i < count; i++) {
      var tempArr = [];
      tempArr.push(jsonData[i]["name"]);
      tempArr.push(parseInt(jsonData[i]["powerlevel"]));
      dataArray.push(tempArr);
    }

    // Create our data table out of JSON data loaded from server.
    var data = google.visualization.arrayToDataTable(dataArray);

    //Chart options
    var options = {
      title: "Group Member Power Levels",
      legend: { position: "none" },
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('exampleGraphDiv'));
    chart.draw(data, options);
  }
}