<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class PublicPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_public_pages_load_with_the_shared_layout_assets(): void
    {
        $routes = [
            'home',
            'public.projects',
            'public.services',
            'public.services.web',
            'public.services.app',
            'public.services.faq',
            'public.about',
            'public.stack',
            'public.contact',
        ];

        foreach ($routes as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee(Vite::asset('resources/css/public-layout.css'), false)
                ->assertSee(Vite::asset('resources/js/public-layout.js'), false)
                ->assertSee(Vite::asset('resources/css/spotlight.css'), false)
                ->assertSee(Vite::asset('resources/js/spotlight.js'), false)
                ->assertSee('id="scroll-progress-container"', false)
                ->assertSee('id="scrollToTopBtn"', false);
        }
    }

    public function test_feature_scripts_are_only_loaded_by_views_that_need_them(): void
    {
        $faqAsset = Vite::asset('resources/js/public-faq-accordion.js');
        $documentationAsset = Vite::asset('resources/js/documentation-notes.js');

        $this->get(route('public.services.faq'))
            ->assertOk()
            ->assertSee($faqAsset, false)
            ->assertDontSee($documentationAsset, false);

        $this->get(route('public.documentation', ['slug' => 'musical-luthier']))
            ->assertOk()
            ->assertSee($documentationAsset, false)
            ->assertDontSee($faqAsset, false);

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertDontSee($faqAsset, false)
            ->assertDontSee($documentationAsset, false);
    }
}
