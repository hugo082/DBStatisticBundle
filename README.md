
# Database Statistic Web Interface

DBStatistic (DBS) is a graph generator that help you to implement a database statistics on your website.

`v1.0` `01 JUN 17`

## Installation

### Step 1: Composer requirement

Add repositories to your `composer.json`

    "repositories" : [
        {
            "type" : "vcs",
            "url" : "https://github.com/hugo082/DBStatisticBundle.git",
            "no-api": true
        }
    ]

Add requirement :

    "require": {
        "db/statisticbundle": "1.0.*",
        //...
    },

Update your requirements with `composer update` command.

### Step 2: Bundle configuration

Enable the bundle in the kernel :

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new DB\StatisticBundle\DBStatisticBundle()
        );
    }

Update your `routing.yml` :

    db.statistic:
        resource: "@DBStatisticBundle/Resources/config/routing.yml"
        prefix:   /statistic

Set up your `config.yml` :

    db_statistic:
        service: my.custom.processor.statistic

## About

DBStatisticBundle is a FOUQUET initiative.
See also the [creator](https://github.com/hugo082).

## License

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE)

## Documentation

### Create graph

To create a graph you must add it in your `config.yml` like this

    db_statistic:
        service: my.custom.processor.statistic
        graphs:
            myGraphId:
                method: myCustomGraphMethod
                type: graphType (doughnut | bar | line)

After this, go to your service class and implement your custom method

    use DB\StatisticBundle\Core\Graph;
    
    public function myCustomGraphMethod(Graph $graph, array $parameters) {
        // set your data
    }

### Graph Data

To set data to your graph, you must use the `Graph` parameter and insert the data. To do this, you must create a line that 
will contains the data. All data have been sorted by string label.
For example

    public function myCustomGraphMethod(Graph $graph, array $parameters) {
        $repo = $this->em->getRepository('MyBundle:MyEntity');
        $entities = $repo->findAll();

        $data = $graph->getData();
        $line = $data->createLine("main");
        foreach ($entities as $e)
            $line->incrementValueForItemWithLabel($e->getCountry(), 1);
    }

List of line data insertion :
- `setValueForItemWithLabel` : set the value for item with label
- `incrementValueForItemWithLabel` : increment the value for item with label
- `incrementValueForItemWithDate` : increment the value for item with date (line must have a `Scale`, see below)
- `incrementMoyValueForItemWithLabel` : compute automatically the average of final value.

### Graph multiple line 

Your graph can contains multiple line. To do this, can create multiple line on your data (Warning : all line must have 
different id).

    public function myCustomGraphMethod(Graph $graph, array $parameters) {
        $repo = $this->em->getRepository('MyBundle:MyEntity');
        $entities = $repo->findAll();

        $data = $graph->getData();
        $caLine = $data->createLine("ca", "Turnover");
        $beLine = $data->createLine("be", "Profit");

        foreach ($entities as $e) {
            $caLine->incrementValueForItemWithDate($e->getDate(), $e->getPrice());
            $beLine->incrementValueForItemWithDate($e->getDate(), $e->getMargin() / 100 * $e->getPrice());
        }
    }
    
### Graph actions

You can set different actions on your graph by adding them in your `config.yml` file. This action send to your custom 
method in `paramters`. Some defaults methods takes this `parameters` into account to custom the result (like scale).

    graphID:
        actions:
          - {id: actionId, title: actionTitle}
    
### Graph scale

You can create a scale for your graph. To do this, you must set the scale to your data and compute them with graph `parameters`.
All scale item have an `action_id`, and this item are choose when `parameters` contains his action. 

After setting all your data, you must compute them to take scale in consideration.

    public function caByYear(myCustomGraphMethod $graph, array $parameters) {
        $repo = $this->em->getRepository('MyBundle:MyEntity');
        $entities = $repo->findAll();

        $data = $graph->getData();
        $data->setScale(Scale::fromType(Scale::SCALE_TYPE_DATE));
        $data->getScale()->computeParameters($parameters);

        $caLine = $data->createLine("ca", "Chiffre d'affaire");
        $beLine = $data->createLine("be", "Benefice");

        foreach ($entities as $e) {
            $caLine->incrementValueForItemWithDate($e->getDate(), $e->getPrice());
            $beLine->incrementValueForItemWithDate($e->getDate(), $e->getMargin() / 100 * $e->getPrice());
        }
        $data->computeItemWithScale();
    }

List of scale type:
- `SCALE_TYPE_DATE` : date scale (default actions: `year` | `month` | `day` | `week`)
    - `lable format` : format of date that display (DateTime format)
    - `label increment` : interval of empty data (Value1 ..(empty).. Value2 ) (DateTime modify)
    - `decrement min value` : min value of date (DateTime modify)
    
### Convenient methods

- `Line` :
    - `compareAllItems` : compare all methods with your custom action.
    - `defaultLabelForDate` : set default internal of empty value (used in `SCALE_TYPE_DATE`)
    - `sortItems` : sort items with your custom action
    - `sortItemsByDate` : sort items by date (Warning : all items must have a `date`) (used in `SCALE_TYPE_DATE`)
    - `setItemsInheritance` : all item value contains values of previous items

### Display graph

To display graph, you must insert this scripts to your view :

    {% javascripts '@DBStatisticBundle/Resources/public/js/jquery.min.js' // or other jquery
    '@DBStatisticBundle/Resources/public/js/statcore.min.js'
    '@DBStatisticBundle/Resources/public/js/Chart.min.js' %}
    <script src="{{ asset_url }}"></script>
    {% endjavascripts %}
    
After this, insert in your body `div` with graph attribute like this

    <div data-type="graph" data-id="gId"></div>
    
With this code, one query is executed for each graph. To load multiple graph in one query, use `data-multiple` attribute

    <div data-type="graph" data-id="gId_1" data-multiple="first_load"></div>
    <div data-type="graph" data-id="gId_2" data-multiple="first_load"></div>
    <div data-type="graph" data-id="gId_3" data-multiple="second_load"></div>
    
### Graph options display

DBS implement Char.js to show your graph. To custom options (color, line height...) of your graph, you can set option on 
your `Line` or `DataItem`.
To manage color, you can use DB\StatisticBundle\Core\Color and his defaults methods.

    $line->setOption("backgroundColor", $backColor->getRGBA());
    
For more information about available options, see [Char.js docs](http://www.chartjs.org/docs/latest/)