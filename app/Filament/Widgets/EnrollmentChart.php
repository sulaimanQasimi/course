<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class EnrollmentChart extends ChartWidget
{
    protected ?string $heading = 'Enrollment Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
