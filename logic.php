<?php

Active_and_change_plan:
	Plan = demo ? : original
	If ( selected plan is different then setted one) {
		Set plan - expire all previous plans
			end_date = today + plan validity 
			if(invoice first to first){
				end_date = last_date of current month 
				// if we keep end_date = start_date then on invoice approve how to get itwas 1st to 1st (##) or
			}
			if(data reset value){
				if(first to first){
					reset_date = 1st of (today + reset duration)
				}else{
					reset_date = today + reset duration
				}
			}

			expire_date = end_date + garce period <== will be problem when invoice will be approved

	}

	if(create invoice) {
		create invoice - as on today (in draft/due )
		// (##) or manually set status to due to avoide approve activity if invoice created from here
		// on set status = due and save will update Trnsaction from afterSave hook in invoice
	}


Upcoming Invoices Create invoice: - shows user plan conditions order by user and end_date
	check if any previous invoice of this user is still to be approved ? just to be safe -- should be in user->createInvoice ??
	create invoice of same plan on the end_date of user_plan_condition  (in draft) [Plan details as per new data in plan]


Invoice Approved: 
	update end_date (non expired conditions of same plan in invoice) = current_end_date  + plan validity
	// (should end_date be start date when applied ?)
	update expire_date = new end_date + grace period
	// accounts entry


Created Manual Invoice
	On Approve ???