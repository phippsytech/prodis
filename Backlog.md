
csv dump of active anonymised participants with the services we provide


clients
clientplans
clientplanservices

services



SELECT clients.id, md5(concat(clients.id,'phippsy')) anon_hash, clients.name, services.code, services.billing_code
FROM clients
JOIN clientplans ON clients.id = clientplans.client_id
JOIN clientplanservices ON clientplans.id = clientplanservices.plan_id
JOIN services on services.id = clientplanservices.service_id
WHERE (clients.archived is null or archived!=1)
ORDER BY clients.id, services.id;



Handling overpayments as a credit on an account.