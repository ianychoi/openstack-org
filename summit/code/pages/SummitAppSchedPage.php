<?php

class SummitAppSchedPage extends SummitPage {

    public function renderSchedule($events) {

        $ordered_events = array();

        foreach($events as $event) {
            $day = date('Ymd',strtotime($event->StartDate));
            $hour = date('His',strtotime($event->StartDate));

            if (!isset($ordered_events[$day])) {
                $ordered_events[$day] = array();
                $event->FirstOfDay = true;
            }

            if (!isset($ordered_events[$day][$hour])) {
                $ordered_events[$day][$hour] = array();
                $event->FirstOfHour = true;
            }

            $ordered_events[$day][$hour][] = $event;
        }

        $viewable_schedule = new ArrayList();
        foreach ($ordered_events as $day_array) {
            $hours_arraylist = new ArrayList();
            foreach ($day_array as $hour_array) {
                $events_arraylist = new ArrayList($hour_array);
                $hours_arraylist->push($events_arraylist);
            }
            $viewable_schedule->push($hours_arraylist);
        }

        return $this->renderWith('SummitAppSchedPage_schedule',array('Schedule'=>$viewable_schedule));
    }

    
}


class SummitAppSchedPage_Controller extends SummitPage_Controller {

    public function init() {
        
        $this->top_section = 'full';
        parent::init();

        Requirements::javascript("summit/javascript/summitapp-schedule.js");
        Requirements::css("summit/css/summitapp-schedule.css");
	}
	
}