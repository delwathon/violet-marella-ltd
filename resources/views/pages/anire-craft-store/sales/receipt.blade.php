<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->receipt_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @media print {
            .no-print { display: none !important; }
            @page {
                size: 72mm auto;
                margin: 0;
            }

            html, body {
                width: 72mm;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            .receipt-a4 { max-width: 100%; }

            #receipt-a4 { display: none !important; }
            #receipt-thermal {
                display: block !important;
                width: 72mm !important;
                max-width: 72mm !important;
                margin: 0 !important;
                padding: 1.8mm 1.6mm !important;
                font-size: 12px !important;
                font-weight: 600 !important;
            }

            #receipt-thermal .small {
                font-size: 11.5px !important;
            }
        }

        .receipt-thermal { 
            width: 72mm;
            max-width: 72mm;
            margin: 0 auto; 
            padding: 1.8mm 1.6mm !important;
            font-family: 'Consolas', 'Lucida Console', 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
            font-weight: 600;
        }

        .receipt-thermal * {
            color: #000 !important;
            text-shadow: none !important;
        }

        .receipt-thermal .small {
            font-size: 11.6px !important;
            font-weight: 600;
        }

        .receipt-thermal strong,
        .receipt-thermal .fw-bold {
            font-weight: 800 !important;
        }

        .thermal-header {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .thermal-separator {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }
    </style>
</head>
<body class="bg-light">
    @php
        $business = $businessProfile ?? \App\Support\BusinessProfile::forSlug('gift_store');
        $company = $companyProfile ?? \App\Support\BusinessProfile::company();
        $receiptFooter = $receiptFooterMessage ?? \App\Models\Setting::get('receipt_footer_message', 'Thank you for shopping with us!');
    @endphp

    <!-- Print Controls -->
    <div class="no-print position-fixed top-0 end-0 m-3 p-3 bg-white rounded shadow" style="z-index: 1000;">
        <h6 class="mb-3">Print Options</h6>
        <div class="d-grid gap-2">
            <button onclick="switchFormat('a4')" class="btn btn-primary btn-sm">
                <i class="fas fa-file-alt"></i> A4 Format
            </button>
            <button onclick="switchFormat('thermal')" class="btn btn-info btn-sm">
                <i class="fas fa-receipt"></i> Thermal Format
            </button>
            <hr>
            <button onclick="window.print()" class="btn btn-success btn-sm">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>

    <!-- A4 Receipt -->
    <div id="receipt-a4" class="receipt-a4 d-none container my-5">
        <div class="card shadow-lg">
            <div class="card-body p-5">
                <!-- Company Header -->
                <div class="text-center border-bottom pb-4 mb-4">
                    <h2 class="fw-bold text-primary mb-2">{{ $business['name'] ?? $company['name'] }}</h2>
                    @if(!empty($business['legal_name']) && ($business['legal_name'] !== ($business['name'] ?? '')))
                        <p class="text-muted mb-1">{{ $business['legal_name'] }}</p>
                    @endif
                    @if(!empty($business['address']))
                        <p class="text-muted mb-1">{{ $business['address'] }}</p>
                    @endif
                    <p class="text-muted small mb-0">
                        @if(!empty($business['phone']))
                            Phone: {{ $business['phone'] }}
                        @endif
                        @if(!empty($business['email']))
                            {{ !empty($business['phone']) ? ' | ' : '' }}Email: {{ $business['email'] }}
                        @endif
                        @if(!empty($business['rc_number']))
                            <br>RC: {{ $business['rc_number'] }}
                        @endif
                    </p>
                </div>

                <!-- Receipt Info -->
                <div class="row mb-4">
                    <div class="col-6">
                        <p class="mb-2"><strong>Receipt #:</strong> {{ $sale->receipt_number }}</p>
                        <p class="mb-2"><strong>Cashier:</strong> {{ $sale->staff->full_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-2"><strong>Date:</strong> {{ $sale->sale_date->format('M d, Y H:i') }}</p>
                        <p class="mb-2"><strong>Customer:</strong> {{ $sale->customer->full_name ?? 'Walk-in Customer' }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>SKU</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->saleItems as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_sku }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">₦{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">₦{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end">₦{{ number_format($sale->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tax (7.5%):</strong></td>
                                <td class="text-end">₦{{ number_format($sale->tax_amount, 2) }}</td>
                            </tr>
                            @if($sale->discount_amount > 0)
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td class="text-end text-success">-₦{{ number_format($sale->discount_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="table-dark">
                                <td><strong>TOTAL:</strong></td>
                                <td class="text-end"><strong>₦{{ number_format($sale->total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method:</strong></td>
                                <td class="text-end">{{ strtoupper($sale->payment_method) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid:</strong></td>
                                <td class="text-end">₦{{ number_format($sale->amount_paid, 2) }}</td>
                            </tr>
                            @if($sale->change_amount > 0)
                                <tr>
                                    <td><strong>Change:</strong></td>
                                    <td class="text-end">₦{{ number_format($sale->change_amount, 2) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-5 pt-4 border-top border-dashed">
                    <p class="fw-bold mb-2">{{ $receiptFooter }}</p>
                    <p class="text-muted small mb-1">Please keep this receipt for your records</p>
                    @if(!empty($business['phone']))
                        <p class="text-muted small">For inquiries, please contact us at {{ $business['phone'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Thermal Receipt -->
    <div id="receipt-thermal" class="receipt-thermal p-3 bg-white">
        <!-- Company Header -->
        <div class="text-center thermal-header mb-2">
            {{ strtoupper($business['name'] ?? $company['name']) }}
        </div>
        <div class="text-center small mb-2">
            @if(!empty($business['legal_name']) && ($business['legal_name'] !== ($business['name'] ?? '')))
                {{ $business['legal_name'] }}<br>
            @endif
            @if(!empty($business['address']))
                {{ $business['address'] }}<br>
            @endif
            @if(!empty($business['phone']))
                Tel: {{ $business['phone'] }}
            @endif
        </div>
        <div class="thermal-separator"></div>

        <!-- Receipt Info -->
        <div class="small mb-2">
            <div><strong>Receipt:</strong> {{ $sale->receipt_number }}</div>
            <div><strong>Date:</strong> {{ $sale->sale_date->format('M d, Y H:i') }}</div>
            <div><strong>Cashier:</strong> {{ $sale->staff->full_name ?? 'N/A' }}</div>
            @if($sale->customer)
                <div><strong>Customer:</strong> {{ $sale->customer->full_name }}</div>
            @endif
        </div>
        <div class="thermal-separator"></div>

        <!-- Items -->
        @foreach($sale->saleItems as $item)
            <div class="mb-2">
                <div class="fw-bold">{{ $item->product->name }}</div>
                <div class="d-flex justify-content-between small">
                    <span>{{ $item->quantity }} x ₦{{ number_format($item->unit_price, 2) }}</span>
                    <span>₦{{ number_format($item->total_price, 2) }}</span>
                </div>
            </div>
        @endforeach
        <div class="thermal-separator"></div>

        <!-- Totals -->
        <div class="small">
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal:</span>
                <span>₦{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Tax:</span>
                <span>₦{{ number_format($sale->tax_amount, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
                <div class="d-flex justify-content-between mb-1">
                    <span>Discount:</span>
                    <span>-₦{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="thermal-separator"></div>
            <div class="d-flex justify-content-between fw-bold">
                <span>TOTAL:</span>
                <span>₦{{ number_format($sale->total_amount, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <span>Payment:</span>
                <span>{{ strtoupper($sale->payment_method) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Paid:</span>
                <span>₦{{ number_format($sale->amount_paid, 2) }}</span>
            </div>
            @if($sale->change_amount > 0)
                <div class="d-flex justify-content-between">
                    <span>Change:</span>
                    <span>₦{{ number_format($sale->change_amount, 2) }}</span>
                </div>
            @endif
        </div>

        <!-- Barcode -->
        <!-- <div class="text-center my-3" style="font-family: 'Libre Barcode 39', cursive; font-size: 24px;">
            *{{ $sale->receipt_number }}*
        </div> -->

        <!-- Footer -->
        <div class="thermal-separator"></div>
        <div class="text-center small">
            <p class="mb-1 fw-bold">{{ $receiptFooter }}</p>
            <p class="mb-1">Please keep this receipt</p>
            @if(!empty($business['phone']))
                <p class="mb-0">Tel: {{ $business['phone'] }}</p>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchFormat(format) {
            const a4 = document.getElementById('receipt-a4');
            const thermal = document.getElementById('receipt-thermal');
            
            if (format === 'a4') {
                a4.classList.remove('d-none');
                thermal.classList.add('d-none');
            } else {
                a4.classList.add('d-none');
                thermal.classList.remove('d-none');
            }
        }
    </script>
</body>
</html>
