@extends('layouts.app')

@section('title', 'Gestion d\'Inventaire')
@section('page-title', 'Inventaire')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Statistiques de l'inventaire</h5>
                </div>
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small-box bg-primary text-white p-3 rounded">
                            <div class="inner">
                                <h3>{{ $totalProducts }}</h3>
                                <p>Total produits</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small-box bg-warning text-white p-3 rounded">
                            <div class="inner">
                                <h3>{{ $lowStockProducts }}</h3>
                                <p>Stock faible</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small-box bg-danger text-white p-3 rounded">
                            <div class="inner">
                                <h3>{{ $expiredProducts }}</h3>
                                <p>Expirés</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-x-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="small-box bg-info text-white p-3 rounded">
                            <div class="inner">
                                <h3>{{ $categories }}</h3>
                                <p>Catégories</p>
                            </div>
                            <div class="icon">
                                <i class="bi bi-tag"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Actions rapides</h5>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('inventory.create') }}" class="btn btn-primary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center py-4">
                            <i class="bi bi-plus-circle fs-1 mb-2"></i>
                            <span>Ajouter un produit</span>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('inventory.export') }}" class="btn btn-success btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center py-4">
                            <i class="bi bi-file-earmark-excel fs-1 mb-2"></i>
                            <span>Exporter l'inventaire</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('purchases.create') }}" class="btn btn-info btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center py-4">
                            <i class="bi bi-bag-plus fs-1 mb-2"></i>
                            <span>Nouvel achat</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-lg w-100 h-100 d-flex flex-column justify-content-center align-items-center py-4">
                            <i class="bi bi-graph-up fs-1 mb-2"></i>
                            <span>Rapports d'inventaire</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des produits</h5>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Ajouter un produit
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-md-9">
                <form action="{{ route('inventory.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Rechercher...">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="stock_status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock faible</option>
                            <option value="expired" {{ request('stock_status') == 'expired' ? 'selected' : '' }}>Expirés</option>
                            <option value="near_expiry" {{ request('stock_status') == 'near_expiry' ? 'selected' : '' }}>Expiration proche</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </form>
            </div>
            <div class="col-md-3 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('inventory.export', request()->all()) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Exporter
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-arrow-up"></i> Importer
                    </button>
                </div>
            </div>
        </div>

        <!-- Tableau des produits -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('inventory.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('sort') == 'name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Produit
                                @if(request('sort') == 'name')
                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('inventory.index', array_merge(request()->all(), ['sort' => 'sku', 'direction' => request('sort') == 'sku' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                SKU
                                @if(request('sort') == 'sku')
                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Catégorie</th>
                        <th>
                            <a href="{{ route('inventory.index', array_merge(request()->all(), ['sort' => 'quantity', 'direction' => request('sort') == 'quantity' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Quantité
                                @if(request('sort') == 'quantity')
                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('inventory.index', array_merge(request()->all(), ['sort' => 'selling_price', 'direction' => request('sort') == 'selling_price' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Prix de vente
                                @if(request('sort') == 'selling_price')
                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('inventory.index', array_merge(request()->all(), ['sort' => 'expiry_date', 'direction' => request('sort') == 'expiry_date' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                Date d'expiration
                                @if(request('sort') == 'expiry_date')
                                    <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="placeholder-image me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-box text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $product->name }}</div>
                                        @if($product->is_prescription_required)
                                            <span class="badge bg-info small">Ordonnance requise</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                @if($product->isLowStock())
                                    <span class="badge bg-danger">{{ $product->quantity }}</span>
                                @else
                                    <span class="badge bg-success">{{ $product->quantity }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($product->selling_price, 2) }} €</td>
                            <td>
                                @if($product->expiry_date)
                                    @if($product->isExpired())
                                        <span class="badge bg-danger">{{ $product->expiry_date->format('d/m/Y') }}</span>
                                    @elseif($product->isNearExpiry())
                                        <span class="badge bg-warning text-dark">{{ $product->expiry_date->format('d/m/Y') }}</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $product->expiry_date->format('d/m/Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($product->status == 'active')
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('inventory.show', $product) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.edit', $product) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-quantity="{{ $product->quantity }}">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-product" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-search fs-1 text-muted mb-2"></i>
                                    <h5>Aucun produit trouvé</h5>
                                    <p class="text-muted">Essayez de modifier vos filtres ou d'ajouter un nouveau produit</p>
                                    <a href="{{ route('inventory.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus"></i> Ajouter un produit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $products->appends(request()->all())->links() }}
        </div>
    </div>
</div>

<!-- Modal d'ajustement de stock -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Ajuster le stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustStockForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produit</label>
                        <input type="text" class="form-control" id="adjust-product-name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock actuel</label>
                        <input type="text" class="form-control" id="adjust-current-stock" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="adjustment_type" class="form-label">Type d'ajustement</label>
                        <select id="adjustment_type" name="adjustment_type" class="form-select" required>
                            <option value="add">Ajouter au stock</option>
                            <option value="subtract">Retirer du stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Raison</label>
                        <select id="reason" name="reason" class="form-select" required>
                            <option value="correction d'inventaire">Correction d'inventaire</option>
                            <option value="produits endommagés">Produits endommagés</option>
                            <option value="produits expirés">Produits expirés</option>
                            <option value="produits perdus">Produits perdus</option>
                            <option value="usage interne">Usage interne</option>
                            <option value="don">Don</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'importation -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importer des produits</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Fichier CSV</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" required accept=".csv,.xlsx,.xls">
                        <small class="form-text text-muted">
                            Formats acceptés: CSV, Excel (.xlsx, .xls)
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="import_option" class="form-label">Option d'importation</label>
                        <select id="import_option" name="import_option" class="form-select" required>
                            <option value="add">Ajouter ou mettre à jour</option>
                            <option value="replace">Remplacer tous les produits</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <span>Téléchargez le <a href="{{ route('inventory.template') }}">modèle d'importation</a> pour vous assurer que votre fichier est correctement formaté.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Importer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulaire de suppression -->
<form id="delete-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration pour le modal d'ajustement de stock
        const adjustStockModal = document.getElementById('adjustStockModal');
        adjustStockModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const productQuantity = button.getAttribute('data-product-quantity');
            
            document.getElementById('adjust-product-name').value = productName;
            document.getElementById('adjust-current-stock').value = productQuantity;
            
            const form = document.getElementById('adjustStockForm');
            form.action = `/inventory/${productId}/adjust-stock`;
        });
        
        // Configuration pour la suppression de produit
        const deleteButtons = document.querySelectorAll('.delete-product');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                
                if (confirm(`Êtes-vous sûr de vouloir supprimer le produit "${productName}" ?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/inventory/${productId}`;
                    form.submit();
                }
            });
        });
    });
</script>
@endpush