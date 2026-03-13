<?php

namespace App\Console\Commands;

use App\Services\SjcChartPriceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportSjcChartFromHighcharts extends Command
{
    protected $signature = 'import:sjc-chart-highcharts
                            {--file=sjc_highcharts_1y.js : Relative path in storage/app containing Highcharts seriesOptions snippet}';

    protected $description = 'Parse Highcharts seriesOptions data (Ban ra/Mua vao) and import into sjc_chart_prices';

    public function handle(SjcChartPriceService $service): int
    {
        $file = (string) $this->option('file');

        if (!Storage::exists($file)) {
            $this->error("File not found: storage/app/{$file}");
            $this->line('Create the file first, then run this command again.');
            return self::FAILURE;
        }

        $raw = Storage::get($file);

        // Parse by splitting on series objects — handles any encoding for Vietnamese names
        // Expect exactly 2 series: first = "Bán ra" (sell), second = "Mua vào" (buy)
        preg_match_all('/data:\s*\[((?:\[\d{10,13}\s*,\s*[\d.]+\][\s,]*)+)\]/', $raw, $dataMatches);

        if (count($dataMatches[1]) < 2) {
            $this->error('Could not find 2 data series in file. Found: ' . count($dataMatches[1]));
            return self::FAILURE;
        }

        $sellSeries = [];
        $buySeries = [];

        foreach ($dataMatches[1] as $i => $dataChunk) {
            preg_match_all('/\[(\d{10,13})\s*,\s*(\d+(?:\.\d+)?)\]/', $dataChunk, $points, PREG_SET_ORDER);
            $parsed = array_map(
                fn ($p) => [(int) $p[1], (float) $p[2]],
                $points
            );

            if ($i === 0) {
                $sellSeries = $parsed;
            } elseif ($i === 1) {
                $buySeries = $parsed;
            }
        }

        if (empty($sellSeries) || empty($buySeries)) {
            $this->error('Parsed series is empty. Please verify input format.');
            return self::FAILURE;
        }

        $count = $service->importFromSeries($sellSeries, $buySeries);

        $this->info("Import completed: {$count} rows inserted/updated in sjc_chart_prices.");
        return self::SUCCESS;
    }
}
