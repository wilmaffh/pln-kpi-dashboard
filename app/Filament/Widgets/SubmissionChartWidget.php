<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SubmissionChartWidget extends Widget
{
    protected static string $view = 'filament.widgets.submission-chart-widget';
    protected string | int | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'title' => 'Submission Chart',
            'description' => 'Placeholder chart widget for KPI submissions.',
        ];
    }
}
