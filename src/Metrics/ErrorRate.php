<?php

namespace Napp\NewRelicMetrics\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Trend;
use Napp\NewRelicMetrics\NewRelic;

class ErrorRate extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $data = (new NewRelic())->errorRate((int) $request->input('range'));

        return $this->result('Avg ' . $data[1])->trend($data[0])->suffix('%');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => '30 Min',
            60 => '60 Min',
            180 => '3 Hrs',
            720 => '12 Hrs',
            1440 => '24 Hrs',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addMinutes(2);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'new-relic-error-rate';
    }
}
