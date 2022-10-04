<?php

namespace App\Web\Quotes\Controllers;

use App\Controller;
use App\Web\Quotes\Queries\QuoteIndexQuery;
use Illuminate\Contracts\View\View;

class QuoteController extends Controller
{
    public function index(QuoteIndexQuery $quoteQuery): View
    {
        $quotes = $quoteQuery->get();

        return view('welcome/index', ['quotes' =>$quotes]);
    }
}
