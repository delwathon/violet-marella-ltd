<?php

namespace Tests\Feature;

use App\Http\Middleware\LogUserActivity;
use App\Models\User;
use Tests\TestCase;

class PurchaseOrderFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(LogUserActivity::class);
    }

    public function test_guests_are_redirected_from_purchase_order_routes(): void
    {
        $this->get(route('lounge.inventory.purchase-order'))
            ->assertRedirect(route('login'));

        $this->get(route('anire-craft-store.inventory.purchase-order'))
            ->assertRedirect(route('login'));
    }

    public function test_lounge_purchase_order_requires_selected_products(): void
    {
        $this->actingAs($this->makeUser('admin'), 'user')
            ->get(route('lounge.inventory.purchase-order'))
            ->assertRedirect(route('lounge.inventory.low-stock'))
            ->assertSessionHas('error', 'Select at least one product to generate a purchase order.');
    }

    public function test_store_purchase_order_requires_selected_products(): void
    {
        $this->actingAs($this->makeUser('admin'), 'user')
            ->get(route('anire-craft-store.inventory.purchase-order'))
            ->assertRedirect(route('anire-craft-store.inventory.low-stock'))
            ->assertSessionHas('error', 'Select at least one product to generate a purchase order.');
    }

    private function makeUser(string $role): User
    {
        $user = new User();
        $user->id = 1001;
        $user->first_name = 'Test';
        $user->last_name = 'Operator';
        $user->email = 'operator@example.com';
        $user->role = $role;
        $user->is_active = true;

        return $user;
    }
}
