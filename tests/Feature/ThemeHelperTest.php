<?php

namespace Tests\Feature;

use Illuminate\Contracts\View\View;
use Molitor\Theme\Services\ThemeHelper;
use Tests\TestCase;

class ThemeHelperTest extends TestCase
{
    public function test_theme_helper_can_be_resolved_from_container(): void
    {
        $helper = app(ThemeHelper::class);

        $this->assertInstanceOf(ThemeHelper::class, $helper);
    }

    public function test_theme_helper_view_method_returns_view(): void
    {
        $helper = app(ThemeHelper::class);

        // Create a simple test view
        $view = $helper->view('cms::layouts.partials.header');

        $this->assertInstanceOf(View::class, $view);
    }

    public function test_get_package_name_extracts_view_path(): void
    {
        $helper = app(ThemeHelper::class);

        $result = $helper->getPackageName('cms::layouts.partials.header');

        $this->assertEquals('layouts.partials.header', $result);
    }

    public function test_get_package_name_returns_view_when_no_package(): void
    {
        $helper = app(ThemeHelper::class);

        $result = $helper->getPackageName('layouts.partials.header');

        $this->assertEquals('layouts.partials.header', $result);
    }
}
