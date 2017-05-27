/**
 * Created by hugofouquet on 26/03/2017.
 */

function loadGraph(gId) {
    $.ajax({
        url: "/statistic/data/" + gId,
        data: null,
        success: function( res ) {
            var graph = res.graph;
            var response = res.response;
            var container = $("div[data-id='" + response.id + "']");
            if (response.statusCode === 200) {
                var elementID =  "elm_" + gId;
                $('<canvas>').attr({
                    id: elementID
                }).appendTo(container);
                insertButtons([{title:"hello", id:"helloid"}], container);
                showGraph(graph, elementID);
            } else {
                container.append("<h3 class='graph-error graph-error-title'>Error Loading Graph</h3>");
                container.append("<p class='graph-error graph-error-desc'>" + response.status + "</p>");
            }
        }
    });
}

function showGraph(graph, elemId){
    new Chart(elemId, {
        type: graph.informations.type,
        data: graph.data,
        options: graph.options
    });
}

function insertButtons(buttons, parent) {
    var buttonsContainer = $('<div>').attr({
        class: "buttons-container"
    }).prependTo(parent);

    for (var i = 0; i < buttons.length; i++) {
        var button = buttons[i];
        $('<button>').attr({
            id: button.id
        }).text(button.title).prependTo(buttonsContainer);
    }
}

var graphs = $("div[data-type='graph']");
graphs.each(function() {
    loadGraph($(this).data("id"));
});