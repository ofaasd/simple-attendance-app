<x-app-layout>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <section class="content">
            <div class="container-fluid">
              <!-- Small boxes (Stat box) -->
              <div class="row">
                <div class="card col-md-12">
                    <div class="card-header">
                        Welcome to Dashboard
                    </div>
                    <div class="card-content">

                    </div>
                </div>
                <div class="card card-primary col-md-12">
                    <div class="card-header">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-md-6">
                                    Daily Report
                                </div>
                                <div class="col-md-6 text-right">
                                    <input type="date" name="daily_date" class="form-control" id="daily_date" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                    </div>
                    <div class="card-body" id="daily-content">

                    </div>
                </div>
                <div class="card card-info col-md-12">
                    <div class="card-header">
                        <div class="card-title">
                            Weekly Report

                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                    </div>
                    <div class="card-body" id="weekly-content">

                    </div>
                </div>
                <div class="card card-success col-md-12">
                    <div class="card-header">

                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-inline">
                                    <span class="mr-sm-2">Monthly Report</span>
                                    <select name="status" class="form-control mb-2 mr-sm-2" id="status">
                                        <option value="1">On Time</option>
                                        <option value="2">Late</option>
                                        <option value="3">Not Absence</option>
                                    </select>
                                    <select name="month" class="form-control mb-2 mr-sm-2" id="month">
                                        @foreach($month as $key=>$value)
                                        <option value="{{$key}}" {{(date('m') == $key)?"selected":""}}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                    <select name="year" class="form-control mb-2 mr-sm-2" id="year">
                                        @for($i=date('Y');  $i >= (date('Y')-5); $i--)
                                            <option value="{{$i}}" {{(date('Y') == $i)?"selected":""}}>{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fas fa-minus"></i>
                                    </button>
                                  </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-body" id="monthly-content">

                    </div>
                </div>
              </div>
            </div>
        </section>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $("#daily_date").change(function(){
                get_daily();
            });
            $("#status").change(function(){
                get_monthly();
            });
            $("#month").change(function(){
                get_monthly();
            });
            $("#year").change(function(){
                get_monthly();
            });
            get_daily();
            get_weekly();
            get_monthly();
        });
        function get_daily(){
            $("#overlay-place").html(`<div class="overlay">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`);
            const tanggal = $("#daily_date").val();
            const url = "{!! url("dashboard/get_daily") !!}";
            $.get(url,{tanggal:tanggal},(data)=>{
                $("#daily-content").html(data);
            });
        }
        function get_weekly(){
            $("#weekly-content").html(`<div class="overlay">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`);
            const url = "{!! url("dashboard/get_weekly") !!}";
            $.get(url,(data)=>{
                $("#weekly-content").html(data);
            });
        }
        function get_monthly(){
            $("#monthly-content").html(`<div class="overlay">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>`);
            const url = "{!! url("dashboard/get_monthly") !!}";
            const status = $("#status").val();
            const month = $("#month").val();
            const year = $("#year").val();
            $.get(url,{status:status,month:month,year:year},(data)=>{
                $("#monthly-content").html(data);
            });
        }
    </script>
</x-app-layout>
