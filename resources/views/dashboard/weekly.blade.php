<div class="row">
    <div class="col-md-6">
        <canvas id="weeklyBar" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
    <div class="col-md-6">
        <canvas id="weeklyDonutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
</div>
<script>
var areaChartData = {
    labels  : [{!!$day!!}],
    datasets: [
    {
        label               : 'On Time',
        backgroundColor     : 'rgba(46, 204, 113,1.0)',
        borderColor         : 'rgba(46, 204, 113,1.0)',
        pointRadius          : false,
        pointColor          : '#2ecc71',
        pointStrokeColor    : 'rgba(46, 204, 113,1.0)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(46, 204, 113,1.0)',
        data                : [{!! $total_on2 !!}]
    },
    {
        label               : 'Late',
        backgroundColor     : 'rgba(241, 196, 15,1.0)',
        borderColor         : 'rgba(241, 196, 15,1.0)',
        pointRadius          : false,
        pointColor          : 'rgba(241, 196, 15,1.0)',
        pointStrokeColor    : 'rgba(241, 196, 15,1.0)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(241, 196, 15,1.0)',
        data                : [{!! $total_late2 !!}]
    },
    {
        label               : 'Not Absence',
        backgroundColor     : 'rgba(189, 195, 199,1.0)',
        borderColor         : 'rgba(189, 195, 199,1.0)',
        pointRadius         : false,
        pointColor          : 'rgba(189, 195, 199,1.0)',
        pointStrokeColor    : 'rgba(189, 195, 199,1.0)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(189, 195, 199,1.0)',
        data                : [{!! $total_not2 !!}]
    },
    ]
}

var barChartData = $.extend(true, {}, areaChartData)
var temp0 = areaChartData.datasets[0]
var temp1 = areaChartData.datasets[1]
barChartData.datasets[0] = temp1
barChartData.datasets[1] = temp0

var stackedBarChartCanvas = $('#weeklyBar').get(0).getContext('2d')
var stackedBarChartData = $.extend(true, {}, barChartData)

var stackedBarChartOptions = {
    responsive              : true,
    maintainAspectRatio     : false,

}

new Chart(stackedBarChartCanvas, {
    type: 'bar',
    data: stackedBarChartData,
    options: stackedBarChartOptions
})

var donutChartCanvas = $('#weeklyDonutChart').get(0).getContext('2d')
    var donutData        = {
    labels: [
        'No Absence',
        'Late',
        'On TIme',
    ],
    datasets: [
        {
        data: [{{$total}}],
        backgroundColor : ['#f56954',  '#f39c12', '#00a65a'],
        }
    ]
    }
    var donutOptions     = {
    maintainAspectRatio : false,
    responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
    type: 'doughnut',
    data: donutData,
    options: donutOptions
    })
</script>
