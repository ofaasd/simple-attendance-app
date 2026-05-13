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
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title mb-0">Attendance</h5>
                        </div>
                        <div class="col-md-4 text-right">
                            <div id="clock-wrapper"></div>
                        </div>
                    </div>

                </div>
                <div class="card-datatable card-body table-responsive">
                    <div id="overlay-place"></div>
                    <input type="hidden" name="page" id='page' value='absensi'>
                    <input type="hidden" name="title" id='title' value='Absensi'>
                    <form class="add-new-{{strtolower($title)}} pt-0" id="addNew{{$title}}Form" action="javascript:void(0)">
                      @csrf
                      <div class="mb-4">
                        <div class="row">
                          <div class="col-md-6">
                            <video id="my_camera" width="200" height="200" autoplay playsinline style="margin:auto;display:block;"></video>
                            <canvas id="snap_canvas" width="200" height="200" style="display:none;"></canvas>
                            <br/>
                            <input type="button" class="btn btn-success me-sm-3 me-1 data-submit" value="Take Picture" onClick="take_snapshot()">
                            <input type="hidden" name="image" class="image-tag">
                          </div>
                          <div class="col-md-6">
                            <br/>
                            <div id="results" style="text-align:center">Result Foto</div>

                            <input type="hidden" name="lat" id="lat" class="form-control" >
                            <input type="hidden" name="long" id="long" class="form-control" >
                            <input type="hidden" name="tanggal" id="tanggal" value="{{$tanggal}}" class="form-control" >

                            {{-- Info SPPG & status jarak --}}
                            @if(!empty($isAdmin) && $isAdmin)
                            <div class="mt-3 alert alert-success py-1 px-2" style="font-size:0.85rem;">
                                <i class="fas fa-user-shield"></i> Role admin: Anda dapat melakukan absen di lokasi mana pun.
                            </div>
                            @elseif($sppg && $sppg->lat && $sppg->lng)
                            <div class="mt-3">
                                <small class="text-muted">SPPG: <strong>{{$sppg->nama}}</strong></small><br>
                                <div id="location-status" class="alert alert-warning mt-1 py-1 px-2" style="font-size:0.85rem;">
                                    <i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi Anda...
                                </div>
                            </div>
                            @else
                            <div class="mt-3 alert alert-info py-1 px-2" style="font-size:0.85rem;">
                                <i class="fas fa-info-circle"></i> SPPG belum dikonfigurasi untuk akun Anda. Hubungi admin.
                            </div>
                            @endif
                          </div>

                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12 text-center">
                          @if(empty($absensi->start))
                          <button type="submit" id="btn-absen" class="btn btn-primary me-sm-3 me-1 data-submit" {{(!empty($isAdmin) && $isAdmin) ? '' : 'disabled'}}>Attendance In</button>
                          @elseif(empty($absensi->end))
                          <button type="submit" id="btn-absen" class="btn btn-danger me-sm-3 me-1 data-submit" {{(!empty($isAdmin) && $isAdmin) ? '' : 'disabled'}}>Attendance Out</button>
                          @else
                          <button type="submit" id="btn-absen" class="btn btn-primary me-sm-3 me-1 data-submit" disabled>Already Attendance</button>
                          @endif
                        </div>
                      </div>
                    </form>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">Log Attendance</div>
                        <div class="col-md-6 text-right">
                            <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#filterModal">Filter</a>
                      </div>
                    </div>
                </div>
                <div class="card-body">
                  <table class="table datatables">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>In</th>
                        <th>Late</th>
                        <th>Out</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($list_absensi as $row)
                        <tr>
                          <td>{{date('d-m-Y', strtotime($row->day))}}</td>
                          <td >{{!empty($row->start) ? date('H:i:s', $row->start) : ''}}</td>
                          <td {{!empty($row->start_late) ? "class=bg-danger":"class=bg-success"}}>{{!empty($row->start_late) ? date('H:i:s', $row->start_late) : '00:00:00'}}</td>
                          <td>{{!empty($row->end) ? date('H:i:s', $row->end) : '00:00:00'}}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
    </section>
</div>
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <form action="{{url('attendance')}}" method="GET">
            @csrf
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Filter Date</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="date_start">Date Start : </label>
                    <input type="date" name="date_start" class="form-control" id="date_start" value="{{empty($date_start)?date('Y-m'). '-01':$date_start}}">
                </div>
                <div class="form-group">
                    <label for="date_start">Date End : </label>
                    <input type="date" name="date_end" class="form-control" id="date_end" value="{{empty($date_end)?date('Y-m-d'):$date_end}}">
                </div>

            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
        </form>
    </div>
</div>
<script>
    let _cameraStream = null;
    const IS_ADMIN = {{ (!empty($isAdmin) && $isAdmin) ? 'true' : 'false' }};

    // Koordinat SPPG dari server
    @if($sppg && $sppg->lat && $sppg->lng)
    const SPPG_LAT = {{ $sppg->lat }};
    const SPPG_LNG = {{ $sppg->lng }};
    const SPPG_NAMA = {!! json_encode($sppg->nama) !!};
    const MAX_RADIUS = 50; // meter
    @else
    const SPPG_LAT = null;
    const SPPG_LNG = null;
    const SPPG_NAMA = null;
    const MAX_RADIUS = 50;
    @endif

    // Haversine distance (meter)
    function haversineDistance(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLng/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function updateLocationStatus(lat, lng) {
        $("#lat").val(lat);
        $("#long").val(lng);

        if (IS_ADMIN) {
            @if(empty($absensi->start) || empty($absensi->end))
            $("#btn-absen").prop('disabled', false);
            @endif
            return;
        }

        @if(!($sppg && $sppg->lat && $sppg->lng))
        // Tidak ada SPPG, biarkan tombol tetap disabled
        return;
        @endif

        @if(!empty($absensi->start) && !empty($absensi->end))
        // Sudah absen penuh, tombol tetap disabled
        return;
        @endif

        if (SPPG_LAT === null) return;

        const dist = haversineDistance(SPPG_LAT, SPPG_LNG, parseFloat(lat), parseFloat(lng));
        const distRounded = Math.round(dist);
        const statusEl = document.getElementById('location-status');

        if (dist <= MAX_RADIUS) {
            if (statusEl) {
                statusEl.className = 'alert alert-success mt-1 py-1 px-2';
                statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Anda dalam jangkauan SPPG <strong>' + SPPG_NAMA + '</strong> (' + distRounded + ' m). Absen diizinkan.';
            }
            $("#btn-absen").prop('disabled', false);
        } else {
            if (statusEl) {
                statusEl.className = 'alert alert-danger mt-1 py-1 px-2';
                statusEl.innerHTML = '<i class="fas fa-times-circle"></i> Anda berada <strong>' + distRounded + ' m</strong> dari SPPG <strong>' + SPPG_NAMA + '</strong>. Maksimal jarak: ' + MAX_RADIUS + ' m.';
            }
            $("#btn-absen").prop('disabled', true);
        }
    }

    document.addEventListener("DOMContentLoaded", function(event) {
      const baseUrl = '{!! url("") !!}';
      $(".datatables").DataTable();

      const video = document.getElementById('my_camera');
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
          navigator.mediaDevices.getUserMedia({ video: true, audio: false })
              .then(function(stream) {
                  _cameraStream = stream;
                  video.srcObject = stream;
              })
              .catch(function(err) {
                  let msg = 'Tidak dapat mengakses kamera.';
                  if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                      msg = 'Akses kamera ditolak. Harap izinkan akses kamera di browser Anda.';
                  } else if (err.name === 'NotFoundError') {
                      msg = 'Kamera tidak ditemukan pada perangkat ini.';
                  } else if (err.name === 'NotSupportedError' || location.protocol !== 'https:' && location.hostname !== 'localhost') {
                      msg = 'Kamera hanya dapat diakses melalui koneksi HTTPS atau localhost.';
                  }
                  document.getElementById('my_camera').outerHTML = '<div id="my_camera" class="alert alert-danger" style="width:200px;margin:auto;">'+msg+'</div>';
              });
      } else {
          video.outerHTML = '<div id="my_camera" class="alert alert-danger" style="width:200px;margin:auto;">Browser Anda tidak mendukung akses kamera. Gunakan HTTPS atau browser yang lebih baru.</div>';
      }
      $('#addNew{{$title}}Form').submit(function(e) {

            e.preventDefault();
            $("#overlay-place").html(`<div class="overlay">
                <i class="fas fa-2x fa-sync fa-spin"></i>
            </div>`);
            const lat = $("#lat").val();
            const long = $("#long").val();
            if((lat && long) || IS_ADMIN){

                const url = ''.concat(baseUrl).concat('/attendance');
                // alert(url);
                $.ajax({
                    data: $('#addNew{{$title}}Form').serialize(),
                    url: url,
                    type: 'POST',
                    success: function success(status) {
                        // sweetalert
                        //$("#overlay-place").html('<div class="alert alert-success">Data Saved !</div>');
                        Swal.fire({
                        icon: 'success',
                        title: 'Successfully '.concat(status.nama, ' Updated !'),
                        text: ''.concat('Absensi ', ' ').concat(' Updated Successfully.'),
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                        });
                        location.reload();
                    },
                    error: function error(xhr) {
                        $("#overlay-place").html('');
                        let msg = 'Please take a picture first';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                        title: 'Data Not Saved',
                        text: msg,
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                        });
                    }
                });
            }else{
                Swal.fire({
                    title: 'Longitude and Latitude not found',
                    text: ' Please allow location from your browser. Or Take Picture First',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });

                $("#overlay-place").html(`<div class="alert alert-danger">Longitude and Latitude not found. please allow it from your browser. Or Take Picture First</div>`);
            }
      });

      setInterval(function() {
            var date = new Date();
            $('#clock-wrapper').html(
                date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear() + " " +
                date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds()
                );
        }, 500);

      // Watch geolocation secara terus-menerus untuk update status jarak
      if (navigator.geolocation) {
          navigator.geolocation.watchPosition(
              function(pos) {
                  updateLocationStatus(pos.coords.latitude, pos.coords.longitude);
              },
              function(err) {
                  const statusEl = document.getElementById('location-status');
                  if (statusEl) {
                      statusEl.className = 'alert alert-danger mt-1 py-1 px-2';
                      statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Gagal mendapatkan lokasi: ' + err.message + '. Harap izinkan akses lokasi.';
                  }
                  if (!IS_ADMIN) {
                      $("#btn-absen").prop('disabled', true);
                  }
              },
              { enableHighAccuracy: true, maximumAge: 5000, timeout: 15000 }
          );
      } else {
          const statusEl = document.getElementById('location-status');
          if (statusEl) {
              statusEl.className = 'alert alert-danger mt-1 py-1 px-2';
              statusEl.innerHTML = '<i class="fas fa-times-circle"></i> Browser tidak mendukung geolocation.';
          }
          if (!IS_ADMIN) {
              $("#btn-absen").prop('disabled', true);
          }
      }
    });
    const res = document.getElementById('results');
    function take_snapshot() {
      const video = document.getElementById('my_camera');
      const canvas = document.getElementById('snap_canvas');
      if (!video || !video.srcObject) {
          Swal.fire({ title: 'Kamera tidak aktif', text: 'Harap izinkan akses kamera terlebih dahulu.', icon: 'error' });
          return;
      }
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, 200, 200);
      const data_uri = canvas.toDataURL('image/jpeg', 0.9);
      $(".image-tag").val(data_uri);
      res.innerHTML = '<img src="'+data_uri+'" align="center" width="200" height="200" />';
    }
  </script>
</x-app-layout>


