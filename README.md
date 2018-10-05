# New Relic APM Metrics for Laravel Nova

Add Error Rate, Throughput and Response Time metrics to your Laravel Nova Dashboard.

![Example](https://raw.githubusercontent.com/Napp/nova-new-relic-metrics/master/docs/dashboard.png)

### Install

You need to run the following command: 
`composer require napp/nova-new-relic-metrics`

## Add Credentials

add the following to `config/services.php`

```php
'newrelic' => [
    'api_key' => env('NEW_RELIC_API_KEY'),
    'insights_api_key' => env('NEW_RELIC_INSIGHTS_API_KEY'),
    'account_id' => env('NEW_RELIC_ACCOUNT_ID'),
    'app_id' => env('NEW_RELIC_APP_ID'),
]
```


## Add cards to a dashboard

add to `NovaServiceProvider.php`

```php

public function cards()
{
    return [
        new \Napp\NewRelicMetrics\NewRelicThroughput,
        new \Napp\NewRelicMetrics\NewRelicErrorRate,
        new \Napp\NewRelicMetrics\NewRelicResponseTime,
        new \Napp\NewRelicMetrics\NewRelicMysqlRequests,
        new \Napp\NewRelicMetrics\NewRelicRedisRequests,
    ];
}

```






