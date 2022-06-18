<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $journal = Journal::all();
        $number ??= $request->get('number');
        if (!empty($journal->toArray())) {
            View::share([
                'journal' => $journal,
                'number' => $number
            ]);
        }
        return view('welcome');
    }
}
