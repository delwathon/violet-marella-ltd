<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomerMessagingViewsTest extends TestCase
{
    public function test_lounge_customer_view_exposes_messaging_actions(): void
    {
        $this->assertMessagingActionsExistIn(
            resource_path('views/pages/lounge/customers/show.blade.php')
        );
    }

    public function test_store_customer_view_exposes_messaging_actions(): void
    {
        $this->assertMessagingActionsExistIn(
            resource_path('views/pages/anire-craft-store/customers/show.blade.php')
        );
    }

    private function assertMessagingActionsExistIn(string $path): void
    {
        $contents = file_get_contents($path);

        $this->assertNotFalse($contents, "Failed to read view: {$path}");
        $this->assertStringContainsString('messageCustomerModal', $contents);
        $this->assertStringContainsString('sendCustomerMessage(', $contents);
        $this->assertStringContainsString('mailto:', $contents);
        $this->assertStringContainsString('https://wa.me/', $contents);
        $this->assertStringContainsString('sms:', $contents);
        $this->assertStringContainsString('formatPhoneForWhatsApp', $contents);
    }
}
