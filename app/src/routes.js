import { wrap } from 'svelte-spa-router/wrap'
import Logout from '@app/routes/Logout.svelte'
import NotFound from '@app/routes/NotFound.svelte'
import { replace } from 'svelte-spa-router'

export default {

    '/opensesame': wrap({
        asyncComponent: () => import('./routes/OpenSesame.svelte')
    }),

    '/auth/:token': wrap({
        asyncComponent: () => import('./routes/Auth.svelte')
    }),


    // '/leave': wrap({
    //     asyncComponent: () => import('./routes/Leave/Leave.svelte'),
    // }),

    // '/leave/apply': wrap({
    //     asyncComponent: () => import('./routes/Leave/Apply.svelte'),
    // }),




    '/bug': wrap({
        asyncComponent: () => import('./routes/Bug/BugForm.svelte'),
    }),


    '/tasks': wrap({
        asyncComponent: () => import('./routes/Task/Tasks.svelte'),
    }),

    '/tasks/add': wrap({
        asyncComponent: () => import('./routes/Task/AddTask.svelte')
    }),

    '/tasks/addtime': wrap({
        asyncComponent: () => import('./routes/Task/AddTime.svelte')
    }),

    '/tasks/:task_id': wrap({
        asyncComponent: () => import('./routes/Task/Task.svelte')
    }),



    '/trips': wrap({
        asyncComponent: () => import('./routes/Trips/AddTrip.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/add')
            }
        ]
    }),

    '/trips/add': wrap({
        asyncComponent: () => import('./routes/Trips/AddTrip.svelte')
    }),

    '/trips/history': wrap({
        asyncComponent: () => import('./routes/Trips/History.svelte')
    }),

    '/trips/history/:staff_id': wrap({
        asyncComponent: () => import('./routes/Trips/History.svelte')
    }),


    '/trips/kms': wrap({
        asyncComponent: () => import('./routes/Trips/KMs.svelte')
    }),

    '/trips/settings': wrap({
        asyncComponent: () => import('./routes/Trips/Settings.svelte')
    }),

    '/trips/:trip_id': wrap({
        asyncComponent: () => import('./routes/Trips/EditTrip.svelte')
    }),


    '/stakeholder': wrap({
        asyncComponent: () => import('./routes/Stakeholder/Stakeholder.svelte')
    }),

    '/chat': wrap({
        asyncComponent: () => import('./routes/Chat/Chat.svelte')
    }),


    '/admin': wrap({
        asyncComponent: () => import('./routes/Admin/Default.svelte')
    }),


    // '/meetingnotes/:meetingnote_id': wrap({
    //     asyncComponent: () => import('./routes/MeetingNotes/MeetingNotes.svelte'),
    //     conditions: [
    //         (detail) => {
    //             replace(detail.location+'/details')
    //         }
    //     ]
    // }),

    '/meetingnotes': wrap({
        asyncComponent: () => import('./routes/MeetingNotes/MeetingNotes.svelte')
    }),
    '/meetingnotes/add': wrap({
        asyncComponent: () => import('./routes/MeetingNotes/AddMeetingNote.svelte')
    }),

    '/meetingnotes/:meetingnote_id': wrap({
        asyncComponent: () => import('./routes/MeetingNotes/MeetingNote.svelte')
    }),






    '/billing': wrap({
        asyncComponent: () => import('./routes/Billing/Unbilled.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/unbilled')
            }
        ]
    }),

    '/billing/unbilled': wrap({
        asyncComponent: () => import('./routes/Billing/Unbilled.svelte')
    }),
    '/billing/billed': wrap({
        asyncComponent: () => import('./routes/Billing/Billed.svelte')
    }),


    '/billing/sil': wrap({
        asyncComponent: () => import('./routes/Billing/SIL.svelte')
    }),


    '/billing/add2': wrap({
        asyncComponent: () => import('./routes/Billing/DataEntry/Add2.svelte')
    }),



    '/credentials': wrap({
        asyncComponent: () => import('./routes/Credentials/List.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/list')
            }
        ]
    }),



    '/credentials/add': wrap({
        asyncComponent: () => import('./routes/Credentials/Add.svelte')
    }),

    '/credentials/rules': wrap({
        asyncComponent: () => import('./routes/Credentials/Rules.svelte')
    }),

    '/credentials/list': wrap({
        asyncComponent: () => import('./routes/Credentials/List.svelte')
    }),

    '/credentials/verify': wrap({
        asyncComponent: () => import('./routes/Credentials/Verify.svelte')
    }),

    '/credentials/missing': wrap({
        asyncComponent: () => import('./routes/Credentials/Missing.svelte')
    }),

    '/credentials/expired': wrap({
        asyncComponent: () => import('./routes/Credentials/Expired.svelte')
    }),

    '/credentials/due': wrap({
        asyncComponent: () => import('./routes/Credentials/Due.svelte')
    }),

    '/credentials/report': wrap({
        asyncComponent: () => import('./routes/Credentials/Report.svelte')
    }),

    '/credentials/:credential_id': wrap({
        asyncComponent: () => import('./routes/Credentials/Edit.svelte')
    }),

    '/expenses': wrap({
        asyncComponent: () => import('./routes/Expenses/Expenses.svelte')
    }),


    '/profile': wrap({
        asyncComponent: () => import('./routes/Profile/Profile.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/details')
            }
        ]
    }),


    '/profile/details': wrap({
        asyncComponent: () => import('./routes/Profile/Profile.svelte'),
    }),

    '/profile/payroll': wrap({
        asyncComponent: () => import('./routes/Profile/Payroll.svelte'),
    }),

    '/profile/payslip': wrap({
        asyncComponent: () => import('./routes/Profile/Payslip.svelte'),
    }),

    '/profile/leave': wrap({
        asyncComponent: () => import('./routes/Profile/Leave/Leave.svelte'),
    }),



    '/sysadmin': wrap({
        asyncComponent: () => import('./routes/SysAdmin/SysAdmin.svelte'),
    }),

    '/staff': wrap({
        asyncComponent: () => import('./routes/Staff/Staff.svelte'),
    }),


    '/staff/add': wrap({
        asyncComponent: () => import('./routes/Staff/Add.svelte')
    }),

    '/staff/:staff_id': wrap({
        asyncComponent: () => import('./routes/Staff/Staffer.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/details')
            }
        ]
    }),

    '/staff/:staff_id/details': wrap({
        asyncComponent: () => import('./routes/Staff/Staffer.svelte')
    }),

    '/staff/:staff_id/credentials': wrap({
        asyncComponent: () => import('./routes/Staff/Credentials/Credentials.svelte')
    }),

    '/staff/:staff_id/availability': wrap({
        asyncComponent: () => import('./routes/Staff/Availability/Availability.svelte')
    }),
    '/staff/:staff_id/clients': wrap({
        asyncComponent: () => import('./routes/Staff/Clients/Clients.svelte')
    }),
    '/staff/:staff_id/shifts': wrap({
        asyncComponent: () => import('./routes/Staff/Shifts/Shifts.svelte')
    }),

    '/staff/:staff_id/schedule': wrap({
        asyncComponent: () => import('./routes/Staff/Schedule/Schedule.svelte')
    }),


    '/staff/:staff_id/payroll': wrap({
        asyncComponent: () => import('./routes/Staff/Payroll/Payroll.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/details')
            }
        ]
    }),

    '/staff/:staff_id/payroll/details': wrap({
        asyncComponent: () => import('./routes/Staff/Payroll/Payroll.svelte')
    }),

    '/staff/:staff_id/payroll/paytemplate': wrap({
        asyncComponent: () => import('./routes/Staff/Payroll/PayTemplate.svelte')
    }),




    '/staff/:staff_id/billables': wrap({
        asyncComponent: () => import('./routes/Staff/Billing/Unbilled.svelte')
    }),


    '/staff/:staff_id/team': wrap({
        asyncComponent: () => import('./routes/Staff/Team/Team.svelte')
    }),


    '/staff/:staff_id/invoiced': wrap({
        asyncComponent: () => import('./routes/Staff/Billing/Billed.svelte')
    }),





    '/payroll': wrap({
        asyncComponent: () => import('./routes/Payroll/Payruns.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/payruns')
            }
        ]
    }),

    '/payroll/payruns/:payrun_id': wrap({
        asyncComponent: () => import('./routes/Payroll/Payrun/Payrun.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/payrun')
            }
        ]
    }),

    '/payroll/payruns/:payrun_id/payrun': wrap({
        asyncComponent: () => import('./routes/Payroll/Payrun/Payrun.svelte')
    }),



    '/payroll/payruns/:payrun_id/adjustments': wrap({
        asyncComponent: () => import('./routes/Payroll/Payrun/Adjustments.svelte')
    }),

    '/payroll/payruns/:payrun_id/salarypackaging': wrap({
        asyncComponent: () => import('./routes/Payroll/Payrun/SalaryPackaging/SalaryPackaging.svelte')
    }),


    '/payroll/payruns/:payrun_id/payslips/:payslip_id': wrap({
        asyncComponent: () => import('./routes/Payroll/Payrun/Payslip/Payslip.svelte')
    }),


    '/payroll/payruns': wrap({
        asyncComponent: () => import('./routes/Payroll/Payruns.svelte')
    }),



    '/payroll/payrun/payslip/:payslip_id': wrap({
        asyncComponent: () => import('./routes/Payroll/Payrun/Payslip/Payslip.svelte')
    }),







    '/payroll/staff': wrap({
        asyncComponent: () => import('./routes/Payroll/Staff/Staff.svelte')
    }),

    '/payroll/staff/:staff_id': wrap({
        asyncComponent: () => import('./routes/Payroll/Staff/Staffer.svelte')
    }),



    '/payroll/leave': wrap({
        asyncComponent: () => import('./routes/Payroll/Leave/Requests.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/requests')
            }
        ]
    }),

    '/payroll/leave/requests': wrap({
        asyncComponent: () => import('./routes/Payroll/Leave/Requests.svelte')
    }),
    '/payroll/leave/approved': wrap({
        asyncComponent: () => import('./routes/Payroll/Leave/Approved.svelte')
    }),
    '/payroll/leave/processed': wrap({
        asyncComponent: () => import('./routes/Payroll/Leave/Processed.svelte')
    }),




    '/payroll/paygrades': wrap({
        asyncComponent: () => import('./routes/Payroll/PayGrades/PayGrades.svelte')
    }),



    '/payroll/paygrades/add': wrap({
        asyncComponent: () => import('./routes/Payroll/PayGrades/Add.svelte')
    }),

    '/payroll/paygrades/:paygrade_id': wrap({
        asyncComponent: () => import('./routes/Payroll/PayGrades/Edit.svelte')
    }),



    '/payroll/breakdown': wrap({
        asyncComponent: () => import('./routes/Payroll/Breakdown/Breakdown.svelte')
    }),



    '/documenttypes': wrap({
        asyncComponent: () => import('./routes/DocumentTypes/List.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/list')
            }
        ]
    }),

    '/documenttypes/add': wrap({
        asyncComponent: () => import('./routes/DocumentTypes/Add.svelte')
    }),

    '/documenttypes/list': wrap({
        asyncComponent: () => import('./routes/DocumentTypes/List.svelte')
    }),



    '/documenttypes/:document_type_id': wrap({
        asyncComponent: () => import('./routes/DocumentTypes/Edit.svelte')
    }),
























    '/billables': wrap({
        asyncComponent: () => import('./routes/Billables/Add.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/add')
            }
        ]
    }),

    '/billables/billed': wrap({
        asyncComponent: () => import('./routes/Billables/Billed.svelte')
    }),

    '/billables/unbilled': wrap({
        asyncComponent: () => import('./routes/Billables/Unbilled.svelte')
    }),

    '/billables/add': wrap({
        asyncComponent: () => import('./routes/Billables/Add.svelte')
    }),

    '/billables/:id': wrap({
        asyncComponent: () => import('./routes/Billables/Edit.svelte')
    }),


    '/therapy': wrap({
        asyncComponent: () => import('./routes/Therapy/Therapy.svelte')
    }),
    '/therapy/kpi': wrap({
        asyncComponent: () => import('./routes/Therapy/KPI.svelte')
    }),



    '/sil': wrap({
        asyncComponent: () => import('./routes/SIL/SIL.svelte')
    }),

    '/sil/rosterofcares': wrap({
        asyncComponent: () => import('./routes/SIL/RosterOfCares.svelte')
    }),

    '/sil/rosters': wrap({
        asyncComponent: () => import('./routes/SIL/Rosters/Rosters.svelte')
    }),

    '/sil/formsummary': wrap({
        asyncComponent: () => import('./routes/SIL/FormSummary.svelte')
    }),

    '/sil/filteredformsummary': wrap({
        asyncComponent: () => import('./routes/SIL/FilteredFormSummary.svelte')
    }),



    '/sil/rosterofcares/:rosterofcare_id': wrap({
        asyncComponent: () => import('./routes/SIL/RosterOfCare/RosterOfCare.svelte'),
    }),


    '/timelog': wrap({
        asyncComponent: () => import('./routes/TimeLog/TimeLog.svelte')
    }),




    '/logout': Logout,


    '/': wrap({
        asyncComponent: () => import('./routes/Menu.svelte')
    }),

    '/directory': wrap({
        asyncComponent: () => import('./routes/Directory/Directory.svelte')
    }),

    '/clients': wrap({
        asyncComponent: () => import('./routes/Clients/Clients.svelte')
    }),

    '/clients/add': wrap({
        asyncComponent: () => import('./routes/Clients/Add.svelte')
    }),


    '/clients/:client_id': wrap({
        asyncComponent: () => import('./routes/Clients/Details/Details.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/details')
            }
        ]
    }),


    '/clients/:client_id/details': wrap({
        asyncComponent: () => import('./routes/Clients/Details/Details.svelte')
    }),


    '/clients/:client_id/forms': wrap({
        asyncComponent: () => import('./routes/Clients/Forms/Forms.svelte')
    }),

    '/clients/:client_id/forms/:form': wrap({
        asyncComponent: () => import('./routes/Clients/Forms/Form.svelte')
    }),


    // '/medication/add': HouseAddMedication,

    // '/clients/:client_id/details': HouseDetails,






    '/clients/:client_id/casenotes': wrap({
        asyncComponent: () => import('./routes/Clients/CaseNotes/CaseNotes.svelte')
    }),

    '/clients/:client_id/casenotes/add': wrap({
        asyncComponent: () => import('./routes/Clients/CaseNotes/Add.svelte')
    }),


    '/clients/:client_id/casenotes/:casenote_id': wrap({
        asyncComponent: () => import('./routes/Clients/CaseNotes/CaseNote.svelte')
    }),


    // '/clients/:client_id/schedule': wrap({
    //     asyncComponent: () => import('./routes/House/Schedule/Schedule.svelte')
    // }),

    '/clients/:client_id/roster': wrap({
        asyncComponent: () => import('./routes/Clients/Roster/Roster.svelte')
    }),
    // '/roster/template': HouseRosterTemplate,
    // '/roster/shifts': HouseRosterShifts,
    // '/roster/shifts/:shift_id': HouseRosterEditShift,

    '/clients/:client_id/staff': wrap({
        asyncComponent: () => import('./routes/Clients/Staff/Staff.svelte')
    }),

    '/clients/:client_id/staff/add': wrap({
        asyncComponent: () => import('./routes/Clients/Staff/Add.svelte')
    }),

    // '/clients/:client_id/staff/:staff': wrap({
    //     asyncComponent: () => import('./routes/Clients/Staff/Edit.svelte')
    // }),

    '/clients/:client_id/timeline': wrap({
        asyncComponent: () => import('./routes/Clients/Timeline/Timeline.svelte')
    }),

    '/clients/:client_id/timeline/export': wrap({
        asyncComponent: () => import('./routes/Clients/Timeline/Export.svelte')
    }),

    '/clients/:client_id/timeline/:form_id': wrap({
        asyncComponent: () => import('./routes/Clients/Forms/View.svelte')
    }),

    // '/clients/:client_id/billing': wrap({
    //     asyncComponent: () => import('./routes/Clients/Billable/Billable.svelte'),
    //     conditions: [
    //         (detail) => {
    //             replace(detail.location+'/unbilled')
    //         }
    //     ]
    // }),


    '/clients/:client_id/billables': wrap({
        asyncComponent: () => import('./routes/Clients/Billables/Billables.svelte'),
    }),




    '/clients/:client_id/invoices': wrap({
        asyncComponent: () => import('./routes/Clients/Invoices/Invoices.svelte')
    }),

    '/clients/:client_id/activities': wrap({
        asyncComponent: () => import('./routes/Clients/ActivityHistory/ActivityHistory.svelte')
    }),

    '/clients/:client_id/settings': wrap({
        asyncComponent: () => import('./routes/Clients/Settings/Settings.svelte')
    }),

    '/settings': wrap({
        asyncComponent: () => import('./routes/Settings/Settings.svelte')
    }),

    '/settings/supportcatalogue': wrap({
        asyncComponent: () => import('./routes/Settings/SupportCatalogue/SupportCatalogue.svelte')
    }),


    '/settings/accounting': wrap({
        asyncComponent: () => import('./routes/Settings/Accounting/Accounting.svelte')
    }),

    '/settings/staff': wrap({
        asyncComponent: () => import('./routes/Settings/Staff/Staff.svelte')
    }),

    '/settings/reports': wrap({
        asyncComponent: () => import('./routes/Settings/Reports/Reports.svelte')
    }),

    '/tickets': wrap({
        asyncComponent: () => import('./routes/Tickets/Tickets.svelte')
    }),

    '/tickets/ticket': wrap({
        asyncComponent: () => import('./routes/Tickets/Ticket.svelte')
    }),

    '/tickets/new': wrap({
        asyncComponent: () => import('./routes/Tickets/New.svelte')
    }),

    '/tickets/:ticket_id': wrap({
        asyncComponent: () => import('./routes/Tickets/Ticket.svelte')
    }),



    '/notifications': wrap({
        asyncComponent: () => import('./routes/Notifications/Notifications.svelte')
    }),

    '/users': wrap({
        asyncComponent: () => import('./routes/Users/Users.svelte')
    }),

    '/users/add': wrap({
        asyncComponent: () => import('./routes/Users/Add.svelte')
    }),

    '/users/:user_id': wrap({
        asyncComponent: () => import('./routes/Users/User.svelte')
    }),



    '/services': wrap({
        asyncComponent: () => import('./routes/Service/Services.svelte')
    }),

    '/services/add': wrap({
        asyncComponent: () => import('./routes/Service/Add.svelte')
    }),

    '/services/:service_id': wrap({
        asyncComponent: () => import('./routes/Service/Service.svelte')
    }),


    '/signaturerequests': wrap({
        asyncComponent: () => import('./routes/SignatureRequests/SignatureRequests.svelte')
    }),

    '/signaturerequests/:signature_request_id': wrap({
        asyncComponent: () => import('./routes/SignatureRequests/SignatureRequest.svelte')
    }),


    '/serviceagreements': wrap({
        asyncComponent: () => import('./routes/ServiceAgreements/ServiceAgreements.svelte')
    }),

    '/referrals': wrap({
        asyncComponent: () => import('./routes/Referrals/ReferralForm.svelte')


    }),

    // '/referrals/add': wrap({
    //     asyncComponent: () => import('./routes/Referrals/ReferralForm.svelte'),
    // }),

    // '/serviceagreements/add': wrap({
    //     asyncComponent: () => import('./routes/ServiceAgreements/Add.svelte')
    // }),

    // '/serviceagreements/:serviceagreement_id': wrap({
    //     asyncComponent: () => import('./routes/ServiceAgreements/Serviceagreement.svelte')
    // }),



    '/pulse': wrap({
        asyncComponent: () => import('./routes/Pulse/Pulse.svelte')
    }),


    '/registers': wrap({
        asyncComponent: () => import('./routes/Registers/Registers.svelte')
    }),

    '/registers/complaints': wrap({
        asyncComponent: () => import('./routes/Registers/Complaint/Complaints.svelte')
    }),

    '/registers/complaints/add': wrap({
        asyncComponent: () => import('./routes/Registers/Complaint/AddComplaint.svelte')
    }),

    '/registers/complaints/:id': wrap({
        asyncComponent: () => import('./routes/Registers/Complaint/Complaint.svelte')
    }),

    '/registers/feedbacks': wrap({
        asyncComponent: () => import('./routes/Registers/Feedback/Feedbacks.svelte')
    }),
    '/registers/feedbacks/add': wrap({
        asyncComponent: () => import('./routes/Registers/Feedback/AddFeedback.svelte')
    }),
    '/registers/feedbacks/:id': wrap({
        asyncComponent: () => import('./routes/Registers/Feedback/Feedback.svelte')
    }),

   
    '/registers/risks': wrap({
        asyncComponent: () => import('./routes/Registers/Risk/Risks.svelte')
    }),
    '/registers/risks/add': wrap({
        asyncComponent: () => import('./routes/Registers/Risk/AddRisk.svelte')
    }),
    '/registers/risks/:id': wrap({
        asyncComponent: () => import('./routes/Registers/Risk/Risk.svelte')
    }),
    '/registers/conflictofinterests': wrap({
        asyncComponent: () => import('./routes/Registers/ConflictOfInterest/ConflictOfInterests.svelte')
    }),
    '/registers/conflictofinterests/add': wrap({
        asyncComponent: () => import('./routes/Registers/ConflictOfInterest/AddConflictOfInterest.svelte')
    }),
    '/registers/conflictofinterests/:id': wrap({
        asyncComponent: () => import('./routes/Registers/ConflictOfInterest/ConflictOfInterest.svelte')
    }),


    '/registers/continuousimprovements': wrap({
        asyncComponent: () => import('./routes/Registers/ContinuousImprovement/ContinuousImprovements.svelte')
    }),
    '/registers/continuousimprovements/add': wrap({
        asyncComponent: () => import('./routes/Registers/ContinuousImprovement/AddContinuousImprovement.svelte')
    }),
    '/registers/continuousimprovements/:id': wrap({
        asyncComponent: () => import('./routes/Registers/ContinuousImprovement/ContinuousImprovement.svelte')
    }),

    '/registers/trainings': wrap({
        asyncComponent: () => import('./routes/Registers/Training/Trainings.svelte')
    }),
    '/registers/trainings/add': wrap({
        asyncComponent: () => import('./routes/Registers/Training/AddTraining.svelte')
    }),
    '/registers/trainings/:id': wrap({
        asyncComponent: () => import('./routes/Registers/Training/Training.svelte')
    }),

    '/registers/compliments': wrap({
        asyncComponent: () => import('./routes/Registers/Compliment/Compliments.svelte')
    }),
    '/registers/compliments/add': wrap({
        asyncComponent: () => import('./routes/Registers/Compliment/AddCompliment.svelte')
    }),
    '/registers/compliments/:id': wrap({
        asyncComponent: () => import('./routes/Registers/Compliment/Compliment.svelte')
    }),

    '/reports': wrap({
        asyncComponent: () => import('./routes/Reports/Reports.svelte')
    }),

    '/reports/budget': wrap({
        asyncComponent: () => import('./routes/Reports/Budget.svelte')
    }),

    '/reports/participantbudget': wrap({
        asyncComponent: () => import('./routes/Reports/ParticipantBudget.svelte')
    }),

    '/reports/capacity': wrap({
        asyncComponent: () => import('./routes/Reports/Capacity.svelte')
    }),

    '/reports/providertravel': wrap({
        asyncComponent: () => import('./routes/Reports/ProviderTravel.svelte')
    }),

    '/reports/timelog': wrap({
        asyncComponent: () => import('./routes/Reports/TimeLog.svelte')
    }),

    '/reports/due': wrap({
        asyncComponent: () => import('./routes/Reports/Due.svelte')
    }),

    '/reports/expired': wrap({
        asyncComponent: () => import('./routes/Reports/Expired.svelte')
    }),

    // '/reports/expiring': wrap({
    //     asyncComponent: () => import('./routes/Reports/ExpiringThisMonth.svelte')
    // }),

    '/reports/serviceagreementexpiry': wrap({
        asyncComponent: () => import('./routes/Reports/ServiceAgreementExpiry.svelte')
    }),

    '/reports/kpi': wrap({
        asyncComponent: () => import('./routes/Reports/KPI.svelte')
    }),

    '/reports/overduevisits': wrap({
        asyncComponent: () => import('./routes/Reports/OverdueVisits.svelte')
    }),

    // Note: This report will supersede the overdue visits report
    '/reports/lastseen': wrap({
        asyncComponent: () => import('./routes/Reports/LastSeen.svelte')
    }),









    '/accounts': wrap({
        asyncComponent: () => import('./routes/Accounts/Billables.svelte'),
        conditions: [
            (detail) => {
                replace(detail.location + '/billables')
            }
        ]
    }),

    // '/invoicing/ndia': wrap({
    //     asyncComponent: () => import('./routes/Invoicing/NDIA/NDIA.svelte')
    // }),
    // '/invoicing/ndia/unbilled': wrap({
    //     asyncComponent: () => import('./routes/Invoicing/NDIA/NDIAUnbilled.svelte')
    // }),
    // '/invoicing/ndia/billed': wrap({
    //     asyncComponent: () => import('./routes/Invoicing/NDIA/NDIABilled.svelte')
    // }),
    // '/invoicing/ndia/billed/:invoice_batch': wrap({
    //     asyncComponent: () => import('./routes/Invoicing/NDIA/NDIAInvoiceBatch.svelte')
    // }),



    '/accounts/billables': wrap({
        asyncComponent: () => import('./routes/Accounts/Billables.svelte')
    }),

    '/accounts/errors': wrap({
        asyncComponent: () => import('./routes/Accounts/Errors.svelte')
    }),


    // '/accounts/invoiced/:invoice_batch/:invoice_number': wrap({
    //     asyncComponent: () => import('./routes/Accounts/Invoice/Invoice.svelte')
    // }),

    '/accounts/invoice/:invoice_number': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoice/Invoice.svelte')
    }),




    '/accounts/invoiced/ndia/remittance': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced/NDIA/Remittance/Remittance.svelte')
    }),
    '/accounts/invoiced/ndia/remittance/summary': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced/NDIA/Remittance/Summary.svelte')
    }),


    '/accounts/invoiced/ndia/remittance/upload': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced/NDIA/Remittance/Upload.svelte')
    }),

    '/accounts/invoiced/ndia/remittance/:remittance_csv_name': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced/NDIA/Remittance/Prototype.svelte')
    }),



    '/accounts/invoiced/ndia/paymentrequeststatus': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced/NDIA/PaymentRequestStatus/PaymentRequestStatus.svelte')
    }),

    '/accounts/invoiced/ndia': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced/NDIA/NDIA.svelte')
    }),

    '/accounts/invoiced/:invoice_batch': wrap({
        asyncComponent: () => import('./routes/Accounts/InvoiceBatch.svelte')
    }),
    '/accounts/invoiced': wrap({
        asyncComponent: () => import('./routes/Accounts/Invoiced.svelte')
    }),


    '/accounts/planmanagers': wrap({
        asyncComponent: () => import('./routes/Accounts/PlanManagers/PlanManagers.svelte')
    }),

    '/accounts/planmanagers/add': wrap({
        asyncComponent: () => import('./routes/Accounts/PlanManagers/Add.svelte')
    }),

    '/accounts/planmanagers/:planmanager_id': wrap({
        asyncComponent: () => import('./routes/Accounts/PlanManagers/PlanManager.svelte')
    }),









    '/404': NotFound,
    '*': NotFound,

}