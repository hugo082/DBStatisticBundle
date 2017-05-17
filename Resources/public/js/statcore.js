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
    new Chart(elemId, {
        type: gObj.informations.type,
        data: gObj.data,
        options: gObj.options
    });
}