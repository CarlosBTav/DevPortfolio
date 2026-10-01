<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $pages = [
            ['route' => 'home', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['route' => 'public.projects', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['route' => 'public.services', 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['route' => 'public.services.web', 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['route' => 'public.services.app', 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['route' => 'public.about', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'public.stack', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'public.services.faq', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['route' => 'public.contact', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        return response()
            ->view('seo.sitemap', compact('pages'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $contents = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /dashboard',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /profile',
            'Disallow: /projects',
            'Disallow: /clients',
            'Disallow: /messages',
            '',
            'Sitemap: '.route('seo.sitemap'),
            '',
        ]);

        return response($contents)->header('Content-Type', 'text/plain');
    }
}
