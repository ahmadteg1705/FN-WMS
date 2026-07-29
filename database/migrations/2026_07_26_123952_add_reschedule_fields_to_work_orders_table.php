<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            $table->string('work_order_no')
                ->unique()
                ->after('id');

            $table->foreignId('parent_id')
                ->nullable()
                ->after('registration_id')
                ->constrained('work_orders')
                ->nullOnDelete();

            $table->unsignedTinyInteger('reschedule_count')
                ->default(0)
                ->after('status');

            $table->text('cancel_reason')
                ->nullable()
                ->after('catatan');

        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {

            $table->dropForeign(['parent_id']);

            $table->dropColumn([
                'work_order_no',
                'parent_id',
                'reschedule_count',
                'cancel_reason',
            ]);

        });
    }
};