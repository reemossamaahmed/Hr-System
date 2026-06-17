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
        Schema::create('payrolls', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->integer('month');

            $table->integer('year');

            $table->decimal('base_salary',10,2);

            $table->decimal('overtime_amount',10,2)
                ->default(0);

            $table->decimal('late_deduction',10,2)
                ->default(0);

            $table->decimal('absence_deduction',10,2)
                ->default(0);

            $table->decimal('net_salary',10,2);

            $table->enum('status',[
                'pending',
                'paid'
            ])->default('pending');

            $table->date('paid_at')->nullable();

            $table->timestamps();


            $table->unique([
                'user_id',
                'month',
                'year'
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
