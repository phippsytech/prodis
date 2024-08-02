<?php
namespace NDISmate\Models\Payroll\Payrun\Payslip;

use \NDISmate\CORE\KeyValue;

class Generator
{
    const STATUS_IDLE = 'idle';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR = 'error';

    private array $status = [];

    private array $websocket_message = [
        'action' => 'sendToChannel',
        'channel' => 'payslip_generator',
        'data' => [
            'action' => 'updateGeneratorStatus',
            'data' => [
                'status' => 'idle',  // idle, processing, error
                'processed' => 0,
                'count' => 0,
            ]
        ]
    ];

    public function __construct()
    {
        $status = KeyValue::get('xero_payslip_generator_status');
        if (!empty($status)) {
            $this->status = json_decode($status, true);
        } else {
            $this->status = [
                'status' => self::STATUS_IDLE,
                'processed' => 0,
                'count' => 0,
            ];
        }
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function increaseProcessed(): array
    {
        $this->status['processed']++;

        KeyValue::set('xero_payslip_generator_status', json_encode($this->status));
        $this->sendUpdate();

        if ($this->status['processed'] >= $this->status['count']) {
            sleep(1);
            $this->resetGenerator();
        }

        return $this->status;
    }

    public function startGenerator(int $count): array
    {
        $this->status['status'] = self::STATUS_PROCESSING;
        $this->status['count'] = $count;
        $this->status['processed'] = 0;

        KeyValue::set('xero_payslip_generator_status', json_encode($this->status));

        return $this->status;
    }

    public function resetGenerator(): array
    {
        $this->status['status'] = self::STATUS_IDLE;
        $this->status['processed'] = 0;
        $this->status['count'] = 0;

        KeyValue::set('xero_payslip_generator_status', json_encode($this->status));
        $this->sendUpdate();
        return $this->status;
    }

    private function sendUpdate(): array
    {
        $this->websocket_message['data']['data'] = $this->status;

        $socket = stream_socket_client('unix:///tmp/ndismate.sock');
        if ($socket !== false) {
            $websocket_json = json_encode($this->websocket_message);
            fwrite($socket, $websocket_json);
            $response = fread($socket, 8192);
        }

        return $this->status;
    }
}
