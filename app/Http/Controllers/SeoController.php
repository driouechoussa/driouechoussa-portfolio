<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Support\Locale;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * sitemap.xml: every page, project and published article in every
     * language, with hreflang alternates.
     */
    public function sitemap(): Response
    {
        $entries = collect([
            ['route' => 'home', 'params' => [], 'lastmod' => null, 'priority' => '1.0'],
            ['route' => 'about', 'params' => [], 'lastmod' => null, 'priority' => '0.8'],
            ['route' => 'projects.index', 'params' => [], 'lastmod' => Project::max('updated_at'), 'priority' => '0.9'],
            ['route' => 'posts.index', 'params' => [], 'lastmod' => Post::published()->max('updated_at'), 'priority' => '0.9'],
            ['route' => 'contact', 'params' => [], 'lastmod' => null, 'priority' => '0.6'],
            ['route' => 'privacy', 'params' => [], 'lastmod' => null, 'priority' => '0.2'],
        ]);

        foreach (Project::ordered()->get() as $project) {
            $entries->push(['route' => 'projects.show', 'params' => [$project], 'lastmod' => $project->updated_at, 'priority' => '0.7']);
        }

        foreach (Post::published()->get() as $post) {
            $entries->push(['route' => 'posts.show', 'params' => [$post], 'lastmod' => $post->updated_at, 'priority' => '0.7']);
        }

        $urls = $entries->map(function (array $entry) {
            $alternates = collect(Locale::SUPPORTED)
                ->mapWithKeys(fn (string $locale) => [$locale => Locale::route($entry['route'], $entry['params'], $locale)]);

            return [
                'alternates' => $alternates,
                'lastmod' => $entry['lastmod'] ? \Illuminate\Support\Carbon::parse($entry['lastmod'])->toAtomString() : null,
                'priority' => $entry['priority'],
            ];
        });

        return response()
            ->view('seo.sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /']; // keep local / staging copies out of search engines

        return response(implode("\n", $lines)."\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
