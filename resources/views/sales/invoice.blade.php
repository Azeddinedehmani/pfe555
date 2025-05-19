@extends('layouts.app')

@section('title', 'Facture #' . $sale->invoice_number)
@section('page-title', 'Facture')

@push('styles')
<style>
    @media print {
        body {
            font-size: 12pt;
        }
        .print-invisible {
            display: none !important;
        }
        .invoice-box {
            max-width: 100% !important;
            box-shadow: none !important;
            border: none !important;
        }
    }
    
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid var(--border-color);
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 14px;
        line-height: 24px;
        font-family: 'Rubik', sans-serif;
        color: var(--text-color);
        background-color: var(--card-bg);
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: var(--primary-color);
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading td {
        background: var(--light-bg);
        border-bottom: 1px solid var(--border-color);
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td {
        border-bottom: 1px solid var(--border-color);
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(4) {
        border-top: 2px solid var(--border-color);
        font-weight: bold;
    }
    
    .invoice-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
        text-align: center;
        color: var(--muted-color);
        font-size: 12px;
    }
    
    .invoice-box .thank-you {
        text-align: center;
        margin-top: 40px;
        font-size: 16px;
        font-weight: 500;
        color: var(--primary-color);
    }
    
    .print-link {
        font-size: 24px;
        color: var(--primary-color);
        cursor: pointer;
    }
    
    .payment-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: bold;
    }
    
    .payment-badge.paid {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .payment-badge.pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .payment-badge.partial {
        background-color: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 print-invisible">
        <h3>Facture #{{ $sale->invoice_number }}</h3>
        <div>
            <a href="{{ route('sales.generatePdf', $sale) }}" class="btn btn-success me-2">
                <i class="bi bi-file-earmark-pdf"></i> Télécharger PDF
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimer
            </button>
            <a href="{{ route('sales.index') }}" class="btn btn-secondary ms-2">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>
    
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ asset('images/logo.png') }}" style="width: 100%; max-width: 200px;" alt="Pharmacia Logo">
                            </td>
                            <td style="text-align: right;">
                                Facture #: {{ $sale->invoice_number }}<br>
                                Date: {{ $sale->created_at->format('d/m/Y') }}<br>
                                Heure: {{ $sale->created_at->format('H:i') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <strong>Pharmacia</strong><br>
                                123 Rue de la Santé<br>
                                75000 Paris, France<br>
                                Tél: 01 23 45 67 89<br>
                                Email: contact@pharmacia.fr
                            </td>
                            <td style="text-align: right;">
                                <strong>Client</strong><br>
                                {{ $sale->client->name }}<br>
                                {{ $sale->client->address ?: 'Adresse non spécifiée' }}<br>
                                Tél: {{ $sale->client->phone ?: 'Non spécifié' }}<br>
                                Email: {{ $sale->client->email ?: 'Non spécifié' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="details">
                <td colspan="2">
                    <strong>Méthode de paiement:</strong> 
                    @if($sale->payment_method == 'cash')
                        Espèces
                    @elseif($sale->payment_method == 'card')
                        Carte bancaire
                    @elseif($sale->payment_method == 'bank_transfer')
                        Virement bancaire
                    @else
                        {{ $sale->payment_method }}
                    @endif
                </td>
                <td colspan="2" style="text-align: right;">
                    <strong>Statut:</strong> 
                    <span class="payment-badge {{ $sale->payment_status }}">
                        @if($sale->payment_status == 'paid')
                            Payé
                        @elseif($sale->payment_status == 'pending')
                            En attente
                        @elseif($sale->payment_status == 'partial')
                            Partiel
                        @else
                            {{ $sale->payment_status }}
                        @endif
                    </span>
                </td>
            </tr>
            
            @if($sale->prescription)
            <tr class="details">
                <td colspan="4">
                    <strong>Ordonnance:</strong> {{ $sale->prescription->reference_number }} | 
                    <strong>Prescripteur:</strong> Dr. {{ $sale->prescription->prescriber_name }} | 
                    <strong>Date:</strong> {{ $sale->prescription->issue_date->format('d/m/Y') }}
                </td>
            </tr>
            @endif
            
            <tr class="heading">
                <td width="40%">Produit</td>
                <td width="20%" style="text-align: right;">Prix unitaire</td>
                <td width="15%" style="text-align: right;">Quantité</td>
                <td width="25%" style="text-align: right;">Total</td>
            </tr>
            
            @foreach($sale->saleItems as $item)
            <tr class="item {{ $loop->last ? 'last' : '' }}">
                <td>
                    {{ $item->product->name }}
                    @if($item->product->is_prescription_required)
                    <small class="text-muted">(Sous ordonnance)</small>
                    @endif
                </td>
                <td style="text-align: right;">{{ number_format($item->price, 2) }} €</td>
                <td style="text-align: right;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->total, 2) }} €</td>
            </tr>
            @endforeach
            
            <tr class="total">
                <td colspan="3" style="text-align: right;">Sous-total:</td>
                <td style="text-align: right;">{{ number_format($sale->subtotal, 2) }} €</td>
            </tr>
            
            <tr class="total">
                <td colspan="3" style="text-align: right;">TVA (20%):</td>
                <td style="text-align: right;">{{ number_format($sale->tax, 2) }} €</td>
            </tr>
            
            @if($sale->discount > 0)
            <tr class="total">
                <td colspan="3" style="text-align: right;">Remise:</td>
                <td style="text-align: right;">-{{ number_format($sale->discount, 2) }} €</td>
            </tr>
            @endif
            
            <tr class="total">
                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                <td style="text-align: right;"><strong>{{ number_format($sale->total, 2) }} €</strong></td>
            </tr>
        </table>
        
        @if($sale->note)
        <div style="margin-top: 30px;">
            <strong>Note:</strong>
            <p>{{ $sale->note }}</p>
        </div>
        @endif
        
        <div class="thank-you">
            Merci pour votre confiance !
        </div>
        
        <div class="invoice-footer">
            <p>Pharmacia - SIRET: 123 456 789 00012 - TVA: FR 12 345 678 901</p>
            <p>Cette facture a été générée automatiquement et est valable sans signature ni cachet.</p>
        </div>
    </div>
    
    <!-- Actions complémentaires -->
    <div class="d-flex justify-content-between mt-4 print-invisible">
        <div>
            @if($sale->status != 'cancelled')
            <form action="{{ route('sales.cancel', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette vente ? Cette action est irréversible.')">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Annuler la vente
                </button>
            </form>
            @endif
        </div>
        <div>
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-info me-2">
                <i class="bi bi-eye"></i> Détails de la vente
            </a>
            <a href="{{ route('sales.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nouvelle vente
            </a>
        </div>
    </div>
</div>
@endsection