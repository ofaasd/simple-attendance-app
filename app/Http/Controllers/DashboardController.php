<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use App\Models\User;
use App\Models\WorkingHour;
use DateTime;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    //
    public function index_hr(){
        $month = array(1=>'January','February','March','April','May','June','July','August','September','Oktober','November','December');
        return view('dashboard2',compact('month'));
    }
    public function index(){

        return view('dashboard');
    }
    public function get_daily(Request $request){
        $tanggal = $request->tanggal;
        $tanggal_now = date('Y-m-d');
        $user = User::all();
        $list_presence = [];
        $total_on = 0;
        $total_late = 0;
        $total_not = 0;
        if($tanggal <= $tanggal_now){
            foreach($user as $row){
                $presence = Presence::select('presences.*','users.name')->join('users','users.id','=','user_id')->where('day',$tanggal)->where('user_id',$row->id)->first() ?? '';
                if(!empty($presence)){
                    if($presence->start_late == 0){
                        $total_on++;
                    }else{
                        $total_late++;
                    }
                }else{
                    $total_not++;
                }
                $list_presence[$row->id] = $presence;
            }
        }
        $total = $total_not . ',' . $total_late . ',' . $total_on;

        return view('dashboard.daily',compact('user','list_presence','tanggal','total'));
    }
    public function get_weekly(Request $request){
        $startOfWeek = date("Y-m-d", strtotime("Monday this week"));
        $day = '';
        $total_on2 = '';
        $total_late2 = '';
        $total_not2 = '';

        $total_on_week = 0;
        $total_late_week = 0;
        $total_not_week = 0;

        $user = User::all();
        for ($i=0; $i<7;$i++){
            $total_on = 0;
            $total_late = 0;
            $total_not = 0;
            $tanggal = date("Y-m-d", strtotime($startOfWeek . " + $i day"));
            $day .= "'" . date("l", strtotime($startOfWeek . " + $i day")) ."',";
            $tanggal_now = date('Y-m-d');
            if($tanggal <= $tanggal_now){
                foreach($user as $row){
                    $presence = Presence::select('presences.*','users.name')->join('users','users.id','=','user_id')->where('day',$tanggal)->where('user_id',$row->id)->first() ?? '';
                    if(!empty($presence)){
                        if($presence->start_late == 0){
                            $total_on++;
                        }else{
                            $total_late++;
                        }
                    }else{
                        $total_not++;
                    }
                }
            }
            $total_on2 .= $total_on . ',';
            $total_late2 .= $total_late . ',';
            $total_not2 .= $total_not . ',';

            $total_on_week += $total_on;
            $total_late_week += $total_late;
            $total_not_week += $total_not;
        }
        $total = $total_not_week . "," . $total_late_week . "," .$total_on_week;
        return view('dashboard.weekly',compact('day','total_on2','total_late2','total_not2','total'));
    }
    public function get_monthly(Request $request){
        $month = ($request->month < 10)?"0" . $request->month : $request->month;
        $year = $request->year;
        $date = $year . "-" . $month . "-1";
        $last_date = date("t", strtotime($date));
        $user = User::all();
        $date_chart = "";
        $total_on2 = '';
        $total_late2 = '';
        $total_not2 = '';
        $status = $request->status;
        for($i=1; $i<= $last_date; $i++){
            $total_on = 0;
            $total_late = 0;
            $total_not = 0;
            $tanggal = ($i < 10)?"0".$i:$i;
            $full_date = $year . "-" . $month . "-" . $tanggal;
            $tanggal_now = date('Y-m-d');
            if($full_date <= $tanggal_now){
                foreach($user as $row){
                    $presence = Presence::select('presences.*','users.name')->join('users','users.id','=','user_id')->where('day',$full_date)->where('user_id',$row->id)->first() ?? '';
                    if(!empty($presence)){
                        if($presence->start_late == 0){
                            $total_on++;
                        }else{
                            $total_late++;
                        }
                    }else{
                        $convert_hari = date('w',strtotime($full_date)) + 1;
                        $working = WorkingHour::where('user_id',$row->id)->where('days',$convert_hari)->where('working_start','<>',0)->count();
                        if($working > 0){
                            // echo $convert_hari . " ";
                            // echo $row->id . " " . $working . "<br />";
                            $total_not++;
                        }

                    }
                }
            }
            $total_on2 .= $total_on . ',';
            $total_late2 .= $total_late . ',';
            $total_not2 .= $total_not . ',';
            $date_chart .= $i . ",";
        }
        if($status == 1){
            $total = $total_on2;
        }elseif($status == 2 ){
            $total = $total_late2;
        }else{
            $total = $total_not2;
        }
        $list_status = array(1=>'On Time','Late','Not Absence');
        return view('dashboard.monthly',compact('date_chart','total','status','list_status'));
    }
}
