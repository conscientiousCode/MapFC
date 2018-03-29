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