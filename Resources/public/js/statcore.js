/**
 * Created by hugofouquet on 26/03/2017.
 */

function loadGraph(gId, elemId) {
    $.ajax({
        url: "/statistic/data/" + gId,
        data: null,
        success: function( resObj ) {
            if (resObj.response.statusCode == 200) {
                showGraph(resObj.graph, elemId);
            }
        }
    });
}

function showGraph(gObj, elemId){

    var data = [];
    gObj.lines.forEach(function (element, index, array) {
        console.log(element);
        var item = {
            label: element.label,
            data: element.dataSets,
            backgroundColor: element.backgroundColor,
            hoverBackgroundColor: "#3c3c3c"
        };
        data.push(item);
    });
    console.log(gObj.lines);
    console.log(data);

    var data = {
        labels: gObj.labels,
        datasets: data
    };

    new Chart(elemId, {
        type: gObj.information.type,
        data: data,
        options: null
    });
}