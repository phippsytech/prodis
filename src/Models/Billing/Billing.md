"NDISNumber",  // This is the client - NOTE: it is possible this field will be empty.


'session_date' => [v::numericVal()], // this is the same as start_date.  I need to decide which name I will keep
'start_date' => [v::stringType()],  //"SupportsDeliveredFrom",
'start_time' => [v::stringType()],
'end_date' => [v::stringType()], //"SupportsDeliveredTo",
'end_time' => [v::stringType()],

'duration' => [v::numericVal()],
'session_duration' => [v::numericVal()], // in mins

'actual_travel_time' => [v::numericVal()], // actual minutes travelled (not capped)

'kilometers_travelled' => [v::stringType()],  // 
'kms' => [v::numericVal()], // this is the shift version of kilometers travelled


"Quantity",
"UnitPrice", // the rate at time of billing


"SupportNumber",
'service_id' => [v::numericVal()], // this needs to become the support number


'staff_id' => [v::numericVal()],
'house_id' => [v::stringType()], // this needs to be replaced with client_id
'client_id' => [v::numericVal()],
'planmanager_id' => [v::numericVal()],  // who we are billing

'claim_type' => [v::stringType()],  //"ClaimType",
'cancellation_reason' => [v::stringType()],  //"CancellationReason"



'invoice_batch' => [v::optional(v::numericVal())],




// Fields irrelevant to billing
'notes' => [v::stringType()],  // long text
'shift_type' => [v::stringType()],
'group_ids' => [v::stringType()], 
'do_not_bill' => [v::boolVal()],  // if a shift is marked do not bill, it won't be added to billing.  It will be used in payruns


// Final field choices

'start_date' => [v::stringType()], // Required
'start_time' => [v::stringType()],
'end_date' => [v::stringType()],
'end_time' => [v::stringType()],
'quantity' => [v::numericVal()], // this is the duration for time based billing
'unit' => [v::stringType()],  // each, hours, minutes, kms // Does the support number define this?
"UnitPrice", // the rate at time of billing
"SupportNumber" => [v::stringType()],



// SHIFTS
staff_id
client_id
house_id
do_not_bill // flag to indicate if the client is billed for the shift.
'start_date' => [v::stringType()], // Required
'start_time' => [v::stringType()],
'end_date' => [v::stringType()],
'end_time' => [v::stringType()],
"kms" => [v::stringType()],
"SupportNumber" => [v::stringType()],




// BILLING
staff_id
client_id
planmanager_id
support_item_number
start_date
end_date
quantity 
unit
unit_price // rate at time of billing.
claim_type
cancellation_reason




to create an entry, I need:
start_date and duration
if duration is null then all other fields are required.




4050094011,431092290,2023-06-06,2023-06-06,11_022_0110_7_3,431092290-48,0.25,,234.83,P2,,,,NF2F,
4050094011,430396274,2023-06-05,2023-06-05,11_022_0110_7_3,430396274-48,1,,234.83,P2,,,,CANC,NSDH

4050094011,430662360,2023-06-05,2023-06-05,11_023_0110_7_3,430662360-48,0.5,,193.99,P2,,,,REPW,
4050094011,430662360,2023-06-06,2023-06-06,11_023_0110_7_3,430662360-48,1,,193.99,P2,,,,NF2F,
4050094011,432162574,2023-06-05,2023-06-05,11_023_0110_7_3,432162574-48,0.17,,193.99,P2,,,,NF2F,
4050094011,432162574,2023-06-06,2023-06-06,11_022_0110_7_3,432162574-48,1,,234.83,P2,,,,,
4050094011,430022940,2023-06-05,2023-06-05,11_023_0110_7_3,430022940-48,1,,193.99,P2,,,,,
4050094011,430587740,2023-06-05,2023-06-05,11_023_0110_7_3,430587740-48,0.08,,193.99,P2,,,,NF2F,
4050094011,430587740,2023-06-07,2023-06-07,11_023_0110_7_3,430587740-48,0.08,,193.99,P2,,,,NF2F,
4050094011,430531121,2023-06-05,2023-06-05,11_022_0110_7_3,430531121-48,0.5,,234.83,P2,,,,TRAN,
4050094011,430531121,2023-06-05,2023-06-05,11_022_0110_7_3,430531121-48,1.5,,234.83,P2,,,,,
4050094011,430893142,2023-06-05,2023-06-05,11_022_0110_7_3,430893142-48,0.5,,234.83,P2,,,,,
4050094011,430440841,2023-06-05,2023-06-05,11_022_0110_7_3,430440841-48,1.5,,234.83,P2,,,,,
4050094011,430440841,2023-06-05,2023-06-05,11_022_0110_7_3,430440841-48,0.5,,234.83,P2,,,,TRAN,
4050094011,431202371,2023-06-05,2023-06-05,11_023_0110_7_3,431202371-48,0.08,,193.99,P2,,,,NF2F,
4050094011,431202371,2023-06-06,2023-06-06,11_022_0110_7_3,431202371-48,2,,234.83,P2,,,,,
4050094011,431202371,2023-06-06,2023-06-06,11_023_0110_7_3,431202371-48,0.5,,193.99,P2,,,,TRAN,
4050094011,431202371,2023-06-06,2023-06-06,11_023_0110_7_3,431202371-48,0.08,,193.99,P2,,,,NF2F,
4050094011,431712893,2023-06-06,2023-06-06,11_022_0110_7_3,431712893-48,0.5,,234.83,P2,,,,NF2F,
4050094011,431639499,2023-06-05,2023-06-05,11_022_0110_7_3,431639499-48,0.17,,234.83,P2,,,,NF2F,
4050094011,430044239,2023-06-06,2023-06-06,11_022_0110_7_3,430044239-48,1.25,,234.83,P2,,,,NF2F,
