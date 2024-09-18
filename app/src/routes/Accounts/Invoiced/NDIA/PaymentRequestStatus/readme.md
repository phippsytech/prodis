<script>

</script>

Remittance

# create invoices for remittance items
- a remittance file contains multiple invoices
- all data for an invoice is completely within one remittance file.


pay_req_doc_date: 20230108
bulk_clm_ref: CC PRODA 1.CSV
prov_claim_ref: 430016635-1
item_ref: 11_022_0110_7_3
item_qty: 0.50
unit_price: 234.84
amount_paid: 117.42
participant_bp: 0430016635
participant_name: Ms. Kristen Kormah
support_start_date: 20221219
support_end_date: 20221219

claim_type: 
cancel_rsn: 


For each invoice I need to get:
Invoice Date: pay_req_doc_date
Invoice Due Date - calculate from invoice date
Invoice Number: prov_claim_ref
Reference: Look up client name from clients so invoice data is consistent.
Account Code (eg 200, 201).  I can get this from the servicebookings table.


Item Code: I can look this up from the services table.
For description I will use support_number, support_name, start date, claim type, cancel reason.  Claim type and cancel reason will come direct from the remittance data rather than doing any conversion.


# create payment batches for invoices 



# Reconcile payments against payment batches.

201
20101
20102
20103
20104
20105
20106