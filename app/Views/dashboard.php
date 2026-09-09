<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Invoice Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = {
            theme: {
                extend: {
                    colors: {brand: "#2563eb", mist: "#f6f8fc"},
                    boxShadow: {card: "0 18px 45px rgba(28, 39, 64, 0.08)"}
                }
            }
        };</script>
</head>
<body class="min-h-screen bg-mist text-slate-900 antialiased">
<main class="mx-auto flex min-h-screen w-full flex-col px-5 py-8 sm:px-8 lg:px-10">
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-2xl">Dashboard</h1>
        </div>

    </div>
    <div class="grid gap-6 lg:grid-cols-[25%_75%] lg:items-start">

        <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-card sm:p-8">
            <h2 class="text-lg font-bold text-slate-900">Invoice event</h2>
            <label class="mt-4 block text-sm font-semibold text-slate-700" for="invoice-body">Request body</label>
            <textarea id="invoice-body"
                      class="mt-2 min-h-64 w-full rounded-xl border border-slate-200 bg-slate-50 p-4 font-mono text-sm text-slate-800 shadow-sm outline-none transition focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                      spellcheck="false" aria-describedby="invoice-body-help">
            </textarea>
            <p id="invoice-body-help" class="mt-2 text-xs text-slate-500">
                Enter the JSON body for the invoice request.
            </p>
            <button id="send-invoice"
                    class="mt-4 rounded-xl bg-brand px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300 disabled:cursor-wait disabled:opacity-60"
                    type="button">
                Send invoice
            </button>
            <div class="mt-3">
                <pre id="query-result" class="mt-4 max-h-80 min-h-24 overflow-auto rounded-xl bg-slate-950 p-4 font-mono text-xs leading-6 text-slate-100" role="status" aria-live="polite">Run the query to load invoice results.</pre>
            </div>
        </section>
        <section>
            <div class="py-2 flex justify-between w-full">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Invoices</h2>
                    <p class="mt-1 text-sm text-slate-500">Reload the page for refresh</p>
                </div>
                <div class="text-center">
                    <span class=" text-slate-500 pr-5">Run &quot;worker&quot;, takes one job at a time; cases &rarr;</span>

                    <button id="worker-ok"
                        class="mt-4 rounded-xl bg-brand px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-300 disabled:cursor-wait disabled:opacity-60"
                        type="button">
                        OK
                    </button>
                    <button id="worker-error"
                        class="mt-4 rounded-xl bg-brand px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-300 disabled:cursor-wait disabled:opacity-60"
                        type="button">
                        Error
                    </button>
                    <button id="worker-timeout"
                        class="mt-4 rounded-xl bg-brand px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-300 disabled:cursor-wait disabled:opacity-60"
                        type="button">
                        Timeout
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-3xl border border-slate-200/80 bg-white p-6 shadow-card sm:p-8">
                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50/80">
                    <tr class="transition-colors hover:bg-blue-50/40">
                        <th class="whitespace-nowrap px-5 py-4">Invoice ID</th>
                        <th class="whitespace-nowrap px-5 py-4">Name</th>
                        <th class="whitespace-nowrap px-5 py-4">Email</th>
                        <th class="whitespace-nowrap px-5 py-4">Amount</th>
                        <th class="whitespace-nowrap px-5 py-4">Status</th>
                        <th class="whitespace-nowrap px-5 py-4">Shipping Status</th>
                        <th class="whitespace-nowrap px-5 py-4">Shipping Reference</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($invoices) && is_array($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr class="transition-colors hover:bg-blue-50/40">
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['invoice_id']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['customer_name']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['customer_email']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['amount']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['status']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['shipping_status']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($invoice['shipping_reference']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="transition-colors hover:bg-blue-50/40">
                            <td colspan="4" class="px-5 py-14 text-center">
                                <p class="text-sm font-semibold text-slate-900">No invoices yet</p>
                                <p class="mt-1 text-sm text-slate-500">New invoice records will appear here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 pt-5 pb-2">Log</h2>
            </div>
            <div class="overflow-x-auto rounded-3xl border border-slate-200/80 bg-white p-6 shadow-card sm:p-8">

                <table class="min-w-full divide-y divide-slate-100 text-left">
                    <thead class="bg-slate-50/80">
                    <tr class="transition-colors hover:bg-blue-50/40">
                        <th class="whitespace-nowrap px-5 py-4">Name</th>
                        <th class="whitespace-nowrap px-5 py-4">Payload</th>
                        <th class="whitespace-nowrap px-5 py-4">Created at</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($eventLog) && is_array($eventLog)): ?>
                        <?php foreach ($eventLog as $event): ?>
                            <tr class="transition-colors hover:bg-blue-50/40">
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($event['name']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($event['payload']) ?></td>
                                <td class="whitespace-nowrap px-5 py-5 text-sm text-slate-700"><?= esc($event['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="transition-colors hover:bg-blue-50/40">
                            <td colspan="4" class="px-5 py-14 text-center">
                                <p class="text-sm font-semibold text-slate-900">No logs yet</p>
                                <p class="mt-1 text-sm text-slate-500">New log records will appear here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</main>

<script>
    const button = document.querySelector("#send-invoice");
    const workerButtons = [
        {button: document.querySelector("#worker-ok"), url: "/api/shipment-job-test/ok"},
        {button: document.querySelector("#worker-error"), url: "/api/shipment-job-test/error"},
        {button: document.querySelector("#worker-timeout"), url: "/api/shipment-job-test/timeout"}
    ];
    const invoiceBody = document.querySelector("#invoice-body");
    const result = document.querySelector("#result");
    const queryResult = document.querySelector("#query-result");

    const invoice = {
        event: "invoice.created",
        invoice_id: "INV-2026-00123",
        customer: {
            id: "CUST-4711",
            name: "Muster Optik GmbH",
            email: "kontakt@musteroptik.example"
        },
        amount: 249.90,
        currency: "CHF",
        status: "open",
        due_date: "2026-10-15",
        created_at: "2026-09-08T10:15:00Z"
    };

    invoiceBody.value = JSON.stringify(invoice, null, 2);

    button.addEventListener("click", async () => {
        let requestBody;

        try {
            requestBody = JSON.parse(invoiceBody.value);
        } catch (error) {
            queryResult.textContent = "Unable to send invoice: request body must be valid JSON.";
            invoiceBody.focus();
            return;
        }

        button.disabled = true;
        queryResult.textContent = "Sending...";

        try {
            const response = await fetch("/api/invoices", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(requestBody)
            });

            let responseText = await response.text();

            if (responseText) {
                try {
                    responseText = JSON.stringify(JSON.parse(responseText), null, 2);
                } catch (error) {
                    // Keep non-JSON responses readable in the result panel.
                }
            }

            queryResult.textContent = responseText || "(empty response)";

        } catch (error) {
            queryResult.textContent = `Unable to send invoice: ${error.message}`;
        } finally {
            button.disabled = false;
        }
    });
    workerButtons.forEach(({button, url}) => {
        button.addEventListener("click", async () => {
            workerButtons.forEach(({button}) => button.disabled = true);
            queryResult.textContent = "Sending " + button.textContent.trim() + " request...";

            try {
                const response = await fetch(url);
                let responseText = await response.text();

                if (responseText) {
                    try {
                        responseText = JSON.stringify(JSON.parse(responseText), null, 2);
                    } catch (error) {
                        // Keep non-JSON responses readable in the result panel.
                    }
                }

                queryResult.textContent = responseText || "Request completed with status " + response.status + ".";
            } catch (error) {
                queryResult.textContent = "Unable to send worker request: " + error.message;
            } finally {
                workerButtons.forEach(({button}) => button.disabled = false);
            }
        });
    });
</script>
</body>
</html>