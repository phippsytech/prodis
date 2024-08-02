To create timesheets for employees in Xero, I need to know:
Employee ID
The earnings rate id for the billable shift they worked.
 - remember there are multiple levels of earnings rates, so you need to know which one to use.

Be careful of SIL SLEEP.  It should only be billed for 1 hour.  I can see it has been set up as rate per unit.

Earnings rate table looks a bit like this:

 SIL DAY
    1
    4
 SIL EVE
    1
    4
 SIL NIGHT
    1
    4
 SIL SAT
    1
    4
 SIL SUN
    1
    4
 SIL PUB
    1
    4
 SIL SLEEP
    1
    4


 so I should be able to get earings rate id like this:
 $earnings_rate[$service_code][$staff_level]

 