### Business logic

Triggering shipment api decoupled from invoices web hook.


Payment API --[event]--> Events 
                            ^    
                            |
                        ShipmentJob -------> Shipment API
                            ^
                            |
                         Worker




```mermaid
flowchart LR
    Payment["Payment API"] -->|event| Events["Events"]
    Events --> Shipment["Shipment API"]
    Worker["Worker"] --> Events
```

### API
/api/shippment-mock/ok
/api/shippment-mock/failure
/api/shippment-mock/timeout
###
```bash
docker compose up -d
```
