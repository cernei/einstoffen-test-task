<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(): string
    {
        return view('dashboard.html');
    }

    public function data(): ResponseInterface
    {
        $db = db_connect();
        $invoices = $db->table('invoices')
            ->select('id, invoice_id, customer_name, amount, currency, comment, shipping_status, shipping_info, created_at')
            ->orderBy('id', 'desc')
            ->get()
            ->getResultArray();

        $jobs = $db->table('queue_jobs')->orderBy('id', 'desc')->get()->getResultArray();
        foreach ($jobs as &$job) {
            if (!empty($job['created_at'])) {
                $job['created_at'] = gmdate('Y-m-d H:i:s', $job['created_at']);
                $job['available_at'] = gmdate('Y-m-d H:i:s', $job['available_at']);
            }
        }
        unset($job);

        $failedJobs = $db->table('queue_jobs_failed')->orderBy('id', 'desc')->get()->getResultArray();
        foreach ($failedJobs as &$job) {
            if (!empty($job['failed_at'])) {
                $job['failed_at'] = gmdate('Y-m-d H:i:s', $job['failed_at']);
            }
        }
        unset($job);
        $eventLog = $db->table('event_log')->orderBy('id', 'desc')->get()->getResultArray();

        return $this->response->setJSON([
            'invoices' => $invoices,
            'jobs' => $jobs,
            'failedJobs' => $failedJobs,
            'eventLog' => $eventLog
        ]);
    }

    public function clearAll(): ResponseInterface
    {
        $db = db_connect();
        $db->table('invoices')->truncate();
        $db->table('event_log')->truncate();
        $db->table('queue_jobs')->truncate();
        $db->table('queue_jobs_failed')->truncate();

        return $this->response->setJSON([
            'message' => 'Tables truncated successfully.',
        ]);
    }
}
