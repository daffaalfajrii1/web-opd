<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class WebsiteSetting extends Model
{
    public const MARQUEE_OFF = 'off';

    public const MARQUEE_CUSTOM = 'custom';

    public const MARQUEE_PENGUMUMAN = 'pengumuman';

    public const CAROUSEL_DURATION_MIN = 2;

    public const CAROUSEL_DURATION_MAX = 30;

    public const CAROUSEL_DURATION_DEFAULT = 5;

    public const THEME_TEAL = 'teal';

    public const THEME_ORANGE = 'orange';

    public const THEME_RED = 'red';

    public const THEME_GREEN = 'green';

    public const THEME_BLUE = 'blue';

    public const THEME_NAVY = 'navy';

    protected $fillable = [
        'site_name',
        'site_tagline',
        'theme_color',
        'logo_path',
        'favicon_path',
        'carousel_enabled',
        'carousel_duration',
        'marquee_enabled',
        'marquee_mode',
        'marquee_text',
        'google_maps_embed',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_whatsapp',
        'social_facebook',
        'social_instagram',
        'social_youtube',
        'footer_text',
    ];

    protected function casts(): array
    {
        return [
            'marquee_enabled' => 'boolean',
            'carousel_enabled' => 'boolean',
            'carousel_duration' => 'integer',
        ];
    }

    public static function current(): self
    {
        return Cache::remember('website_settings', 3600, function () {
            if (! Schema::hasTable('website_settings')) {
                return new self(self::defaults());
            }

            return self::query()->first() ?? self::query()->create(self::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('website_settings');
        Cache::forget('website_carousels_active');
        Cache::forget('public_profil_pages');
    }

    /**
     * Preset warna tema siap pakai.
     *
     * @return array<string, array{label: string, primary: string, dark: string, light: string}>
     */
    public static function themes(): array
    {
        return [
            self::THEME_TEAL => [
                'label' => 'Teal',
                'primary' => '#005461',
                'dark' => '#003d47',
                'light' => '#007d91',
            ],
            self::THEME_ORANGE => [
                'label' => 'Oranye',
                'primary' => '#c2410c',
                'dark' => '#9a3412',
                'light' => '#ea580c',
            ],
            self::THEME_RED => [
                'label' => 'Merah',
                'primary' => '#b91c1c',
                'dark' => '#7f1d1d',
                'light' => '#dc2626',
            ],
            self::THEME_GREEN => [
                'label' => 'Hijau',
                'primary' => '#166534',
                'dark' => '#14532d',
                'light' => '#22c55e',
            ],
            self::THEME_BLUE => [
                'label' => 'Biru',
                'primary' => '#1d4ed8',
                'dark' => '#1e3a8a',
                'light' => '#3b82f6',
            ],
            self::THEME_NAVY => [
                'label' => 'Navy',
                'primary' => '#1e3a5f',
                'dark' => '#0f2744',
                'light' => '#2f5f98',
            ],
        ];
    }

    /** @return list<string> */
    public static function themeKeys(): array
    {
        return array_keys(self::themes());
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'site_name' => 'Web OPD Rejang Lebong',
            'site_tagline' => 'Website Resmi OPD Kabupaten Rejang Lebong',
            'theme_color' => self::THEME_TEAL,
            'marquee_enabled' => true,
            'marquee_mode' => self::MARQUEE_CUSTOM,
            'marquee_text' => 'Selamat datang di website resmi OPD Kabupaten Rejang Lebong.',
            'contact_address' => 'Kabupaten Rejang Lebong, Bengkulu',
            'contact_phone' => '(0732) 000000',
            'contact_email' => 'info@rejanglebongkab.go.id',
            'contact_whatsapp' => '6281200000000',
            'footer_text' => 'Website Resmi OPD Kabupaten Rejang Lebong.',
            'carousel_enabled' => true,
            'carousel_duration' => self::CAROUSEL_DURATION_DEFAULT,
        ];
    }

    public function carouselDurationMs(): int
    {
        $seconds = (int) ($this->carousel_duration ?: self::CAROUSEL_DURATION_DEFAULT);

        return max(self::CAROUSEL_DURATION_MIN, min(self::CAROUSEL_DURATION_MAX, $seconds)) * 1000;
    }

    public function siteName(): string
    {
        return $this->site_name ?: 'Web OPD Rejang Lebong';
    }

    public function shortName(): string
    {
        return $this->siteName();
    }

    public function themeKey(): string
    {
        $key = (string) ($this->theme_color ?: self::THEME_TEAL);

        return array_key_exists($key, self::themes()) ? $key : self::THEME_TEAL;
    }

    /** @return array{label: string, primary: string, dark: string, light: string} */
    public function theme(): array
    {
        return self::themes()[$this->themeKey()];
    }

    public function themeCssVariables(): string
    {
        $theme = $this->theme();
        $primaryRgb = $this->hexToRgb($theme['primary']);
        $lightRgb = $this->hexToRgb($theme['light']);

        return implode(
            '',
            [
                '--primary-color: '.$theme['primary'].';',
                '--primary-color-rgb: '.$primaryRgb.';',
                '--secondary-color: '.$theme['light'].';',
                '--secondary-color-rgb: '.$lightRgb.';',
                '--theme-primary: '.$theme['primary'].';',
                '--theme-dark: '.$theme['dark'].';',
                '--theme-light: '.$theme['light'].';',
                '--bg-dark-1: '.$theme['primary'].';',
                '--bg-dark-1-rgb: '.$primaryRgb.';',
                '--bg-dark-2: '.$theme['dark'].';',
                '--bg-dark-3: '.$theme['dark'].';',
                '--id-color: '.$theme['primary'].';',
                '--btn-hover-bg: '.$theme['dark'].';',
            ]
        );
    }

    public function defaultShareImageUrl(): string
    {
        return $this->logoUrl()
            ?? $this->faviconUrl()
            ?? asset('optilux/images/logo-dark.webp');
    }

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return asset('storage/'.$this->logo_path);
    }

    public function faviconUrl(): ?string
    {
        if ($this->favicon_path) {
            return asset('storage/'.$this->favicon_path);
        }

        return $this->logoUrl();
    }

    public function mapsIframeSrc(): ?string
    {
        $embed = trim((string) $this->google_maps_embed);
        if ($embed === '') {
            return null;
        }

        if (preg_match('/src=["\']([^"\']+)["\']/i', $embed, $matches)) {
            return $matches[1];
        }

        if (filter_var($embed, FILTER_VALIDATE_URL)) {
            return $embed;
        }

        return null;
    }

    /** @return list<string> */
    public function marqueeItems(): array
    {
        if (! $this->marquee_enabled || $this->marquee_mode === self::MARQUEE_OFF) {
            return [];
        }

        if ($this->marquee_mode === self::MARQUEE_PENGUMUMAN) {
            if (! Schema::hasTable('pengumumans')) {
                return [];
            }

            return Pengumuman::query()
                ->published()
                ->orderByDesc('published_at')
                ->limit(8)
                ->pluck('title')
                ->all();
        }

        if ($this->marquee_text) {
            $lines = preg_split('/\r\n|\r|\n|\|/', $this->marquee_text) ?: [];

            return array_values(array_filter(array_map('trim', $lines)));
        }

        return [];
    }

    public function whatsappUrl(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->contact_whatsapp);
        if ($number === '') {
            return null;
        }

        return 'https://wa.me/'.$number;
    }

    private function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return '0, 84, 97';
        }

        return hexdec(substr($hex, 0, 2)).', '.hexdec(substr($hex, 2, 2)).', '.hexdec(substr($hex, 4, 2));
    }
}
