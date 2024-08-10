$(document).ready(function() {
  
    load_data();

    function load_data(filters = []) {
        $.getJSON("{{ path('app_rh_dashboard_temps_plein_partiel_data') }}", {filters: filters}, function (data) {
            console.log(data.series);

            Highcharts.chart('container', {

                title: {
                    text: '',
                    align: 'left'
                },

                subtitle: {
                    text: '',
                    align: 'left'
                },

                yAxis: {
                    title: {
                        text: ''
                    }
                },

                xAxis: {
                    accessibility: {
                        rangeDescription: data.range
                    }
                },

                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

                plotOptions: {
                    series: {
                        label: {
                            connectorAllowed: false
                        },
                        pointStart: parseInt(data.annees.plein.min_year)
                    }
                },

                series: data.series.plein,

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }

            });

            //Deuxieme charte
            Highcharts.chart('container0', {

                title: {
                    text: '',
                    align: 'left'
                },

                subtitle: {
                    text: '',
                    align: 'left'
                },

                yAxis: {
                    title: {
                        text: ''
                    }
                },

                xAxis: {
                    accessibility: {
                        rangeDescription: data.rangePartiel
                    }
                },

                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

                plotOptions: {
                    series: {
                        label: {
                            connectorAllowed: false
                        },
                        pointStart: parseInt(data.annees.partiel.min_year)
                    }
                },

                series: data.series.partiel,

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }

            });

            

        })


    }




})