/**
 * Created by hugofouquet on 26/03/2017.
 */

function loadGraph(gID) {
    $.ajax({
        url: "/statistic/data/" + gID,
        data: null,
        success: function( res ) {
            var graph = res.graph;
            var response = res.response;
            var container = $("div[data-id='" + response.id + "']");
            if (response.statusCode === 200) {
                var elementID =  "elm_" + graph.informations.id;
                $('<canvas>').attr({
                    id: elementID
                }).appendTo(container);
                insertActions(graph, container);
                showGraph(graph, elementID);
            } else {
                container.append("<h3 class='graph-error graph-error-title'>Error Loading Graph</h3>");
                container.append("<p class='graph-error graph-error-desc'>" + response.status + "</p>");
            }
        }
    });
}

function showGraph(graph, elemId){
    window["graphs"][graph.informations.id] = new Chart(elemId, {
        type: graph.informations.type,
        data: graph.data,
        options: graph.options
    });
}

function updateGraph(gID, action) {
    $.ajax({
        url: "/statistic/data/" + gID,
        data: action,
        success: function( res ) {
            var chart = window["graphs"][res.response.id];
            if (res.response.statusCode === 200 && typeof chart !== "undefined") {
                chart.data.labels = res.graph.data.labels;
                chart.data.datasets = res.graph.data.datasets;
                chart.update();
            } else
                console.error("Impossible to update graph with id : " + gID);
        }
    });
}

function insertActions(graph, parent) {
    var actionsContainer = $('<div>').attr({
        class: "actions-container"
    }).prependTo(parent);
    for (var i = 0; i < graph.actions.length; i++) {
        var action = graph.actions[i];
        $('<button>')
            .text(action.title)
            .prependTo(actionsContainer)
            .data("id", graph.informations.id)
            .data("action", action)
            .click(function () {
                var data = $(this).data();
                updateGraph(data.id, data.action);
        });
    }
}

var graphs = $("div[data-type='graph']");
graphs.each(function() {
    loadGraph($(this).data("id"));
});