<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicePrintController extends Controller
{
    public function print(Invoice $invoice)
    {
        // Load relationships for the invoice
        $invoice->load(['student', 'course', 'enrollment', 'payment']);
        
        return view('invoices.print', compact('invoice'));
    }
}
