<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceService
{
    /**
     * Générer une facture PDF pour une vente
     *
     * @param Sale $sale
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoice(Sale $sale)
    {
        // Charger les données nécessaires
        $sale->load(['client', 'items.product', 'user']);
        
        // Récupérer les paramètres de la pharmacie depuis les settings
        $pharmacyName = setting('pharmacy_name', 'Pharmacia');
        $pharmacyAddress = setting('pharmacy_address', '');
        $pharmacyPhone = setting('pharmacy_phone', '');
        $pharmacyEmail = setting('pharmacy_email', '');
        $pharmacyTaxId = setting('pharmacy_tax_id', '');
        
        // Formater les données pour la facture
        $invoiceData = [
            'sale' => $sale,
            'invoice_number' => $sale->reference,
            'date' => $sale->created_at->format('d/m/Y'),
            'due_date' => $sale->created_at->addDays(30)->format('d/m/Y'),
            'client' => $sale->client ? [
                'name' => $sale->client->name,
                'address' => $sale->client->address,
                'phone' => $sale->client->phone,
                'email' => $sale->client->email,
            ] : [
                'name' => 'Client Anonyme',
                'address' => '',
                'phone' => '',
                'email' => '',
            ],
            'pharmacy' => [
                'name' => $pharmacyName,
                'address' => $pharmacyAddress,
                'phone' => $pharmacyPhone,
                'email' => $pharmacyEmail,
                'tax_id' => $pharmacyTaxId,
                'logo' => public_path('images/logo.png'),
            ],
            'items' => $sale->items->map(function ($item) {
                return [
                    'description' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'discount' => $item->discount,
                    'total' => $item->subtotal,
                ];
            }),
            'subtotal' => $sale->total_amount,
            'discount' => $sale->discount,
            'tax' => $sale->tax,
            'total' => $sale->final_amount,
            'notes' => $sale->notes,
            'payment_method' => $this->formatPaymentMethod($sale->payment_method),
            'payment_status' => $this->formatPaymentStatus($sale->payment_status),
            'seller' => $sale->user->name,
        ];
        
        // Générer le PDF
        $pdf = PDF::loadView('pdf.invoice', $invoiceData);
        
        // Configurer le PDF
        $pdf->setPaper('a4');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        
        return $pdf;
    }
    
    /**
     * Formater la méthode de paiement pour affichage
     *
     * @param string $method
     * @return string
     */
    private function formatPaymentMethod($method)
    {
        switch ($method) {
            case 'cash':
                return 'Espèces';
            case 'card':
                return 'Carte bancaire';
            case 'bank_transfer':
                return 'Virement bancaire';
            default:
                return ucfirst($method);
        }
    }
    
    /**
     * Formater le statut de paiement pour affichage
     *
     * @param string $status
     * @return string
     */
    private function formatPaymentStatus($status)
    {
        switch ($status) {
            case 'paid':
                return 'Payé';
            case 'unpaid':
                return 'Non payé';
            case 'partial':
                return 'Partiellement payé';
            default:
                return ucfirst($status);
        }
    }
    
    /**
     * Enregistrer une facture dans le stockage
     *
     * @param Sale $sale
     * @return string Le chemin du fichier enregistré
     */
    public function saveInvoice(Sale $sale)
    {
        $pdf = $this->generateInvoice($sale);
        $path = 'invoices/' . date('Y/m/') . $sale->reference . '.pdf';
        
        Storage::disk('public')->put($path, $pdf->output());
        
        return $path;
    }
}