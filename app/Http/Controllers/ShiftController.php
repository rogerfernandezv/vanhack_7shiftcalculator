<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $punches = $this->getData('https://shiftstestapi.firebaseio.com/timePunches.json');
        $locations = $this->getData('https://shiftstestapi.firebaseio.com/locations.json');
        //max day time
        //max week time
        //mult_day
        //mult_week
        $users = $this->getData('https://shiftstestapi.firebaseio.com/users.json', $locations[0]['id'], $punches, $locations)->sortBy('firstName');

        return view('shift/index', compact('users'));
    }

    /**
     * Function to get from URL and parse the response
     *  
     *
     * @param String $url
     * @param String $key
     * @param String $punches
     * @return Collection
     */
    public function getData($url, $key=null, $punches=null, $loc=null)
    {
        $json = json_decode(file_get_contents($url), true);
        $datas = $key == null ? $json : $json[$key];

        $data_final = array();

        if($punches == null)
        {
            foreach($datas as $key=> $value)
            {
                array_push($data_final, $value);
            }
        }
        else
        {
            foreach($datas as $key=>$value)
            {
                $user = $value;
                $punches_user = $punches->where('userId', $user['id']);

                $user['detail'] = $this->calcWeek($punches_user, $user['hourlyWage']);
                $user['location'] = $loc->where('id', $user['locationId'])->first();

                array_push($data_final, $user);
            }
        }
        
        return collect($data_final);
    }

    /**
     * Calculate week daily overtime and payment
     *
     * @param array $dates
     * @param int $user_sal
     * @return array
     */
    public function calcWeek($dates, $user_sal)
    {
        $dates = $dates->sortBy('clockedIn');

        $time = [ 'total_time' => 0,
                    'total_paid' => 0, 
                    'week_overtime' => 0,
                    'day_overtime' => 0,
                    'total_overtime' => 0
                ];

        foreach($dates as $d)
        {
            $date_in = Carbon::parse($d['clockedIn']);
            $date_out = Carbon::parse($d['clockedOut']);

            if(!isset($weeks[$date_in->weekOfYear]) || !is_array($weeks[$date_in->weekOfYear] ) )
                $weeks[$date_in->weekOfYear] = array();

            array_push($weeks[$date_in->weekOfYear], $d);
        }
        
        foreach($weeks as $key=>$value)
        {
            $total_week = 0;
            $salary_mult = 0;
            $day_overtime  = 0;

            foreach($value as $day)
            {
                //$overtime_day = 0;

                $clock_in = \Carbon\Carbon::parse($day['clockedIn']);
                $clock_out = \Carbon\Carbon::parse($day['clockedOut']);

                if($clock_out > $clock_in)
                {
                    $diff = $clock_out->diffInMinutes($clock_in);
                    $time['total_time'] += $diff;
                    
                    $total_week += $diff;

                    if($diff > 480)
                    {
                        //$overtime_day += $diff - 480;
                        $salary_mult = 1.5;
                        $day_overtime += $diff - 480;
                    }
                }
            }
            if($total_week > 2400)
            {
                $salary_mult = 2;
                $time['week_overtime'] += $total_week - 2400;
            }
            else if($day_overtime > 0)
            {
                $time['day_overtime'] += $day_overtime;
            }
        }

        $total_overtime = $time['day_overtime'] + $time['week_overtime'];
        $sal = 0;

        $time['total_paid'] = (($time['total_time'] - $total_overtime)/60) * $user_sal;

        if($time['day_overtime'] > 0)
        {
            $time['total_paid'] += (($time['day_overtime']/60) * $user_sal) * 1.5;
        }

        if($time['week_overtime'] > 0)
        {
            $time['total_paid'] += (($time['week_overtime']/60) * $user_sal) * 2;
        }

        //round(1.95583, 2);
        if($time['total_paid'] > 0)
            $time['total_paid'] = round($time['total_paid'], 2);

        //convertMinuteToHours
        $time['week_overtime'] = $this->convertMinuteToHours($time['week_overtime']);
        $time['day_overtime'] = $this->convertMinuteToHours($time['day_overtime']);
        $time['total_time'] = $this->convertMinuteToHours($time['total_time']);
        $time['total_overtime'] = $this->convertMinuteToHours($total_overtime);

        return $time;
    }

    /**
     * @param int $min
     * @return String
     */
    public function convertMinuteToHours($min)
    {
        $time = floor($min / 60).'h';
        $time_min = ($min -   floor($min / 60) * 60);

        if($time_min > 0)
            $time .= $time_min . 'min';

        return $time;
    }

}
