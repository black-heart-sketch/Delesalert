<?php

namespace App\Console\Commands;

use App\Models\Zone;
use App\Services\PredictionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('predictions:generate {--zone= : Generate a prediction for one zone ID}')]
#[Description('Generate outage-risk predictions from recorded operational data')]
class GenerateOutagePredictions extends Command
{
    public function handle(PredictionService $predictions): int
    {
        $query = Zone::query()->orderBy('id');

        if ($this->option('zone')) {
            $query->whereKey((int) $this->option('zone'));
        }

        $zones = $query->get();

        if ($zones->isEmpty()) {
            $this->warn('No matching zones were found.');

            return self::FAILURE;
        }

        foreach ($zones as $zone) {
            $prediction = $predictions->generate($zone);
            $this->line(sprintf(
                '%s: %s risk (%d%%, %s)',
                $zone->name,
                $prediction->risk_level,
                (int) round($prediction->probability * 100),
                $prediction->ai_generated ? 'OpenRouter' : 'statistical fallback',
            ));
        }

        $this->info(sprintf('%d prediction(s) generated.', $zones->count()));

        return self::SUCCESS;
    }
}
