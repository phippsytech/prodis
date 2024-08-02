
I'm considering a horizontal flow to the roster
This is the closest component to what I would like, but I'd need to modify some functionality to suit my purposes.
https://github.com/ANovokmet/svelte-gantt



Things to consider: 
1 row represents a participant.
It is possible in some setups for a staff member to be split across multiple participants.  In this case you would have the same staff member on both participants, and then indicate there is an allocation error, which can be resolved by indicating a percentage split, or by reallocating the the staff member to just on participant.

Within the 1 row representing a participant, there will be rows, that only indicate mutliple resources allocated at the same time.

For STA type billing, perhaps 1 row within the participant represents the billing type, so you allocate staff to the STA line, and then additional staff are allocated to the STA SCA line which is billed in a different manner.




