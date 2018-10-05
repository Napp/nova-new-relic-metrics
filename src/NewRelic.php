<?php

namespace Napp\NewRelicMetrics;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;

/**
 * Class NewRelic
 * @package Napp\NewRelicMetrics
 */
class NewRelic
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $appId;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.newrelic.com/',
            'headers' => [
                'X-Api-Key' => config('services.newrelic.api_key'),
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->appId = config('services.newrelic.app_id');
    }

    public function responseTime(int $range): array
    {
        $response = $this->client->get('v2/applications/' . $this->appId . '/metrics/data.json', [
            'query' => [
                'names[]' => 'HttpDispatcher',
                'values[]' => 'average_call_time',
                'from' => now()->subMinutes($range)->toIso8601String(),
                'to' => now()->toIso8601String()
            ]
        ])->getBody();

        return $this->reMap(json_decode($response)->metric_data->metrics[0]->timeslices, 'average_call_time');
    }

    /**
     * @see https://rpm.newrelic.com/api/explore/applications/metric_data?application_id=appid&names[]=HttpDispatcher&values[]=requests_per_minute&summarize=false
     * @param int $range
     * @return array
     */
    public function throughput(int $range): array
    {
        $response = $this->client->get('v2/applications/' . $this->appId . '/metrics/data.json', [
            'query' => [
                'names[]' => 'HttpDispatcher',
                'values[]' => 'requests_per_minute',
                'from' => now()->subMinutes($range)->toIso8601String(),
                'to' => now()->toIso8601String()
            ]
        ])->getBody();

        return $this->reMap(json_decode($response)->metric_data->metrics[0]->timeslices, 'requests_per_minute');
    }

    public function errorRate(int $range): array
    {
        $from = now()->subMinutes($range)->toIso8601String();
        $to = now()->toIso8601String();

        $response = $this->client->get('v2/applications/' . $this->appId . '/metrics/data.json?' .
            'names[]=Errors/all&names[]=HttpDispatcher&names[]=OtherTransaction/all&values[]=error_count' .
            '&values[]=call_count&from=' . $from . '&to=' . $to . '&summarize=true')->getBody();

        $error_count = json_decode($response)->metric_data->metrics[0]->timeslices[0]->values->error_count;
        $call_count = json_decode($response)->metric_data->metrics[1]->timeslices[0]->values->call_count;
        $other_count = json_decode($response)->metric_data->metrics[2]->timeslices[0]->values->call_count;

        // calc error rate
        $calls = (int) $call_count + $other_count;
        if (0 === $calls) {
            $errorRate = 0;
        } else {
            $errorRate = number_format(100 * $error_count / $calls, 3);
        }

        $response = $this->client->get('v2/applications/' . $this->appId . '/metrics/data.json', [
            'query' => [
                'names[]' => 'Errors/all',
                'values[]' => 'error_count',
                'from' => $from,
                'to' => $to
            ]
        ])->getBody();
        $errors = $this->reMap(json_decode($response)->metric_data->metrics[0]->timeslices, 'error_count');

        return [$errors[0], $errorRate];
    }

    /**
     * @param array $list
     * @param string $valueLabel
     * @return array
     */
    private function reMap(array $list, string $valueLabel): array
    {
        $avg = 0;
        $output = [];
        foreach ($list as $item) {
            $label = Carbon::parse($item->to)->format('Y-m-d H:i');
            $avg += $item->values->{$valueLabel};
            $output[$label] = $item->values->{$valueLabel};
        }

        $avg = number_format($avg / count($list), 1);

        return [$output, $avg];
    }
}