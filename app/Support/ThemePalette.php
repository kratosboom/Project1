<?php

namespace App\Support;

class ThemePalette
{
    /**
     * @return array<string, array{label: string, primary: string, secondary: string, bg: string, nav: string}>
     */
    public static function presets(): array
    {
        return [
            'crimson' => [
                'label' => 'Crimson Red',
                'primary' => '#fb2323',
                'secondary' => '#b30000',
                'bg' => '#111827',
                'nav' => '#1f2937',
            ],
            'royal-blue' => [
                'label' => 'Royal Blue',
                'primary' => '#3b82f6',
                'secondary' => '#1d4ed8',
                'bg' => '#0b1220',
                'nav' => '#1e293b',
            ],
            'emerald' => [
                'label' => 'Emerald Green',
                'primary' => '#10b981',
                'secondary' => '#047857',
                'bg' => '#0b1b17',
                'nav' => '#1f2937',
            ],
            'violet' => [
                'label' => 'Violet Purple',
                'primary' => '#8b5cf6',
                'secondary' => '#6d28d9',
                'bg' => '#140f26',
                'nav' => '#1f1b35',
            ],
            'amber' => [
                'label' => 'Amber Gold',
                'primary' => '#f59e0b',
                'secondary' => '#b45309',
                'bg' => '#1a1306',
                'nav' => '#2b1f0a',
            ],
            'rose' => [
                'label' => 'Rose Pink',
                'primary' => '#f43f5e',
                'secondary' => '#be123c',
                'bg' => '#1a0f17',
                'nav' => '#2b1623',
            ],
        ];
    }

    /**
     * @return array{key: string, label: string, primary: string, secondary: string, bg: string, nav: string}
     */
    public static function resolve(?string $key): array
    {
        $presets = self::presets();
        $key = is_string($key) && isset($presets[$key]) ? $key : 'crimson';
        $theme = $presets[$key];

        return [
            'key' => $key,
            'label' => $theme['label'],
            'primary' => $theme['primary'],
            'secondary' => $theme['secondary'],
            'bg' => $theme['bg'],
            'nav' => $theme['nav'],
        ];
    }
}

