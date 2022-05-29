<?php

namespace App\Web\Quotes\Controllers;

use App\Web\Quotes\Queries\QuoteIndexQuery;
use Support\App\Api\Controller;

class QuoteController extends Controller
{
    public function index(QuoteIndexQuery $quoteQuery)
    {
        $quotes = $quoteQuery->get();

        return view('welcome/index', ['quotes' =>$quotes]);
    }
}
