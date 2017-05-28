/**
 * Created by hugofouquet on 26/03/2017.
 */

function loadGraph(response) {
    var container = $("div[data-id='" + response.status.graph_id + "']");
    if (response.status.code === 200) {
        var elementID =  "elm_" + response.graph.informations.id;
        $('<canvas>').attr({
            id: elementID
        }).appendTo(container);
        insertActions(response.graph, container);
        showGraph(response.graph, elementID);
    } else {
        container.append("<h3 class='graph-error graph-error-title'>Error Loading Graph</h3>");
        container.append("<p class='graph-error graph-error-desc'>" + response.status.title + "</p>");
        console.error(response.status.message);
    }
}

function multipleRequestGraph(ids) {
    $.ajax({
        url: "/statistic/data/multiple",
        data: ids,
        success: function( response ) {
            for (var graph in response.graphs)
                loadGraph(response.graphs[graph]);
        }
    });
}

function requestGraph(gID) {
    $.ajax({
        url: "/statistic/data/" + gID,
        data: null,
        success: loadGraph
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
            var chart = window["graphs"][gID];
            if (typeof chart === "undefined") {
                console.error("Impossible to find chart with id : " + gID);
                return;
            }
            if (res.status.code === 200) {
                chart.data.labels = res.graph.data.labels;
                chart.data.datasets = res.graph.data.datasets;
                chart.update();
            } else
                console.error(res.status.message);
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
var multiple = {};
graphs.each(function(index) {
    var multipleData = $(this).data("multiple");
    if (typeof multipleData !== "undefined") {
        if (typeof multiple[multipleData] === "undefined")
            multiple[multipleData] = {};
        multiple[$(this).data("multiple")]["id" + index] = $(this).data("id");
    } else
        requestGraph($(this).data("id"));
});

for (var graphSet in multiple){
    multipleRequestGraph(multiple[graphSet]);
}