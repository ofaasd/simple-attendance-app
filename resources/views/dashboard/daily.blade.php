<div class="row">
    <div class="col-md-6">
        <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
    <div class="col-md-6">
        <table class="table" id="example2">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>In</th>
                    <th>Late</th>
                    <th>Out</th>
                    <th>Overtime</th>

                </tr>
            </thead>
            <tbody>
                @foreach($user as $row)
                <tr>
                    <td>{{date('d-m-Y', strtotime($tanggal))}}</td>
                    <td>{{$row->name}}</td>
                    <td>{{!empty($list_presence[$row->id]->start) ? date('H:i:s', $list_presence[$row->id]->start) : '00:00:00'}}</td>
                    <td {{(!empty($list_presence[$row->id]->start_late) || empty($list_presence[$row->id]->start)) ? "class=bg-danger":"class=bg-success"}}>{{!empty($list_presence[$row->id]->start_late) ? date('H:i:s', $list_presence[$row->id]->start_late) : '00:00:00'}}</td>
                    <td>{{!empty($list_presence[$row->id]->end) ? date('H:i:s', $row->end) : '00:00:00'}}</td>
                    <td>{{(!empty($list_presence[$row->id]->overtime) && $list_presence[$row->id]->overtime == 1)?"Yes":"No"}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $('#example2').DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
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
