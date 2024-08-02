
/*

an approach:

We do a payrun for each week, so the first thing we need is the finalised shift report for the week we are processing.
That looks like this:
* start_date
* start_time
* end_date
* end_time
* staff_id


Now we step through the shift report to get billing items for the shift
Note: a single shift can contain multiple billing items to cover different billing rates
* start_date
* start_time
* end_date
* end_time
* staff_id
* service_code

getSessionsForShift($shift){

}


What we should end up with is an array that we can generate timetracking sessions from and a payrun.
That needs to contain
* session_date
* start_date (this would be a new field)
* start_time (I haven't been using this)
<!-- 
I don't think I need these fields because I can calculate them from start time and duration.
* end_date (this would be a new field)
* end_time (I haven't been using this) 
-->
* session_duration (in minutes) // it is important this gets handled properley because sleep over shifts don't multiply the rate by this.
* staff_id
* service_id
* client_id

We need to create the sessions

As each shift is processed we should update houserosters with a field to indicate that the shift has been processed.  
this would lock the shift from changes or being processed again should the run be interrupted.


The existing invoicing system takes care of invoicing the client for the sessions.

We need to create the payroll items for each staff member




*/



            // $start = new \DateTime($item['start']);
            // $end = new \DateTime($item['end']);


            // $duration = $start->diff($end);
            // $minutes = $duration->h * 60 + $duration->i;

            // $result = [
            //     'staff_id' => $item['staff_id'],
            //     'client_id' => $client_id,
            //     'service_id' => $service_id,
            //     'session_date' => $item['start_date'],
            //     'start_time' => $item['start_time'],
            //     'session_duration' => $item['duration'],
            //     'notes' => "TEST SIL BILLING ITEM",
            // ];

            // $result = $item;


/*

$billingTable = [
    'Monday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL DAY"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL DAY"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL EVE"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Tuesday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL DAY"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL DAY"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL EVE"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Wednesday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL DAY"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL DAY"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL EVE"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Thursday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL DAY"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL DAY"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL EVE"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Friday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL DAY"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL DAY"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL EVE"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Saturday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL SAT"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL SAT"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL SAT"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Sunday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL SUN"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL SUN"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL SUN"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ],
    'Public Holiday' => [
      ["start"=>"06:00", "end"=>"14:00", "service_code"=>"SIL PUB"],
      ["start"=>"14:00", "end"=>"20:00", "service_code"=>"SIL PUB"],
      ["start"=>"20:00", "end"=>"22:00", "service_code"=>"SIL PUB"],
      ["start"=>"22:00", "end"=>"06:00", "service_code"=>"SIL SLEEP"]
    ]
  ];





        // // Output the results
        // foreach ($results as $result) {
        //     foreach ($result as $row) {
        //         echo "Start Date: {$row['start_date']} {$row['start_time']} :: ";
        //         echo "End Date: {$row['end_date']} {$row['end_time']} :: ";
        //         echo "Duration: {$row['duration']} :: ";
        //         echo "Staff ID: {$row['staff_id']} :: ";
        //         echo "Service Code: {$row['service_code']}";
        //         echo "\n";
        //     }
        // }

  */