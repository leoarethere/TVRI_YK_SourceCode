<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FrontendAssetHygieneTest extends TestCase
{
    #[Test]
    public function application_entry_does_not_initialize_removed_ui_libraries(): void
    {
        $entry = file_get_contents(resource_path('js/app.js'));

        $this->assertNotFalse($entry);
        $this->assertStringNotContainsString("from 'jquery'", $entry);
        $this->assertStringNotContainsString("'owl.carousel'", $entry);
        $this->assertStringNotContainsString("'flowbite'", $entry);
        $this->assertStringNotContainsString('console.log(', $entry);
    }

    #[Test]
    public function removed_ui_libraries_are_not_direct_dependencies(): void
    {
        $package = json_decode(
            file_get_contents(base_path('package.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $dependencies = array_merge(
            $package['dependencies'] ?? [],
            $package['devDependencies'] ?? [],
        );

        foreach (['jquery', 'owl.carousel', 'flowbite', 'flowbite-typography'] as $packageName) {
            $this->assertArrayNotHasKey($packageName, $dependencies);
        }
    }

    #[Test]
    public function turbo_submission_state_can_be_reused_after_a_failed_request(): void
    {
        $entry = file_get_contents(resource_path('js/app.js'));

        $this->assertNotFalse($entry);
        $this->assertStringContainsString("submitButton.removeAttribute('data-original-text')", $entry);
        $this->assertStringContainsString("errorDiv.setAttribute('role', 'alert')", $entry);
        $this->assertStringContainsString('Terjadi gangguan jaringan. Silakan coba lagi.', $entry);
    }
}
