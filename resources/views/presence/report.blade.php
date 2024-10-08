<x-app-layout>
    <!-- {{strtolower($title)}} List Table -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$title}}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                    </ol>
                </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <section class="content">
            <div class="container-fluid">
              <!-- Small boxes (Stat box) -->
              <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <div class="card-title">
                                Filter
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                  <i class="fas fa-minus"></i>
                                </button>
                              </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="GET" action="">
                                        <div class="mb-2">
                                            <label for="date_start">Date Start</label>
                                            <input type="date" name="date_start" id="date_start" class="form-control" value="{{$date_start}}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="date_end">Date End</label>
                                            <input type="date" name="date_end" id="date_end" class="form-control" value="{{$date_end}}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                @foreach($list_status as $key=>$value)
                                                <option value="{{$key}}" {{($key==$status)?"selected":""}}>{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <input type="submit" value="Show" class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                {{$title}} {{$list_status[$status]}}
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x:scroll">
                            <table class="table" id="table-report">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        @foreach($period as $row)
                                            <th>{{$row->format("d-m-Y")}}</th>
                                        @endforeach
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = []; $grand_total = 0; @endphp
                                    @foreach($user as $row)
                                        @php $total[$row->id] = 0 @endphp
                                        <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$row->name}}</td>
                                            @foreach($period as $dt)
                                                <td>{{$jumlah[$dt->format("Y-m-d")][$row->id]}}</td>
                                                @php
                                                    $total[$row->id] += $jumlah[$dt->format("Y-m-d")][$row->id];
                                                    $grand_total += $jumlah[$dt->format("Y-m-d")][$row->id];
                                                @endphp
                                            @endforeach
                                            <th>{{$total[$row->id]}}</th>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan=2>Total</th>
                                        @foreach($period as $dt)
                                            <th>{{array_sum($jumlah[$dt->format("Y-m-d")])}}</th>
                                        @endforeach
                                        <th>{{$grand_total}}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
