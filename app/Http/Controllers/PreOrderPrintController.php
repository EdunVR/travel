<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PreOrderPrintController extends Controller
{
    public function penawaran(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        $pdf = PDF::loadView('admin.pre-orders.pdf.penawaran', compact('preorder'))
            ->setPaper('a4', 'portrait');
        
        $filename = "Penawaran-" . str_replace(['/', '\\'], '-', $preorder->kode_preorder) . ".pdf";
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function invoice(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        $pdf = PDF::loadView('admin.pre-orders.pdf.invoice', compact('preorder'))
            ->setPaper('a4', 'portrait');
        
        $filename = "Invoice-" . str_replace(['/', '\\'], '-', $preorder->kode_preorder) . ".pdf";
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function kwitansi(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        $pdf = PDF::loadView('admin.pre-orders.pdf.kwitansi', compact('preorder'))
            ->setPaper('a4', 'portrait');
        
        $filename = "Kwitansi-" . str_replace(['/', '\\'], '-', $preorder->kode_preorder) . ".pdf";
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function penawaranModal(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        return view('admin.pre-orders.pdf.penawaran', compact('preorder'));
    }

    public function invoiceModal(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        return view('admin.pre-orders.pdf.invoice', compact('preorder'));
    }

    public function kwitansiModal(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        return view('admin.pre-orders.pdf.kwitansi', compact('preorder'));
    }
}