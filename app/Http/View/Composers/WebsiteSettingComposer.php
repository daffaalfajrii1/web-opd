<?php

namespace App\Http\View\Composers;

use App\Models\WebsiteCarousel;
use App\Models\WebsiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class WebsiteSettingComposer
{
    public function compose(View $view): void
    {
        $settings = WebsiteSetting::current();
        $payload = ['websiteSettings' => $settings];

        // Carousel hanya dibutuhkan di halaman publik; skip query di admin/auth.
        if (str_starts_with((string) $view->name(), 'public.')) {
            $payload['websiteCarousels'] = $this->activeCarousels($settings);
        }

        $view->with($payload);
    }

    private function activeCarousels(WebsiteSetting $settings): Collection
    {
        if (! $settings->carousel_enabled || ! Schema::hasTable('website_carousels')) {
            return collect();
        }

        return Cache::remember('website_carousels_active', 3600, function () {
            return WebsiteCarousel::query()->active()->get();
        });
    }
}
