<?php

namespace Napp\NewRelicMetrics\Http\Controllers;

use Napp\NewRelicMetrics\NewRelic;

class ApiController
{
    public function transactions()
    {
        return response()->json((new NewRelic())->transactions());
    }
}