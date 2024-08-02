Because we don't want to spend more than the employee's acrued leave we can't
add leve into xero until the pay is run.

Any future leave will affect the leave balance and make an employee

I need to use the leave balance at the time of payment to work out if the employee
has enough leave available or if I need to convert a portion of the leave to unpaid
leave.


How we do it:

* Is there leave for the current period?  No?  Move on.
* Is the leave annual leave?  If Annual Leave 5 weeks exists as a leave balance, use that.  Otherwise use standard Annual Leave.  (HARDCODE FOR NOW)
* Yes?  Get that leave
* Is the leave unpaid?  No checking needed.  Create the leave.
* Is the leave paid?  get the leave balance
* is there enough leave balance?
* create leave.  Delete leave from CRM (we're not using it for how leave was handled)
* if there isn't enough leave balance, work out how much leave there is
* create the leave for that and work out the remainder
* create unpaid leave for the remainder.


