<?php

namespace App\Http\Services;

use App\Models\Lead;

class LeadService {
    public function ResetTimeOff() {
        $leads = Lead::where('status', '=', true)->get();

        for($i = 0; $i < count($leads); $i++) {
            $leads[$i]->time_off_interactions += 1;
            
            $time = $leads[$i]->time_off_interactions;

            if($time > 1 && $leads[$i]->reputation > -1.0)
                $leads[$i]->reputation -= 0.02;

            if($time <= -3 && $leads[$i]->reputation < 1.0)
                $leads[$i]->reputation += 0.02;

            $leads[$i]->save();
        }
    } 
}