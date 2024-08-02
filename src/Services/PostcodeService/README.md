Importing Postcodes from Matthew Proctor’s Australian Postcode Database which includes the MMM 2019 used by NDIS.
https://matthewproctor.com/australian_postcodes#downloadlinks

Things to note:
isolated_towns_modification.json contains the data listed in the NDIS Price Guide
This guide is updated each year, and so should be reviewed each year to ensure modifications are current

There are new suburbs that are missing MMM, which will most likely be updated in future.
I have used the tool at https://www.health.gov.au/resources/apps-and-tools/health-workforce-locator/app#hwc-map
along with looking at the MMM codes of surrounding suburbs to take my best guess at the NDIS zone when it is missing.

the missing_mmm.sql file contains those guesses.  Before applying them to an imported dataset, you should check to see if
newer udpates to the postcode database contain a value, and use that (update the file to remove any missing_mmm fields that now exist)
