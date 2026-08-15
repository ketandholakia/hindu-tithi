<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\FestivalDefinition;
use App\Models\FestivalRule;
use App\Models\FestivalAlias;

class FestivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(base_path('festival_master.json'));
        $festivals = json_decode($json, true);

        foreach ($festivals as $data) {
            $definition = FestivalDefinition::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name_en' => $data['name']['en'] ?? $data['name'],
                    'name_gu' => $data['name']['gu'] ?? null,
                    'name_hi' => $data['name']['hi'] ?? null,
                    'enabled' => true,
                ]
            );

            // Recreate rule
            $definition->rules()->delete();
            $definition->rules()->create([
                'rule_type' => $data['rule_type'],
                'month' => $data['month'] ?? null,
                'paksha' => $data['paksha'] ?? null,
                'tithi' => $data['tithi'] ?? null,
                'required_kala' => $data['kala'] ?? null,
                'priority' => $data['priority'] ?? 1,
            ]);

            // Create regional variants aliases
            $definition->aliases()->delete();
            if (isset($data['regional_variants']) && is_array($data['regional_variants'])) {
                foreach ($data['regional_variants'] as $variant) {
                    $definition->aliases()->create([
                        'region' => $variant,
                        'language' => 'en', // default mapping
                        'name' => $data['name']['en'] ?? $data['name']
                    ]);
                }
            }
        }
    }
}
