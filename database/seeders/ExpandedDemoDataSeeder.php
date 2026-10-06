<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\CommunityPost;
use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\Prediction;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ExpandedDemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $providers = $this->seedProviders();
        $clients = $this->seedClients();
        $zones = $this->seedZones();
        $administrator = User::query()->where('email', 'admin@delestalert.cm')->firstOrFail();

        $this->seedClientProfiles($clients, $zones);
        $this->seedNetworkActivity($providers, $clients, $zones, $administrator);
        $this->seedPredictions($zones);
        $this->seedCommunity($providers, $clients, $zones);
        $this->seedBills($clients);
        $this->seedNotifications($clients, $zones);
    }

    /** @return Collection<int, User> */
    private function seedProviders(): Collection
    {
        return collect([
            ['name' => 'ENEO Littoral Dispatch', 'first_name' => 'ENEO', 'last_name' => 'Littoral', 'email' => 'provider.littoral@delestalert.cm', 'phone' => '237650100001'],
            ['name' => 'ENEO Régions Operations', 'first_name' => 'ENEO', 'last_name' => 'Régions', 'email' => 'provider.regions@delestalert.cm', 'phone' => '237650100002'],
        ])->map(fn (array $data): User => User::query()->updateOrCreate(
            ['email' => $data['email']],
            $data + ['role' => 'PROVIDER', 'status' => 'ACTIVE', 'password' => 'password', 'email_verified_at' => now()],
        ));
    }

    /** @return Collection<int, User> */
    private function seedClients(): Collection
    {
        $names = [
            ['Brenda', 'Eposi'], ['Samuel', 'Tchana'], ['Nadine', 'Abanda'], ['Armand', 'Fokou'],
            ['Christelle', 'Ngo'], ['Junior', 'Mbah'], ['Estelle', 'Ndam'], ['Patrick', 'Oumarou'],
            ['Carine', 'Essomba'], ['Alain', 'Mvondo'], ['Yvette', 'Kengne'], ['Boris', 'Ngassa'],
        ];

        return collect($names)->map(function (array $name, int $index): User {
            $emailName = mb_strtolower($name[0]);

            return User::query()->updateOrCreate(
                ['email' => $emailName.'@delestalert.cm'],
                [
                    'name' => implode(' ', $name),
                    'first_name' => $name[0],
                    'last_name' => $name[1],
                    'phone' => sprintf('23767010%04d', $index + 1),
                    'role' => 'CLIENT',
                    'status' => 'ACTIVE',
                    'password' => 'password',
                    'email_verified_at' => now(),
                ],
            );
        });
    }

    /** @return Collection<int, Zone> */
    private function seedZones(): Collection
    {
        $definitions = [
            ['Deido', 'Douala', 'Littoral', 'Douala I', 4.0636, 9.7064],
            ['Bonabéri', 'Douala', 'Littoral', 'Douala IV', 4.0794, 9.6658],
            ['Logpom', 'Douala', 'Littoral', 'Douala V', 4.1002, 9.7800],
            ['Mvan', 'Yaoundé', 'Centre', 'Yaoundé IV', 3.8178, 11.5318],
            ['Nlongkak', 'Yaoundé', 'Centre', 'Yaoundé I', 3.8846, 11.5293],
            ['Etoug-Ebe', 'Yaoundé', 'Centre', 'Yaoundé VI', 3.8462, 11.4728],
            ['Molyko', 'Buea', 'Sud-Ouest', 'Buea', 4.1593, 9.2918],
            ['Down Beach', 'Limbe', 'Sud-Ouest', 'Limbe I', 4.0114, 9.2056],
            ['Kumba Town', 'Kumba', 'Sud-Ouest', 'Kumba I', 4.6363, 9.4469],
            ['Commercial Avenue', 'Bamenda', 'Nord-Ouest', 'Bamenda II', 5.9631, 10.1591],
            ['Nkwen', 'Bamenda', 'Nord-Ouest', 'Bamenda III', 5.9868, 10.1815],
            ['Plateau', 'Garoua', 'Nord', 'Garoua I', 9.3014, 13.3977],
            ['Domayo', 'Maroua', 'Extrême-Nord', 'Maroua I', 10.5910, 14.3159],
            ['Baladji', 'Ngaoundéré', 'Adamaoua', 'Ngaoundéré II', 7.3277, 13.5847],
            ['Mokolo', 'Bertoua', 'Est', 'Bertoua I', 4.5773, 13.6846],
            ['Nko’ovos', 'Ebolowa', 'Sud', 'Ebolowa I', 2.9243, 11.1538],
            ['Dombe', 'Kribi', 'Sud', 'Kribi II', 2.9506, 9.9182],
            ['Njindare', 'Foumban', 'Ouest', 'Foumban', 5.7266, 10.8987],
        ];

        return collect($definitions)->map(fn (array $zone): Zone => Zone::query()->updateOrCreate(
            ['name' => $zone[0], 'city' => $zone[1]],
            ['region' => $zone[2], 'district' => $zone[3], 'latitude' => $zone[4], 'longitude' => $zone[5]],
        ));
    }

    /**
     * @param  Collection<int, User>  $clients
     * @param  Collection<int, Zone>  $zones
     */
    private function seedClientProfiles(Collection $clients, Collection $zones): void
    {
        $clients->values()->each(function (User $client, int $index) use ($zones): void {
            $homeZone = $zones[$index % $zones->count()];
            $workZone = $zones[($index + 5) % $zones->count()];

            $client->locations()->updateOrCreate(
                ['label' => 'Domicile'],
                ['zone_id' => $homeZone->id, 'address' => 'Secteur résidentiel '.$homeZone->name, 'city' => $homeZone->city, 'district' => $homeZone->district, 'region' => $homeZone->region, 'latitude' => $homeZone->latitude, 'longitude' => $homeZone->longitude, 'is_primary' => true],
            );
            $client->locations()->updateOrCreate(
                ['label' => 'Travail'],
                ['zone_id' => $workZone->id, 'address' => 'Centre commercial '.$workZone->name, 'city' => $workZone->city, 'district' => $workZone->district, 'region' => $workZone->region, 'latitude' => $workZone->latitude, 'longitude' => $workZone->longitude, 'is_primary' => false],
            );
            $client->notificationPreference()->updateOrCreate(
                ['user_id' => $client->id],
                ['push_enabled' => true, 'email_enabled' => $index % 3 !== 0, 'sms_enabled' => $index % 2 === 0, 'scheduled_outage_alerts' => true, 'predicted_outage_alerts' => true, 'restoration_alerts' => true, 'minimum_risk_threshold' => $index % 2 === 0 ? 'MEDIUM' : 'HIGH'],
            );
        });
    }

    /**
     * @param  Collection<int, User>  $providers
     * @param  Collection<int, User>  $clients
     * @param  Collection<int, Zone>  $zones
     */
    private function seedNetworkActivity(Collection $providers, Collection $clients, Collection $zones, User $administrator): void
    {
        $outageStatuses = ['ONGOING', 'PLANNED', 'RESOLVED'];
        $incidentStatuses = ['OPEN', 'IN_PROGRESS', 'RESOLVED'];
        $reportStatuses = ['PENDING', 'VALIDATED', 'REJECTED'];

        $zones->values()->each(function (Zone $zone, int $index) use ($providers, $clients, $administrator, $outageStatuses, $incidentStatuses, $reportStatuses): void {
            $provider = $providers[$index % $providers->count()];
            $outageStatus = $outageStatuses[$index % count($outageStatuses)];
            $outageStartedAt = now()->subHours(($index % 8) + 1);
            $scheduledStart = now()->addDays(($index % 6) + 1)->setTime(7 + ($index % 3), 0);

            Outage::query()->updateOrCreate(
                ['zone_id' => $zone->id, 'title' => 'Intervention réseau — '.$zone->name],
                [
                    'created_by' => $provider->id,
                    'description' => 'Intervention de démonstration documentée pour suivre la continuité du service dans la zone.',
                    'type' => $outageStatus === 'PLANNED' ? 'SCHEDULED' : 'UNPLANNED',
                    'status' => $outageStatus,
                    'source' => 'PROVIDER',
                    'latitude' => $zone->latitude,
                    'longitude' => $zone->longitude,
                    'affected_radius_km' => 1.0 + (($index % 5) * 0.5),
                    'scheduled_start' => $outageStatus === 'PLANNED' ? $scheduledStart : null,
                    'expected_end' => $outageStatus === 'PLANNED' ? $scheduledStart->copy()->addHours(3) : null,
                    'actual_start' => $outageStatus !== 'PLANNED' ? $outageStartedAt : null,
                    'actual_end' => $outageStatus === 'RESOLVED' ? $outageStartedAt->copy()->addMinutes(90 + $index) : null,
                    'estimated_duration_minutes' => 90 + (($index % 5) * 30),
                ],
            );

            Incident::query()->updateOrCreate(
                ['provider_id' => $provider->id, 'zone_id' => $zone->id, 'title' => 'Diagnostic technique — '.$zone->name],
                [
                    'description' => 'Contrôle des équipements, protections et niveaux de charge du réseau local.',
                    'incident_type' => ['TECHNICAL_FAILURE', 'OVERLOAD', 'EQUIPMENT_FAILURE'][$index % 3],
                    'severity' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'][$index % 4],
                    'status' => $incidentStatuses[$index % count($incidentStatuses)],
                    'latitude' => $zone->latitude,
                    'longitude' => $zone->longitude,
                    'occurred_at' => now()->subHours($index + 2),
                ],
            );

            foreach ([0, 1] as $reportIndex) {
                $client = $clients[($index + $reportIndex) % $clients->count()];
                $status = $reportStatuses[($index + $reportIndex) % count($reportStatuses)];
                $description = sprintf('Signalement communautaire %d pour %s : variation ou interruption constatée.', $reportIndex + 1, $zone->name);

                OutageReport::query()->updateOrCreate(
                    ['user_id' => $client->id, 'zone_id' => $zone->id, 'description' => $description],
                    [
                        'validated_by' => $status === 'PENDING' ? null : $administrator->id,
                        'address' => 'Secteur '.$zone->name.', '.$zone->city,
                        'latitude' => $zone->latitude,
                        'longitude' => $zone->longitude,
                        'status' => $status,
                        'rejection_reason' => $status === 'REJECTED' ? 'Aucune anomalie confirmée par les autres sources.' : null,
                        'reported_at' => now()->subHours(($index * 2) + $reportIndex + 1),
                        'validated_at' => $status === 'PENDING' ? null : now()->subHours($index + 1),
                    ],
                );
            }
        });
    }

    /** @param Collection<int, Zone> $zones */
    private function seedPredictions(Collection $zones): void
    {
        $probabilities = [0.18, 0.34, 0.52, 0.67, 0.83, 0.91];

        $zones->values()->each(function (Zone $zone, int $index) use ($probabilities): void {
            $probability = $probabilities[$index % count($probabilities)];
            $riskLevel = match (true) {
                $probability >= 0.80 => 'CRITICAL',
                $probability >= 0.60 => 'HIGH',
                $probability >= 0.40 => 'MEDIUM',
                default => 'LOW',
            };
            $predictedStart = now()->addHours(($index % 24) + 2);
            $duration = 90 + (($index % 4) * 30);

            Prediction::query()->updateOrCreate(
                ['zone_id' => $zone->id, 'model_version' => 'expanded-demo-v1'],
                [
                    'predicted_start' => $predictedStart,
                    'predicted_end' => $predictedStart->copy()->addMinutes($duration),
                    'estimated_duration_minutes' => $duration,
                    'probability' => $probability,
                    'risk_level' => $riskLevel,
                    'confidence_score' => 0.68 + (($index % 5) * 0.05),
                    'input_metrics' => ['outages_90d' => ($index % 7) + 1, 'reports_14d' => ($index % 5) + 1, 'expanded_demo_dataset' => true],
                    'factors' => ['Historique récent de la zone', 'Charge estimée du réseau', 'Signalements communautaires'],
                    'ai_generated' => $index % 3 === 0,
                    'generated_at' => now(),
                ],
            );
        });
    }

    /**
     * @param  Collection<int, User>  $providers
     * @param  Collection<int, User>  $clients
     * @param  Collection<int, Zone>  $zones
     */
    private function seedCommunity(Collection $providers, Collection $clients, Collection $zones): void
    {
        $categories = ['UPDATE', 'QUESTION', 'TIP'];

        $zones->values()->each(function (Zone $zone, int $index) use ($providers, $clients, $categories): void {
            $author = $index % 4 === 0 ? $providers[$index % $providers->count()] : $clients[$index % $clients->count()];
            $post = CommunityPost::query()->updateOrCreate(
                ['user_id' => $author->id, 'title' => 'Point réseau communautaire — '.$zone->name],
                [
                    'zone_id' => $zone->id,
                    'category' => $categories[$index % count($categories)],
                    'body' => 'Informations locales partagées pour aider les habitants de '.$zone->city.' à suivre la situation électrique.',
                    'status' => 'PUBLISHED',
                ],
            );

            $post->comments()->updateOrCreate(
                ['user_id' => $providers[$index % $providers->count()]->id],
                ['body' => 'Merci pour cette mise à jour. La situation de la zone est suivie par les équipes.'],
            );
            $post->reactions()->updateOrCreate(['user_id' => $clients[$index % $clients->count()]->id]);
            $post->reactions()->updateOrCreate(['user_id' => $clients[($index + 3) % $clients->count()]->id]);
        });
    }

    /** @param Collection<int, User> $clients */
    private function seedBills(Collection $clients): void
    {
        $clients->values()->each(function (User $client, int $clientIndex): void {
            foreach (range(0, 3) as $monthIndex) {
                $isPaid = $monthIndex > 0;
                $reference = sprintf('DLST-X%02d-%02d', $clientIndex + 1, $monthIndex + 1);
                $dueDate = now()->startOfMonth()->subMonths($monthIndex)->addDays(14);
                $paidAt = $isPaid ? $dueDate->copy()->subDays(($clientIndex % 4) + 1) : null;

                $bill = Bill::query()->updateOrCreate(
                    ['user_id' => $client->id, 'account_reference' => $reference],
                    [
                        'provider_name' => 'ENEO Cameroon',
                        'description' => 'Consommation électrique — '.$dueDate->translatedFormat('F Y'),
                        'amount_due' => 6500 + ($clientIndex * 850) + ($monthIndex * 600),
                        'currency' => 'XAF',
                        'due_date' => $dueDate->toDateString(),
                        'status' => $isPaid ? 'PAID' : 'UNPAID',
                        'paid_at' => $paidAt,
                    ],
                );

                if ($isPaid) {
                    BillPayment::query()->updateOrCreate(
                        ['transaction_reference' => 'DEMO-EXPANDED-'.$reference],
                        [
                            'bill_id' => $bill->id,
                            'user_id' => $client->id,
                            'amount' => $bill->amount_due,
                            'currency' => 'XAF',
                            'provider' => 'DEMO',
                            'status' => 'COMPLETED',
                            'customer_phone' => $client->phone,
                            'provider_transaction_id' => 'SEED-'.$reference,
                            'provider_payload' => ['source' => 'expanded-demo-seeder'],
                            'paid_at' => $paidAt,
                        ],
                    );
                }
            }
        });
    }

    /**
     * @param  Collection<int, User>  $clients
     * @param  Collection<int, Zone>  $zones
     */
    private function seedNotifications(Collection $clients, Collection $zones): void
    {
        $clients->values()->each(function (User $client, int $clientIndex) use ($zones): void {
            foreach (range(0, 2) as $notificationIndex) {
                $zone = $zones[($clientIndex + $notificationIndex) % $zones->count()];
                $notificationKey = $client->email.'|'.$zone->id.'|'.$notificationIndex;

                $client->notifications()->updateOrCreate(
                    ['id' => $this->deterministicUuid($notificationKey)],
                    [
                        'type' => 'demo.outage-status',
                        'data' => [
                            'title' => 'Mise à jour réseau — '.$zone->name,
                            'message' => 'Une nouvelle information électrique est disponible pour votre zone suivie.',
                            'zone_id' => $zone->id,
                            'status' => ['ONGOING', 'PLANNED', 'RESOLVED'][$notificationIndex],
                        ],
                        'read_at' => $notificationIndex === 2 ? now()->subHour() : null,
                    ],
                );
            }
        });
    }

    private function deterministicUuid(string $value): string
    {
        $hash = md5($value);

        return sprintf('%s-%s-%s-%s-%s', substr($hash, 0, 8), substr($hash, 8, 4), substr($hash, 12, 4), substr($hash, 16, 4), substr($hash, 20, 12));
    }
}
