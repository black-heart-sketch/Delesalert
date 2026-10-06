<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\CommunityPost;
use App\Models\Incident;
use App\Models\Outage;
use App\Models\OutageReport;
use App\Models\Prediction;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->seedUsers();
        $zones = $this->seedZones();

        $this->seedClientProfiles($users, $zones);
        $this->seedNetworkHistory($users, $zones);
        $this->seedPredictions($zones);
        $this->seedCommunity($users, $zones);
        $this->seedBills($users);

        SystemSetting::query()->updateOrCreate(['key' => 'map_provider'], ['value' => 'leaflet']);
    }

    /** @return array<string, User> */
    private function seedUsers(): array
    {
        $definitions = [
            'admin' => ['email' => 'admin@delestalert.cm', 'name' => 'Amina Njoya', 'first_name' => 'Amina', 'last_name' => 'Njoya', 'phone' => '237650000001', 'role' => 'ADMIN'],
            'provider' => ['email' => 'provider@delestalert.cm', 'name' => 'ENEO Operations', 'first_name' => 'ENEO', 'last_name' => 'Operations', 'phone' => '237650000002', 'role' => 'PROVIDER'],
            'client' => ['email' => 'client@delestalert.cm', 'name' => 'Jean Mbarga', 'first_name' => 'Jean', 'last_name' => 'Mbarga', 'phone' => '237650186981', 'role' => 'CLIENT'],
            'client_two' => ['email' => 'mireille@delestalert.cm', 'name' => 'Mireille Fonkou', 'first_name' => 'Mireille', 'last_name' => 'Fonkou', 'phone' => '237670000004', 'role' => 'CLIENT'],
        ];

        $users = [];

        foreach ($definitions as $key => $definition) {
            $users[$key] = User::query()->updateOrCreate(
                ['email' => $definition['email']],
                $definition + ['status' => 'ACTIVE', 'password' => 'password', 'email_verified_at' => now()],
            );
        }

        return $users;
    }

    /** @return array<string, Zone> */
    private function seedZones(): array
    {
        $definitions = [
            'bonamoussadi' => ['name' => 'Bonamoussadi', 'city' => 'Douala', 'region' => 'Littoral', 'district' => 'Douala V', 'latitude' => 4.0932, 'longitude' => 9.7538],
            'makepe' => ['name' => 'Makepe', 'city' => 'Douala', 'region' => 'Littoral', 'district' => 'Douala V', 'latitude' => 4.0896, 'longitude' => 9.7394],
            'akwa' => ['name' => 'Akwa', 'city' => 'Douala', 'region' => 'Littoral', 'district' => 'Douala I', 'latitude' => 4.0511, 'longitude' => 9.7679],
            'bastos' => ['name' => 'Bastos', 'city' => 'Yaoundé', 'region' => 'Centre', 'district' => 'Yaoundé I', 'latitude' => 3.8930, 'longitude' => 11.5184],
            'biyem_assi' => ['name' => 'Biyem-Assi', 'city' => 'Yaoundé', 'region' => 'Centre', 'district' => 'Yaoundé VI', 'latitude' => 3.8447, 'longitude' => 11.4842],
            'bafoussam' => ['name' => 'Bafoussam Centre', 'city' => 'Bafoussam', 'region' => 'Ouest', 'district' => 'Bafoussam I', 'latitude' => 5.4778, 'longitude' => 10.4176],
        ];

        $zones = [];

        foreach ($definitions as $key => $definition) {
            $zones[$key] = Zone::query()->updateOrCreate(
                ['name' => $definition['name'], 'city' => $definition['city']],
                $definition,
            );
        }

        return $zones;
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Zone>  $zones
     */
    private function seedClientProfiles(array $users, array $zones): void
    {
        $users['client']->locations()->updateOrCreate(
            ['label' => 'Maison'],
            ['zone_id' => $zones['bonamoussadi']->id, 'address' => 'Rue des Manguiers, Bonamoussadi', 'city' => 'Douala', 'district' => 'Douala V', 'region' => 'Littoral', 'latitude' => 4.0932, 'longitude' => 9.7538, 'is_primary' => true],
        );
        $users['client']->locations()->updateOrCreate(
            ['label' => 'Bureau'],
            ['zone_id' => $zones['akwa']->id, 'address' => 'Boulevard de la Liberté, Akwa', 'city' => 'Douala', 'district' => 'Douala I', 'region' => 'Littoral', 'latitude' => 4.0511, 'longitude' => 9.7679, 'is_primary' => false],
        );
        $users['client_two']->locations()->updateOrCreate(
            ['label' => 'Domicile'],
            ['zone_id' => $zones['bastos']->id, 'address' => 'Quartier Bastos, Yaoundé', 'city' => 'Yaoundé', 'district' => 'Yaoundé I', 'region' => 'Centre', 'latitude' => 3.8930, 'longitude' => 11.5184, 'is_primary' => true],
        );

        $users['client']->notificationPreference()->updateOrCreate(
            ['user_id' => $users['client']->id],
            ['push_enabled' => true, 'email_enabled' => true, 'sms_enabled' => true, 'scheduled_outage_alerts' => true, 'predicted_outage_alerts' => true, 'restoration_alerts' => true, 'minimum_risk_threshold' => 'MEDIUM'],
        );
        $users['client_two']->notificationPreference()->updateOrCreate(
            ['user_id' => $users['client_two']->id],
            ['push_enabled' => true, 'email_enabled' => true, 'sms_enabled' => false, 'scheduled_outage_alerts' => true, 'predicted_outage_alerts' => true, 'restoration_alerts' => true, 'minimum_risk_threshold' => 'HIGH'],
        );
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Zone>  $zones
     */
    private function seedNetworkHistory(array $users, array $zones): void
    {
        $outages = [
            ['zone' => 'bonamoussadi', 'title' => 'Défaillance du transformateur principal', 'description' => 'Une équipe technique intervient sur le transformateur alimentant le secteur.', 'type' => 'UNPLANNED', 'status' => 'ONGOING', 'source' => 'PROVIDER', 'actual_start' => now()->subHours(2), 'estimated_duration_minutes' => 180, 'affected_radius_km' => 2.5],
            ['zone' => 'biyem_assi', 'title' => 'Coupure sur le départ moyenne tension', 'description' => 'Diagnostic en cours après le déclenchement d’une protection réseau.', 'type' => 'UNPLANNED', 'status' => 'ONGOING', 'source' => 'PROVIDER', 'actual_start' => now()->subMinutes(45), 'estimated_duration_minutes' => 120, 'affected_radius_km' => 1.8],
            ['zone' => 'bastos', 'title' => 'Maintenance programmée de la ligne Bastos', 'description' => 'Renforcement préventif du réseau et remplacement d’équipements.', 'type' => 'SCHEDULED', 'status' => 'PLANNED', 'source' => 'PROVIDER', 'scheduled_start' => now()->addDay()->setTime(8, 0), 'expected_end' => now()->addDay()->setTime(11, 0), 'estimated_duration_minutes' => 180, 'affected_radius_km' => 1.5],
            ['zone' => 'akwa', 'title' => 'Travaux planifiés au poste Akwa', 'description' => 'Maintenance des cellules électriques du poste de distribution.', 'type' => 'SCHEDULED', 'status' => 'PLANNED', 'source' => 'PROVIDER', 'scheduled_start' => now()->addDays(3)->setTime(6, 30), 'expected_end' => now()->addDays(3)->setTime(10, 30), 'estimated_duration_minutes' => 240, 'affected_radius_km' => 1.2],
            ['zone' => 'makepe', 'title' => 'Incident réseau résolu à Makepe', 'description' => 'Le câble endommagé a été remplacé et le service est rétabli.', 'type' => 'UNPLANNED', 'status' => 'RESOLVED', 'source' => 'PROVIDER', 'actual_start' => now()->subDays(6)->subHours(3), 'actual_end' => now()->subDays(6), 'estimated_duration_minutes' => 175, 'affected_radius_km' => 2.0],
            ['zone' => 'bafoussam', 'title' => 'Coupure locale au centre-ville', 'description' => 'Le défaut isolé a été corrigé par l’équipe régionale.', 'type' => 'UNPLANNED', 'status' => 'RESOLVED', 'source' => 'PROVIDER', 'actual_start' => now()->subDays(18)->subHours(2), 'actual_end' => now()->subDays(18), 'estimated_duration_minutes' => 110, 'affected_radius_km' => 1.0],
            ['zone' => 'bonamoussadi', 'title' => 'Maintenance préventive de septembre', 'description' => 'Intervention préventive achevée dans les délais annoncés.', 'type' => 'SCHEDULED', 'status' => 'RESOLVED', 'source' => 'PROVIDER', 'scheduled_start' => now()->subDays(32)->setTime(7, 0), 'actual_start' => now()->subDays(32)->setTime(7, 5), 'actual_end' => now()->subDays(32)->setTime(9, 20), 'estimated_duration_minutes' => 135, 'affected_radius_km' => 1.5],
        ];

        foreach ($outages as $definition) {
            $zone = $zones[$definition['zone']];
            unset($definition['zone']);
            Outage::query()->updateOrCreate(
                ['zone_id' => $zone->id, 'title' => $definition['title']],
                $definition + ['created_by' => $users['provider']->id, 'latitude' => $zone->latitude, 'longitude' => $zone->longitude],
            );
        }

        $incidents = [
            ['zone' => 'bonamoussadi', 'title' => 'Surchauffe du transformateur', 'description' => 'Température anormale détectée, équipe technique mobilisée.', 'incident_type' => 'TECHNICAL_FAILURE', 'severity' => 'HIGH', 'status' => 'IN_PROGRESS', 'occurred_at' => now()->subHours(2)],
            ['zone' => 'biyem_assi', 'title' => 'Déclenchement de protection', 'description' => 'Une protection moyenne tension s’est déclenchée après une surcharge.', 'incident_type' => 'OVERLOAD', 'severity' => 'CRITICAL', 'status' => 'OPEN', 'occurred_at' => now()->subHour()],
            ['zone' => 'makepe', 'title' => 'Câble souterrain endommagé', 'description' => 'Le câble a été remplacé et testé avec succès.', 'incident_type' => 'CABLE_FAILURE', 'severity' => 'HIGH', 'status' => 'RESOLVED', 'occurred_at' => now()->subDays(6)],
            ['zone' => 'akwa', 'title' => 'Vérification préventive du poste', 'description' => 'Un contrôle a identifié une cellule à remplacer pendant la maintenance.', 'incident_type' => 'PREVENTIVE_CHECK', 'severity' => 'MEDIUM', 'status' => 'IN_PROGRESS', 'occurred_at' => now()->subDays(2)],
            ['zone' => 'bafoussam', 'title' => 'Défaut d’isolateur', 'description' => 'L’isolateur défectueux a été remplacé.', 'incident_type' => 'EQUIPMENT_FAILURE', 'severity' => 'MEDIUM', 'status' => 'RESOLVED', 'occurred_at' => now()->subDays(18)],
        ];

        foreach ($incidents as $definition) {
            $zone = $zones[$definition['zone']];
            unset($definition['zone']);
            Incident::query()->updateOrCreate(
                ['provider_id' => $users['provider']->id, 'zone_id' => $zone->id, 'title' => $definition['title']],
                $definition + ['latitude' => $zone->latitude, 'longitude' => $zone->longitude],
            );
        }

        $reports = [
            ['user' => 'client', 'zone' => 'bonamoussadi', 'description' => 'Plus de courant depuis environ deux heures près du marché.', 'address' => 'Marché de Bonamoussadi', 'status' => 'VALIDATED', 'reported_at' => now()->subHours(2), 'validated_at' => now()->subMinutes(90)],
            ['user' => 'client', 'zone' => 'makepe', 'description' => 'Baisse de tension répétée dans notre rue.', 'address' => 'Makepe Missoke', 'status' => 'PENDING', 'reported_at' => now()->subHours(5)],
            ['user' => 'client_two', 'zone' => 'biyem_assi', 'description' => 'Coupure complète dans plusieurs blocs du quartier.', 'address' => 'Carrefour Biyem-Assi', 'status' => 'VALIDATED', 'reported_at' => now()->subMinutes(50), 'validated_at' => now()->subMinutes(30)],
            ['user' => 'client_two', 'zone' => 'bastos', 'description' => 'Clignotement des lampes depuis ce matin.', 'address' => 'Route de l’ambassade', 'status' => 'PENDING', 'reported_at' => now()->subHours(3)],
            ['user' => 'client', 'zone' => 'akwa', 'description' => 'Le courant est revenu après une brève interruption.', 'address' => 'Boulevard de la Liberté', 'status' => 'VALIDATED', 'reported_at' => now()->subDays(3), 'validated_at' => now()->subDays(3)->addHour()],
            ['user' => 'client_two', 'zone' => 'bafoussam', 'description' => 'Signalement sans interruption constatée sur place.', 'address' => 'Centre-ville de Bafoussam', 'status' => 'REJECTED', 'reported_at' => now()->subDays(4), 'validated_at' => now()->subDays(4)->addHours(2), 'rejection_reason' => 'Aucune coupure confirmée dans ce secteur.'],
        ];

        foreach ($reports as $definition) {
            $user = $users[$definition['user']];
            $zone = $zones[$definition['zone']];
            unset($definition['user'], $definition['zone']);
            $reviewed = in_array($definition['status'], ['VALIDATED', 'REJECTED'], true);
            OutageReport::query()->updateOrCreate(
                ['user_id' => $user->id, 'zone_id' => $zone->id, 'description' => $definition['description']],
                $definition + ['latitude' => $zone->latitude, 'longitude' => $zone->longitude, 'validated_by' => $reviewed ? $users['admin']->id : null],
            );
        }
    }

    /** @param array<string, Zone> $zones */
    private function seedPredictions(array $zones): void
    {
        $predictions = [
            ['zone' => 'bonamoussadi', 'probability' => 0.78, 'risk_level' => 'HIGH', 'confidence_score' => 0.82, 'start_in_hours' => 3, 'duration' => 180, 'factors' => ['Coupure active dans la zone', 'Incident technique grave récent', 'Signalements communautaires validés']],
            ['zone' => 'biyem_assi', 'probability' => 0.86, 'risk_level' => 'CRITICAL', 'confidence_score' => 0.79, 'start_in_hours' => 2, 'duration' => 120, 'factors' => ['Incident critique en cours', 'Coupure active confirmée']],
            ['zone' => 'makepe', 'probability' => 0.46, 'risk_level' => 'MEDIUM', 'confidence_score' => 0.67, 'start_in_hours' => 18, 'duration' => 150, 'factors' => ['Coupure récente', 'Nouveau signalement en attente']],
            ['zone' => 'bastos', 'probability' => 0.32, 'risk_level' => 'LOW', 'confidence_score' => 0.74, 'start_in_hours' => 24, 'duration' => 180, 'factors' => ['Maintenance déjà planifiée', 'Peu d’incidents récents']],
            ['zone' => 'akwa', 'probability' => 0.55, 'risk_level' => 'MEDIUM', 'confidence_score' => 0.70, 'start_in_hours' => 12, 'duration' => 210, 'factors' => ['Intervention préventive en préparation', 'Activité récente du réseau']],
            ['zone' => 'bafoussam', 'probability' => 0.18, 'risk_level' => 'LOW', 'confidence_score' => 0.61, 'start_in_hours' => 48, 'duration' => 90, 'factors' => ['Incident précédent résolu', 'Faible volume de signalements récents']],
        ];

        foreach ($predictions as $definition) {
            $zone = $zones[$definition['zone']];
            $predictedStart = now()->addHours($definition['start_in_hours']);
            Prediction::query()->updateOrCreate(
                ['zone_id' => $zone->id, 'model_version' => 'demo-statistical-v2'],
                [
                    'predicted_start' => $predictedStart,
                    'predicted_end' => $predictedStart->copy()->addMinutes($definition['duration']),
                    'estimated_duration_minutes' => $definition['duration'],
                    'probability' => $definition['probability'],
                    'risk_level' => $definition['risk_level'],
                    'confidence_score' => $definition['confidence_score'],
                    'input_metrics' => ['demo_dataset' => true],
                    'factors' => $definition['factors'],
                    'ai_generated' => false,
                    'generated_at' => now(),
                ],
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Zone>  $zones
     */
    private function seedCommunity(array $users, array $zones): void
    {
        $posts = [
            ['key' => 'restoration', 'user' => 'client', 'zone' => 'makepe', 'category' => 'UPDATE', 'title' => 'Le courant est revenu à Makepe', 'body' => 'Le service est rétabli près de Missoke. Merci aux voisins de confirmer pour les rues voisines.'],
            ['key' => 'team', 'user' => 'provider', 'zone' => 'bonamoussadi', 'category' => 'UPDATE', 'title' => 'Équipe technique déployée à Bonamoussadi', 'body' => 'Nos techniciens travaillent sur le transformateur principal. Une nouvelle estimation sera publiée après le diagnostic.'],
            ['key' => 'question', 'user' => 'client_two', 'zone' => 'bastos', 'category' => 'QUESTION', 'title' => 'La maintenance de Bastos concerne-t-elle tout le quartier ?', 'body' => 'Je souhaite savoir si la zone près du lycée sera aussi concernée demain matin.'],
            ['key' => 'tip', 'user' => 'admin', 'zone' => 'biyem_assi', 'category' => 'TIP', 'title' => 'Conseils pendant une coupure prolongée', 'body' => 'Débranchez les appareils sensibles et gardez une lampe chargée. N’intervenez jamais sur une installation publique.'],
        ];

        $createdPosts = [];

        foreach ($posts as $definition) {
            $key = $definition['key'];
            $user = $users[$definition['user']];
            $zone = $zones[$definition['zone']];
            unset($definition['key'], $definition['user'], $definition['zone']);
            $createdPosts[$key] = CommunityPost::query()->updateOrCreate(
                ['user_id' => $user->id, 'title' => $definition['title']],
                $definition + ['zone_id' => $zone->id, 'status' => 'PUBLISHED'],
            );
        }

        $createdPosts['restoration']->comments()->updateOrCreate(
            ['user_id' => $users['provider']->id],
            ['body' => 'Merci pour la confirmation. Nos contrôles de stabilité se poursuivent.'],
        );
        $createdPosts['question']->comments()->updateOrCreate(
            ['user_id' => $users['provider']->id],
            ['body' => 'La zone du lycée est incluse entre 08 h et 11 h.'],
        );
        $createdPosts['team']->reactions()->updateOrCreate(['user_id' => $users['client']->id]);
        $createdPosts['team']->reactions()->updateOrCreate(['user_id' => $users['client_two']->id]);
        $createdPosts['tip']->reactions()->updateOrCreate(['user_id' => $users['client']->id]);
    }

    /** @param array<string, User> $users */
    private function seedBills(array $users): void
    {
        $bills = [
            ['user' => 'client', 'reference' => 'DLST-000128', 'description' => 'Consommation de septembre 2026', 'amount' => 12500, 'due_date' => now()->addDays(10), 'status' => 'UNPAID'],
            ['user' => 'client', 'reference' => 'DLST-000127', 'description' => 'Consommation d’août 2026', 'amount' => 9800, 'due_date' => now()->subMonth(), 'status' => 'PAID', 'paid_at' => now()->subMonth()->subDays(2)],
            ['user' => 'client', 'reference' => 'DLST-000126', 'description' => 'Consommation de juillet 2026', 'amount' => 11250, 'due_date' => now()->subMonths(2), 'status' => 'PAID', 'paid_at' => now()->subMonths(2)->subDays(3)],
            ['user' => 'client_two', 'reference' => 'DLST-000204', 'description' => 'Consommation de septembre 2026', 'amount' => 18750, 'due_date' => now()->addDays(8), 'status' => 'UNPAID'],
        ];

        foreach ($bills as $definition) {
            $user = $users[$definition['user']];
            unset($definition['user']);
            $bill = Bill::query()->updateOrCreate(
                ['user_id' => $user->id, 'account_reference' => $definition['reference']],
                ['provider_name' => 'ENEO Cameroon', 'description' => $definition['description'], 'amount_due' => $definition['amount'], 'currency' => 'XAF', 'due_date' => $definition['due_date']->toDateString(), 'status' => $definition['status'], 'paid_at' => $definition['paid_at'] ?? null],
            );

            if ($bill->status === 'PAID') {
                BillPayment::query()->updateOrCreate(
                    ['transaction_reference' => 'DEMO-SEED-'.$definition['reference']],
                    ['bill_id' => $bill->id, 'user_id' => $user->id, 'amount' => $bill->amount_due, 'currency' => 'XAF', 'provider' => 'DEMO', 'status' => 'COMPLETED', 'paid_at' => $bill->paid_at],
                );
            }
        }
    }
}
