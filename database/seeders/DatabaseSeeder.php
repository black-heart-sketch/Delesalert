<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\CommunityPost;
use App\Models\Incident;
use App\Models\Outage;
use App\Models\Prediction;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@delestalert.cm'], ['name' => 'Amina Njoya', 'first_name' => 'Amina', 'last_name' => 'Njoya', 'role' => 'ADMIN', 'status' => 'ACTIVE', 'password' => 'password']);
        $provider = User::updateOrCreate(['email' => 'provider@delestalert.cm'], ['name' => 'ENEO Operations', 'first_name' => 'ENEO', 'last_name' => 'Operations', 'role' => 'PROVIDER', 'status' => 'ACTIVE', 'password' => 'password']);
        $client = User::updateOrCreate(['email' => 'client@delestalert.cm'], ['name' => 'Jean Mbarga', 'first_name' => 'Jean', 'last_name' => 'Mbarga', 'role' => 'CLIENT', 'status' => 'ACTIVE', 'password' => 'password']);
        $bonamoussadi = Zone::updateOrCreate(['name' => 'Bonamoussadi', 'city' => 'Douala'], ['region' => 'Littoral', 'district' => 'Douala V', 'latitude' => 4.0932, 'longitude' => 9.7538]);
        $bastos = Zone::updateOrCreate(['name' => 'Bastos', 'city' => 'Yaoundé'], ['region' => 'Centre', 'district' => 'Yaoundé I', 'latitude' => 3.8930, 'longitude' => 11.5184]);
        $client->locations()->updateOrCreate(['label' => 'Home'], ['zone_id' => $bonamoussadi->id, 'address' => 'Bonamoussadi, Douala', 'city' => 'Douala', 'region' => 'Littoral', 'latitude' => 4.0932, 'longitude' => 9.7538, 'is_primary' => true]);
        Outage::updateOrCreate(['zone_id' => $bonamoussadi->id, 'title' => 'Transformer maintenance'], ['created_by' => $provider->id, 'description' => 'Emergency transformer work is underway.', 'type' => 'UNPLANNED', 'status' => 'ONGOING', 'source' => 'PROVIDER', 'latitude' => 4.0932, 'longitude' => 9.7538, 'affected_radius_km' => 2.5, 'actual_start' => now()->subHour(), 'estimated_duration_minutes' => 180]);
        Outage::updateOrCreate(['zone_id' => $bastos->id, 'title' => 'Planned line maintenance'], ['created_by' => $provider->id, 'description' => 'Routine network maintenance.', 'type' => 'SCHEDULED', 'status' => 'PLANNED', 'source' => 'PROVIDER', 'latitude' => 3.8930, 'longitude' => 11.5184, 'affected_radius_km' => 1.5, 'scheduled_start' => now()->addDay(), 'expected_end' => now()->addDay()->addHours(3), 'estimated_duration_minutes' => 180]);
        Incident::updateOrCreate(['provider_id' => $provider->id, 'zone_id' => $bonamoussadi->id, 'title' => 'Transformer fault'], ['description' => 'Technical team dispatched.', 'incident_type' => 'TECHNICAL_FAILURE', 'severity' => 'HIGH', 'status' => 'IN_PROGRESS', 'occurred_at' => now()->subHour()]);
        Prediction::updateOrCreate(['zone_id' => $bastos->id, 'model_version' => 'statistical-baseline-v1'], ['predicted_start' => now()->addDays(2), 'estimated_duration_minutes' => 90, 'probability' => .63, 'risk_level' => 'HIGH', 'confidence_score' => .72, 'generated_at' => now()]);
        $communityPost = CommunityPost::updateOrCreate(['user_id' => $client->id, 'title' => 'Power is back in Bonamoussadi'], ['zone_id' => $bonamoussadi->id, 'category' => 'UPDATE', 'body' => 'Electricity returned around 18:30 near the main road. Please share whether your street is back too.', 'status' => 'PUBLISHED']);
        $provider->communityPosts()->updateOrCreate(['title' => 'Transformer team dispatched'], ['zone_id' => $bonamoussadi->id, 'category' => 'UPDATE', 'body' => 'A technical team is working on the reported transformer fault. Follow the outage board for official restoration updates.', 'status' => 'PUBLISHED']);
        $communityPost->comments()->updateOrCreate(['user_id' => $provider->id], ['body' => 'Thank you for the local update. Our official notice will be updated as work progresses.']);
        $unpaidBill = Bill::updateOrCreate(['user_id' => $client->id, 'account_reference' => 'DLST-000128'], ['provider_name' => 'ENEO Cameroon', 'description' => 'September 2026 electricity usage', 'amount_due' => 12500, 'currency' => 'XAF', 'due_date' => now()->addDays(10)->toDateString(), 'status' => 'UNPAID', 'paid_at' => null]);
        $paidBill = Bill::updateOrCreate(['user_id' => $client->id, 'account_reference' => 'DLST-000127'], ['provider_name' => 'ENEO Cameroon', 'description' => 'August 2026 electricity usage', 'amount_due' => 9800, 'currency' => 'XAF', 'due_date' => now()->subMonth()->toDateString(), 'status' => 'PAID', 'paid_at' => now()->subMonth()]);
        BillPayment::updateOrCreate(['bill_id' => $paidBill->id], ['user_id' => $client->id, 'amount' => $paidBill->amount_due, 'currency' => $paidBill->currency, 'provider' => 'DEMO', 'status' => 'COMPLETED', 'transaction_reference' => 'DEMO-SEED-000127', 'paid_at' => $paidBill->paid_at]);
    }
}
