<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ScrapeLogoService
{
    public function extractLogoUrl(string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        try {
            $res = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; RTP-LogoBot/1.0)'])
                ->get($url);
            if (! $res->successful()) {
                return null;
            }
            $html = $res->body();
        } catch (\Throwable) {
            return null;
        }

        if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            return $this->toAbsolute($m[1], $url);
        }
        if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $m)) {
            return $this->toAbsolute($m[1], $url);
        }
        if (preg_match('/<link[^>]+rel=["\']apple-touch-icon["\'][^>]+href=["\']([^"\']+)["\']/i', $html, $m)) {
            return $this->toAbsolute($m[1], $url);
        }
        if (preg_match('/<link[^>]+rel=["\']icon["\'][^>]+href=["\']([^"\']+)["\']/i', $html, $m)) {
            return $this->toAbsolute($m[1], $url);
        }
        if (preg_match('/<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\'][^"\']*icon[^"\']*["\']/i', $html, $m)) {
            return $this->toAbsolute($m[1], $url);
        }

        return null;
    }

    private function toAbsolute(string $path, string $base): string
    {
        $path = html_entity_decode(trim($path), ENT_QUOTES, 'UTF-8');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $parts = parse_url($base);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');
        if (str_starts_with($path, '//')) {
            return ($parts['scheme'] ?? 'https').':'.$path;
        }
        if (str_starts_with($path, '/')) {
            return $origin.$path;
        }

        return rtrim($origin, '/').'/'.ltrim($path, '/');
    }
}
