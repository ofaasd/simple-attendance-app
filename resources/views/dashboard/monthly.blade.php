<div class="row">
    <div class="col-md-12">
        <canvas id="monthlyBar" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
</div>
<script>
var areaChartData = {
      labels  : [{!! $date_chart !!}],
      datasets: [
        {
          label               : '{{$list_status[$status]}}',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [{!! $total !!}]
        },
      ]
    }

var barChartCanvas = $('#monthlyBar').get(0).getContext('2d')
var barChartData = $.extend(true, {}, areaChartData)
var temp0 = areaChartData.datasets[0]
barChartData.datasets[0] = temp0

var barChartOptions = {
    responsive              : true,
    maintainAspectRatio     : false,
    datasetFill             : false
}

new Chart(barChartCanvas, {
    type: 'bar',
    data: barChartData,
    options: barChartOptions
})
</script>
