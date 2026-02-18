@extends('layouts.app')
@section('title', 'Customer Profile')

@section('content')
<div class="content-area">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">{{ $customer->full_name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('anire-craft-store.customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">{{ $customer->full_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('anire-craft-store.customers.edit', $customer->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('anire-craft-store.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Full Name:</th>
                                    <td><strong>{{ $customer->full_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $customer->email ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td>{{ $customer->date_of_birth?->format('M d, Y') ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Gender:</th>
                                    <td>{{ $customer->gender ? ucfirst($customer->gender) : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Customer Type:</th>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($customer->customer_type) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $customer->full_address ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Member Since:</th>
                                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $customer->updated_at->diffForHumans() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($customer->notes)
                    <div class="mt-3">
                        <h6>Notes</h6>
                        <p class="text-muted">{{ $customer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Purchase Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-box">
                                <h3 class="text-primary">{{ $totalPurchases }}</h3>
                                <small class="text-muted">Total Orders</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <h3 class="text-success">₦{{ number_format($totalSpent, 2) }}</h3>
                                <small class="text-muted">Total Spent</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <h3 class="text-info">₦{{ number_format($avgOrderValue, 2) }}</h3>
                                <small class="text-muted">Avg Order</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <h3 class="text-warning">{{ $lastPurchase?->format('M d') ?: 'Never' }}</h3>
                                <small class="text-muted">Last Purchase</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            @if($topProducts->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top Purchased Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td>{{ $product->total_quantity }} units</td>
                                        <td class="text-success">₦{{ number_format($product->total_spent, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Purchase History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Purchase History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt #</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('anire-craft-store.sale', $sale->id) }}">
                                                {{ $sale->receipt_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $sale->saleItems->count() }} items</span>
                                        </td>
                                        <td>
                                            <strong class="text-success">₦{{ number_format($sale->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $sale->payment_method === 'cash' ? 'success' : 'primary' }}">
                                                {{ ucfirst($sale->payment_method) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="" onclick="printReceipt({{ $sale->id }})" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No purchase history
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($sales->hasPages())
                        <div class="mt-3">
                            {{ $sales->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Loyalty Points -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Loyalty Points</h5>
                </div>
                <div class="card-body text-center">
                    <div class="loyalty-points-display">
                        <i class="fas fa-star fa-3x text-warning mb-2"></i>
                        <h2 class="mb-0">{{ number_format($customer->loyalty_points, 0) }}</h2>
                        <p class="text-muted">Points Available</p>
                    </div>
                    
                    @if($customer->isEligibleForLoyaltyDiscount())
                        <div class="alert alert-success">
                            <i class="fas fa-gift"></i> Eligible for 5% loyalty discount!
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-warning" onclick="showAdjustPointsModal('add')">
                            <i class="fas fa-plus"></i> Add Points
                        </button>
                        <button class="btn btn-outline-secondary" onclick="showAdjustPointsModal('deduct')">
                            <i class="fas fa-minus"></i> Deduct Points
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('anire-craft-store.index') }}" class="btn btn-success">
                            <i class="fas fa-shopping-cart"></i> New Sale
                        </a>
                        <a href="{{ route('anire-craft-store.customers.edit', $customer->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Customer
                        </a>
                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#messageCustomerModal">
                            <i class="fas fa-envelope"></i> Send Message
                        </button>
                        <hr>
                        <button class="btn btn-outline-danger" onclick="deleteCustomer()">
                            <i class="fas fa-trash"></i> Delete Customer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Points Modal -->
<div class="modal fade" id="adjustPointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustPointsTitle">Adjust Loyalty Points</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adjustPointsForm">
                    <input type="hidden" id="adjustAction">
                    
                    <div class="mb-3">
                        <label class="form-label">Current Points</label>
                        <input type="text" class="form-control" value="{{ number_format($customer->loyalty_points, 0) }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Points to <span id="actionLabel"></span> <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="adjustPointsAmount" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitPointsAdjustment()">
                    <i class="fas fa-save"></i> Adjust Points
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Message Customer Modal -->
<div class="modal fade" id="messageCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Message to {{ $customer->full_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" class="form-control" id="customerMessageSubject" value="Hello {{ $customer->full_name }}">
                </div>
                <div class="mb-0">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" id="customerMessageBody" rows="4">Hi {{ $customer->full_name }},</textarea>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="sendCustomerMessage('email')">
                        <i class="fas fa-envelope me-1"></i>Email
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="sendCustomerMessage('whatsapp')">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                    </button>
                    <button type="button" class="btn btn-outline-dark" onclick="sendCustomerMessage('sms')">
                        <i class="fas fa-comment-alt me-1"></i>SMS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.stat-box {
    padding: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.loyalty-points-display {
    padding: 2rem 1rem;
}
</style>
@endpush

@push('scripts')
<script>
const customerName = @json($customer->full_name);
const customerEmail = @json($customer->email);
const customerPhone = @json($customer->phone);

function showAdjustPointsModal(action) {
    document.getElementById('adjustAction').value = action;
    document.getElementById('actionLabel').textContent = action === 'add' ? 'Add' : 'Deduct';
    document.getElementById('adjustPointsTitle').textContent = `${action === 'add' ? 'Add' : 'Deduct'} Loyalty Points`;
    document.getElementById('adjustPointsAmount').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('adjustPointsModal'));
    modal.show();
}

async function submitPointsAdjustment() {
    const action = document.getElementById('adjustAction').value;
    const points = parseInt(document.getElementById('adjustPointsAmount').value);
    
    if (!points) {
        alert('Please enter points amount');
        return;
    }
    
    try {
        const response = await fetch('/app/anire-craft-store/customers/{{ $customer->id }}/loyalty', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                action: action,
                points: points
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('adjustPointsModal'));
            modal.hide();
            
            alert('Loyalty points adjusted successfully');
            window.location.reload();
        } else {
            alert(result.message || 'Failed to adjust points');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adjusting points');
    }
}

function sendCustomerMessage(channel) {
    const subject = document.getElementById('customerMessageSubject').value.trim() || `Hello ${customerName}`;
    const body = document.getElementById('customerMessageBody').value.trim();
    const encodedSubject = encodeURIComponent(subject);
    const encodedBody = encodeURIComponent(body);

    if (!body) {
        alert('Please enter a message before sending.');
        return;
    }

    if (channel === 'email') {
        if (!customerEmail) {
            alert('This customer does not have an email address.');
            return;
        }

        window.open(`mailto:${customerEmail}?subject=${encodedSubject}&body=${encodedBody}`, '_blank');
        return;
    }

    if (channel === 'whatsapp') {
        const whatsappPhone = formatPhoneForWhatsApp(customerPhone);

        if (!whatsappPhone) {
            alert('This customer does not have a valid phone number for WhatsApp.');
            return;
        }

        window.open(`https://wa.me/${whatsappPhone}?text=${encodedBody}`, '_blank');
        return;
    }

    if (channel === 'sms') {
        if (!customerPhone) {
            alert('This customer does not have a phone number.');
            return;
        }

        window.open(`sms:${customerPhone}?body=${encodedBody}`, '_blank');
    }
}

function formatPhoneForWhatsApp(phone) {
    if (!phone) {
        return '';
    }

    const trimmed = String(phone).trim();
    let digits = trimmed.replace(/[^\d]/g, '');

    if (digits.startsWith('0')) {
        digits = `234${digits.slice(1)}`;
    }

    return digits;
}

function deleteCustomer() {
    if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/app/anire-craft-store/customers/{{ $customer->id }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodField);
    document.body.appendChild(form);
    form.submit();
}

function printReceipt(saleId) {
    window.open(`/app/anire-craft-store/sales/${saleId}/receipt`, '_blank');
}
</script>
@endpush
@endsection
