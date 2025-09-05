<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('formbuilder.table_names');

        throw_if(empty($tableNames), new Exception('Error: config/formbuilder.php not loaded. Run [php artisan config:clear] and try again.'));

        Schema::create($tableNames['forms'], function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->string('title');
            $table->string('locale', 10)->default('fr');
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create($tableNames['form_items'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('form_id')->constrained($tableNames['forms'])->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('type');
            $table->unsignedInteger('position')->default(0);
            $table->json('options')->nullable();
            $table->json('validation')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['form_id', 'key']);
        });

        Schema::create($tableNames['branching_rules'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('form_id')->constrained($tableNames['forms'])->cascadeOnDelete();
            $table->json('condition');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create($tableNames['answer_sets'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('form_id')->constrained($tableNames['forms']);
            $table->morphs('answerable');
            $table->timestamp('submitted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create($tableNames['answers'], function (Blueprint $table) use ($tableNames) {
            $table->id();
            $table->foreignId('answer_set_id')->constrained($tableNames['answer_sets'])->cascadeOnDelete();
            $table->string('field_key');
            $table->json('value')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['answer_set_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('formbuilder.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/formbuilder.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::dropIfExists($tableNames['answers']);
        Schema::dropIfExists($tableNames['answer_sets']);
        Schema::dropIfExists($tableNames['branching_rules']);
        Schema::dropIfExists($tableNames['form_items']);
        Schema::dropIfExists($tableNames['forms']);
    }
};
        