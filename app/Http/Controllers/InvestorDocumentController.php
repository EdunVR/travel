<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\InvestorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Str;

class InvestorDocumentController extends Controller
{
    /**
     * Menyimpan dokumen yang diupload
     */
    public function store(Request $request, $investorId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:KTP,NPWP,AKAD,KONTRAK,LAINNYA',
            'document' => 'required|file|mimes:pdf,jpg,png|max:2048'
        ]);

        try {
            $path = $request->file('document')->store('investor-documents', 'public');

            $document = InvestorDocument::create([
                'investor_id' => $investorId,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'file_path' => $path,
                'is_custom' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diupload',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload dokumen: '.$e->getMessage()
            ], 500);
        }
    }

    public function createCustom(Request $request, $investorId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:AKAD,KONTRAK,LAINNYA',
            'content' => 'required|string',
            'signature' => 'nullable|string|max:255'
        ]);

        try {
            // Generate PDF
            $pdf = PDF::loadView('irp.investor.documents.custom_template', [
                'title' => $validated['title'],
                'content' => $validated['content'],
                'signature' => $validated['signature'] ?? null,
                'investor' => Investor::find($investorId),
                'date' => now()->format('d F Y')
            ]);

            $filename = 'doc_'.Str::slug($validated['title']).'_'.time().'.pdf';
            $path = 'investor-documents/custom/'.$filename;
            
            Storage::disk('public')->put($path, $pdf->output());

            $document = InvestorDocument::create([
                'investor_id' => $investorId,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'file_path' => $path,
                'is_custom' => true,
                'content' => $validated['content'],
                'meta' => [
                    'signature' => $validated['signature'] ?? null
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dibuat',
                'document' => $document
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat dokumen: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan dokumen
     */
    public function show($investorId, $documentId)
    {
        $investor = Investor::findOrFail($investorId);
        $document = $investor->documents()->findOrFail($documentId);
        
        if ($document->is_custom) {
            return PDF::loadView('irp.investor.documents.custom', [
                'title' => $document->title,
                'content' => $document->content,
                'signature' => $document->meta['signature'] ?? null,
                'investor' => $investor,
                'date' => $document->created_at->format('d F Y')
            ])->stream($document->title.'.pdf');
        }

        return response()->file(storage_path('app/public/'.$document->file_path));
    }

    /**
     * Menghapus dokumen
     */
    public function destroy($investorId, $documentId)
    {
        $investor = Investor::findOrFail($investorId);
        $document = $investor->documents()->findOrFail($documentId);
        
        try {
            // Hapus file fisik
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            $document->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: '.$e->getMessage()
            ], 500);
        }
    }
}