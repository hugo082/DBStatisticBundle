/**
 * Created by hugofouquet on 26/03/2017.
 */

function ActionController(graphController, actions, graph, parent) {
    this.actions = actions;
    this.graphController = graphController;
    this.container = $('<div>').attr({
        class: "actions-container"
    }).prependTo(parent);
    this.graph = graph;

    this.insert = function () {
        var actionController = this;
        this.actions.forEach(function (action) {
            if (action.type === 'button')
                actionController.insertButton(action);
            else
                actionController.insertSelect(action);
        });
    };
    this.insertButton = function (action) {
        $('<button>')
            .text(action.title)
            .prependTo(this.container)
            .data("id", this.graph.informations.id)
            .data("action", action)
            .click(function () {
                var data = $(this).data();
                graphController.update(data.action);
            });
    };
    this.insertSelect = function (action) {
        var graphController = this.graphController;
        var select = $('<select>')
            .text(action.title)
            .prependTo(this.container)
            .data("id", this.graph.informations.id)
            .data("action", action)
            .change(function () {
                action.value = $(this).val();
                graphController.update(action);
            });
        /** @namespace action.choices */
        action.choices.forEach(function (choice) {
            select.append($('<option>', {
                value: choice.id,
                text: choice.title
            }));
        });
    }
}

function GraphController(id) {
    this.chart = null;
    this.graph = null;
    this.id = id;
    this.elementId = null;
    this.container = function () {
        return $("div[data-id='" + this.id + "']");
    };
    this.actionController = new ActionController(this, null, null, this.container());

    this.request = function () {
        var controller = this;
        $.ajax({
            url: "/statistic/data/" + this.id,
            data: null,
            success: function (response) {
                controller.loadResponse(response);
            }
        });
    };
    this.loadResponse = function (response) {
        /** @namespace response.status.graph_id */
        this.id = response.status.graph_id;
        if (response.status.code === 200)
            this.load(response.graph);
        else {
            this.container().append("<h3 class='graph-error graph-error-title'>Error Loading Graph</h3>");
            this.container().append("<p class='graph-error graph-error-desc'>" + response.status.title + "</p>");
            console.error(response.status.message);
        }
    };
    this.load = function (graph) {
        this.graph = graph;
        this.id = graph.informations.id;
        this.elementId = "elm_" + graph.informations.id;
        $('<canvas>').attr({
            id: this.elementId
        }).appendTo(this.container());
        this.show();

        $('<div>')
            .text(graph.informations.title).attr({
            class: "title-container"
        }).prependTo(this.container());

        this.actionController.graph = this.graph;
        this.actionController.actions = graph.actions;
        this.actionController.insert();
    };
    this.show = function () {
        /** @namespace this.graph.informations */
        this.chart = new Chart(this.elementId, {
            type: this.graph.informations.type,
            data: this.graph.data,
            options: this.graph.options
        });
    };
    this.update = function (action) {
        var encodeAction = {id: action.id, value: action.value};
        var graphController = this;
        $.ajax({
            url: "/statistic/data/" + graphController.id,
            data: encodeAction,
            success: function (response) {
                if (typeof graphController.chart === "undefined") {
                    console.error("Impossible to find chart with id : " + graphController.id);
                    return;
                }
                if (response.status.code === 200) {
                    graphController.chart.data.labels = response.graph.data.labels;
                    graphController.chart.data.datasets = response.graph.data.datasets;
                    graphController.chart.update();
                } else
                    console.error(response.status.message);
            }
        });
    }
}

var graphs = $("div[data-type='graph']");
var multiple = {};
graphs.each(function (index) {
    var multipleData = $(this).data("multiple");
    if (typeof multipleData !== "undefined") {
        if (typeof multiple[multipleData] === "undefined")
            multiple[multipleData] = {};
        multiple[$(this).data("multiple")]["id" + index] = $(this).data("id");
    } else {
        var graphController = new GraphController($(this).data("id"));
        graphController.request();
    }
});

for (var graphSet in multiple) {
    $.ajax({
        url: "/statistic/data/multiple",
        data: multiple[graphSet],
        success: function (response) {
            /** @namespace response.graphs */
            for (var graph in response.graphs) {
                if (response.graphs.hasOwnProperty(graph)) {
                    var graphController;
                    graphController = new GraphController(null);
                    graphController.loadResponse(response.graphs[graph]);
                }
            }
        }
    });
}