Trips

Managing Provider Travel Data

There are two components to adding a trip

* recording the trip
* billing the trip

Recording the trip records a single trip record that contains the details of the trip required for billing


billing the trip creates 
* a timetracking record for the duration of the trip.
* a timetracking record for the distance of the trip.

What we need to know when recording the trip

staff_id
trip date
client id
service id to record trip duration against
service id to record trip distance against.
plan manager id
vehicle type

kms
duration
purpose


function tripBillingAddDistance(){

}

function tripBillingAddDuration(){

}

function tripBillingUpdateDistance(){

}

function tripBillingUpdateDuration(){
    
}





When a trip is updated, the billing needs to be recalculated and the timetracking records need to be updated.

when this data is updated I need to be able to identify the record used for duration and the record used for distance.
distance = has the trip_id and unit_type =kms
duration = has the trip_id and unit_type != kms


When a trip is deleted, the timetracking records also need to be deleted.
* a trip cannot be deleted if it has been billed without first reversing the billing.

