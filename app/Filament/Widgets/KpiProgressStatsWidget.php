<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class KpiProgressStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.kpi-progress-stats-widget';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'stats' => [
                ['label' => 'Total KPI', 'value' => 'N/A'],
                ['label' => 'Completed', 'value' => 'N/A'],
                ['label' => 'Pending', 'value' => 'N/A'],
            ],
        ];
    }
}
