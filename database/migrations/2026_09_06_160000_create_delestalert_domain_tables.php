<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region');
            $table->string('city');
            $table->string('district')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
            $table->unique(['name', 'city']);
        });
        Schema::create('saved_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('region')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
        Schema::create('outages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->index();
            $table->string('status')->index();
            $table->string('source')->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('affected_radius_km', 6, 2)->nullable();
            $table->timestamp('scheduled_start')->nullable();
            $table->timestamp('expected_end')->nullable();
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->timestamps();
            $table->index(['status', 'type']);
        });
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('zone_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('incident_type');
            $table->string('severity')->index();
            $table->string('status')->default('OPEN')->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
        Schema::create('outage_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description');
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('status')->default('PENDING')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reported_at');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->timestamp('predicted_start');
            $table->timestamp('predicted_end')->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->decimal('probability', 5, 4);
            $table->string('risk_level')->index();
            $table->decimal('confidence_score', 5, 4)->nullable();
            $table->string('model_version');
            $table->timestamp('generated_at');
            $table->timestamps();
        });
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('push_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('scheduled_outage_alerts')->default(true);
            $table->boolean('predicted_outage_alerts')->default(true);
            $table->boolean('restoration_alerts')->default(true);
            $table->string('minimum_risk_threshold')->default('MEDIUM');
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_role')->nullable();
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('predictions');
        Schema::dropIfExists('outage_reports');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('outages');
        Schema::dropIfExists('saved_locations');
        Schema::dropIfExists('zones');
    }
};
