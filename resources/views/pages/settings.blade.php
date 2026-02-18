@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold">Settings</h1>
        <p class="text-muted mb-0">Update business, payment, inventory and system configuration.</p>
    </div>

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Business</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Store Name</label><input type="text" name="store_name" class="form-control" value="{{ old('store_name', $settings['store_name']) }}" required></div>
                        <div class="mb-3"><label class="form-label">Address</label><textarea name="store_address" class="form-control" rows="2">{{ old('store_address', $settings['store_address']) }}</textarea></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $settings['store_phone']) }}"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="store_email" class="form-control" value="{{ old('store_email', $settings['store_email']) }}"></div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-6"><label class="form-label">Currency Code</label><input type="text" name="store_currency" class="form-control" value="{{ old('store_currency', $settings['store_currency']) }}"></div>
                            <div class="col-md-6"><label class="form-label">Currency Symbol</label><input type="text" name="store_currency_symbol" class="form-control" value="{{ old('store_currency_symbol', $settings['store_currency_symbol']) }}"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Tax & Payment</h5></div>
                    <div class="card-body">
                        <div class="row g-3 mb-2">
                            <div class="col-md-6"><label class="form-label">Default Tax Rate (%)</label><input type="number" name="default_tax_rate" step="0.01" min="0" max="100" class="form-control" value="{{ old('default_tax_rate', $settings['default_tax_rate']) }}"></div>
                            <div class="col-md-6"><label class="form-label">Min Cash Drawer</label><input type="number" name="minimum_cash_drawer_amount" step="0.01" min="0" class="form-control" value="{{ old('minimum_cash_drawer_amount', $settings['minimum_cash_drawer_amount']) }}"></div>
                        </div>
                        <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="tax_inclusive_pricing" id="tax_inclusive_pricing" {{ old('tax_inclusive_pricing', $settings['tax_inclusive_pricing']) ? 'checked' : '' }}><label class="form-check-label" for="tax_inclusive_pricing">Use tax-inclusive pricing</label></div>
                        <div class="mb-3">
                            <label class="form-label d-block">Accepted Payment Methods</label>
                            @php $acceptedMethods = old('accepted_payment_methods', $settings['accepted_payment_methods'] ?? []); @endphp
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="accepted_payment_methods[]" value="cash" id="method_cash" {{ in_array('cash', $acceptedMethods, true) ? 'checked' : '' }}><label class="form-check-label" for="method_cash">Cash</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="accepted_payment_methods[]" value="card" id="method_card" {{ in_array('card', $acceptedMethods, true) ? 'checked' : '' }}><label class="form-check-label" for="method_card">Card</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="accepted_payment_methods[]" value="transfer" id="method_transfer" {{ in_array('transfer', $acceptedMethods, true) ? 'checked' : '' }}><label class="form-check-label" for="method_transfer">Transfer</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="accepted_payment_methods[]" value="mobile_money" id="method_mobile_money" {{ in_array('mobile_money', $acceptedMethods, true) ? 'checked' : '' }}><label class="form-check-label" for="method_mobile_money">Mobile Money</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="accepted_payment_methods[]" value="split" id="method_split" {{ in_array('split', $acceptedMethods, true) ? 'checked' : '' }}><label class="form-check-label" for="method_split">Split Payment</label></div>
                        </div>
                        <div class="mb-0"><label class="form-label">Receipt Footer Message</label><textarea name="receipt_footer_message" class="form-control" rows="2">{{ old('receipt_footer_message', $settings['receipt_footer_message']) }}</textarea></div>
                        <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="receipt_show_tax_breakdown" id="receipt_show_tax_breakdown" {{ old('receipt_show_tax_breakdown', $settings['receipt_show_tax_breakdown']) ? 'checked' : '' }}><label class="form-check-label" for="receipt_show_tax_breakdown">Show tax breakdown on receipt</label></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Inventory & Loyalty</h5></div>
                    <div class="card-body">
                        <div class="mb-3"><label class="form-label">Low Stock Threshold</label><input type="number" name="low_stock_threshold" class="form-control" min="0" value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}"></div>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="track_inventory" id="track_inventory" {{ old('track_inventory', $settings['track_inventory']) ? 'checked' : '' }}><label class="form-check-label" for="track_inventory">Enable inventory tracking</label></div>
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="loyalty_points_enabled" id="loyalty_points_enabled" {{ old('loyalty_points_enabled', $settings['loyalty_points_enabled']) ? 'checked' : '' }}><label class="form-check-label" for="loyalty_points_enabled">Enable loyalty points</label></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Loyalty Points Rate</label><input type="number" name="loyalty_points_rate" step="0.01" min="0" class="form-control" value="{{ old('loyalty_points_rate', $settings['loyalty_points_rate']) }}"></div>
                            <div class="col-md-6"><label class="form-label">Loyalty Discount Rate (%)</label><input type="number" name="loyalty_discount_rate" step="0.01" min="0" max="100" class="form-control" value="{{ old('loyalty_discount_rate', $settings['loyalty_discount_rate']) }}"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">System</h5></div>
                    <div class="card-body">
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="backup_enabled" id="backup_enabled" {{ old('backup_enabled', $settings['backup_enabled']) ? 'checked' : '' }}><label class="form-check-label" for="backup_enabled">Enable automatic backups</label></div>
                        <div class="mb-0"><label class="form-label">Auto Logout (minutes)</label><input type="number" name="auto_logout_minutes" min="5" max="1440" class="form-control" value="{{ old('auto_logout_minutes', $settings['auto_logout_minutes']) }}"></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save me-2"></i>Save Settings</button>
            </div>
        </div>
    </form>
</div>
@endsection
