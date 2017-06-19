(function ($) {
    // Load chart package
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawCharts);


    /**
     * Draw Charts after chart package is loaded
     */
    function drawCharts() {
        $('#charts-body').show().lock();
        getChartsData().done(processChartData);
    }


    /**
     * Request for chart data
     * @return {*}
     */
    var getChartsData = function () {
        return $.ajax({
            url: dashboardUrls.chartDataUrl,
            dataType: "json"
        });
    };


    /**
     * Process obtained chart data and draw graphs.
     * @param response
     */
    function processChartData(response) {
        var formattedData = {};
        var headers = response.headers;
        var totals = {};

        if (response.rows === null) {
            $('#charts-body')
                .html('<em>Tento profil zatím neobsahuje žádná data!</em>')
                .unlock();

            return;
        }

        for (var type in response.totals) {
            totals[type] = formatValue(type, response.totals[type]);
            formattedData[type] = [];
        }

        for (var ri in response.rows) {
            var rowData = response.rows[ri];
            var dateText = rowData[0];
            var date = new Date(
                dateText.substring(0, 4), // year
                dateText.substring(4, 6) - 1, // month
                dateText.substring(6, 8) // day
            );

            for (var ci in rowData) {
                if (ci == 0) {
                    continue;
                }
                var header = headers[ci - 1];
                var value = formatValue(header, rowData[ci]);

                var columnData = [date, value];

                if (!formattedData[header]) {
                    formattedData[header] = [columnData];
                } else {
                    formattedData[header].push(columnData)
                }
            }
        }

        drawSessionsChart(formattedData['ga:sessions']);
        drawMiniChart('ga-visits-chart',
            formattedData['ga:sessions'],
            formatValueAndCreateFormatter('ga:sessions', totals['ga:sessions'])
        );
        drawMiniChart('ga-users-chart',
            formattedData['ga:users'],
            formatValueAndCreateFormatter('ga:users', totals['ga:users'])
        );
        drawMiniChart('ga-pageviews-chart',
            formattedData['ga:pageviews'],
            formatValueAndCreateFormatter('ga:pageviews', totals['ga:pageviews'])
        );
        drawMiniChart('ga-viewsPerSession-chart',
            formattedData['ga:pageviewsPerSession'],
            formatValueAndCreateFormatter('ga:pageviewsPerSession', totals['ga:pageviewsPerSession'])
        );

        drawMiniChart('ga-BounceRate-chart',
            formattedData['ga:BounceRate'],
            formatValueAndCreateFormatter('ga:BounceRate', totals['ga:BounceRate'])
        );

        drawMiniChart('ga-organicSearches-chart',
            formattedData['ga:organicSearches'],
            formatValueAndCreateFormatter('ga:organicSearches', totals['ga:organicSearches'])
        );

        drawReturningUsersChart(
            totals['ga:users'],
            totals['ga:newUsers']
        );

        $('#charts-body').unlock();
    }


    /**
     * Get formatted value.
     * @param type
     * @param value
     */
    function formatValue(type, value) {
        return formatValueAndCreateFormatter(type, value).value;
    }


    /**
     * Format value, total value and create data formatter.
     * @param type
     * @param value
     * @return {{value: Number, total: Number, formatter: null}}
     */
    function formatValueAndCreateFormatter(type, value) {
        var result = {value: parseFloat(value), total: parseFloat(value), formatter: null};
        switch (type) {
            case 'ga:pageviewsPerSession':
                result.total = result.value.toFixed(2);
                result.formatter = new google.visualization.NumberFormat({
                    fractionDigits: 2
                });
                break;
            case 'ga:BounceRate':
                result.total = result.value.toFixed(2) + '%';
                result.formatter = new google.visualization.NumberFormat({
                    fractionDigits: 2,
                    suffix: '%'
                });
                break;
        }

        return result;
    }


    /**
     * Draw mini chart
     * @param {String} elId
     * @param {Array} data
     * @param {Object} prefs
     */
    function drawMiniChart(elId, data, prefs) {
        var dataTable = google.visualization.arrayToDataTable(data, true);

        var options = {
            legend: {
                position: 'none'
            },
            pointsVisible: false,
            lineWidth: 1,
            chartArea: {
                width: '95%',
                height: '99%'
            },
            vAxis: {
                textPosition: "none",
                minValue: 0
            },
            hAxis: {
                textPosition: "none",
                format: 'd.M.y',
                gridlines: {
                    count: 15,
                    color: '#FFF'
                }
            },
            crosshair: {
                trigger: 'both',
                orientation: 'vertical'
            },
            tooltip: {
                trigger: 'none'
            },
            focusTarget: 'category'
        };

        var formatter = new google.visualization.DateFormat({
            pattern: 'd.M.y'
        });
        formatter.format(dataTable, 0);

        if (prefs.formatter) {
            prefs.formatter.format(dataTable, 1);
        }

        // Total
        var $element = $('#' + elId);
        var $h2 = $element.prev();
        $h2.text(prefs.total);
        var $strong = $h2.prev().css('color', 'white').text('--.--.----');

        // Chart
        var chart = new google.visualization.AreaChart($element[0]);
        chart.draw(dataTable, options);

        google.visualization.events.addListener(chart, 'onmouseover', function (e) {
            $h2.text(dataTable.getFormattedValue(e.row, 1));
            $strong.text(dataTable.getFormattedValue(e.row, 0));
            $strong.css('color', 'inherit');
        });

        google.visualization.events.addListener(chart, 'onmouseout', function (e) {
            $h2.text(prefs.total);
            $strong.css('color', 'white').text('--.--.----');
        });
    }


    /**
     * Draw main sessions chart.
     * @param {Array} data
     */
    function drawSessionsChart(data) {
        var dataTable = google.visualization.arrayToDataTable(data, true);

        var options = {
            legend: {
                position: 'none'
            },
            pointSize: 3,
            chartArea: {
                width: '95%',
                height: '50%',
                left: 40,
                top: 20,
                bottom: 60
            },
            vAxis: {
                textPosition: "out",
                minValue: 0,
                gridlines: {count: 5}
            },
            hAxis: {
                textPosition: "out",
                format: 'd.M.y',
                gridlines: {
                    count: 15,
                    color: '#FFF'
                }
            },
            crosshair: {
                trigger: 'both',
                orientation: 'vertical'
            },
            focusTarget: 'category'
        };

        var formatter = new google.visualization.DateFormat({
            pattern: 'd.M.y'
        });
        formatter.format(dataTable, 0);

        var chart = new google.visualization.AreaChart(document.getElementById('ga-sessions-chart'));
        chart.draw(dataTable, options);
    }


    /**
     * Draw main sessions chart.
     * @param {Number} users
     * @param {Number} newUsers
     */
    function drawReturningUsersChart(users, newUsers) {
        var returningUsers = users - newUsers;

        if (returningUsers < 0) {
            returningUsers = 0;
        }

        var dataTable = new google.visualization.DataTable();
        dataTable.addColumn('string', 'Type');
        dataTable.addColumn('number', 'Total');
        dataTable.addRows([
            ['Vracející se uživatelé', returningUsers],
            ['Noví uživatelé', newUsers]
        ]);

        var options = {};

        var chart = new google.visualization.PieChart(document.getElementById('ga-returningUsers-chart'));
        chart.draw(dataTable, options);
    }
}(jQuery));