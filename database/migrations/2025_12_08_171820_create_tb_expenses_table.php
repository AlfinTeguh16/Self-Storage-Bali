<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ===== 1. tb_monthly_fixed_expenses =====
        Schema::create('tb_monthly_fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_name', 100);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('unit', 20)->default('IDR');
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();

            $table->index('is_recurring');
        });

        // ===== 2. tb_positions =====
        Schema::create('tb_positions', function (Blueprint $table) {
            $table->id();
            $table->string('position_name', 100)->unique();
            $table->text('description')->nullable();
            $table->decimal('base_salary', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===== 3. tb_employees =====
        Schema::create('tb_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 20)->unique();
            $table->string('full_name', 100);
            $table->foreignId('position_id')->constrained('tb_positions')->onDelete('restrict');
            $table->date('join_date');
            $table->boolean('is_active')->default(true);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });

        // ===== 4. tb_salary_components =====
        Schema::create('tb_salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('component_name', 100)->unique();
            $table->enum('component_type', ['allowance', 'deduction']);
            $table->boolean('is_fixed')->default(false);
            $table->decimal('value', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===== 5. tb_position_salary_components =====
        Schema::create('tb_position_salary_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('tb_positions')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('tb_salary_components')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['position_id', 'component_id']);
        });

        // ===== 6. tb_payroll_periods =====
        Schema::create('tb_payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_name', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        // ===== 7. tb_employee_payroll =====
        Schema::create('tb_employee_payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tb_employees')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('tb_payroll_periods')->onDelete('cascade');
            $table->decimal('base_salary', 12, 2);
            $table->decimal('total_allowance', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2); // di-set via trigger
            $table->timestamps();

            $table->unique(['employee_id', 'period_id']);
        });

        // ===== 8. tb_payroll_details =====
        Schema::create('tb_payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('tb_employee_payroll')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('tb_salary_components')->onDelete('restrict');
            $table->string('component_name', 100); // snapshot saat generate
            $table->enum('component_type', ['allowance', 'deduction']);
            $table->decimal('amount', 12, 2);
        });

        // ===== TRIGGER: Auto-hit net_salary (MySQL) =====
        DB::unprepared('
            CREATE TRIGGER trig_tb_employee_payroll_net_salary_insert
            BEFORE INSERT ON tb_employee_payroll
            FOR EACH ROW
            SET NEW.net_salary = NEW.base_salary + NEW.total_allowance - NEW.total_deduction;
        ');

        DB::unprepared('
            CREATE TRIGGER trig_tb_employee_payroll_net_salary_update
            BEFORE UPDATE ON tb_employee_payroll
            FOR EACH ROW
            SET NEW.net_salary = NEW.base_salary + NEW.total_allowance - NEW.total_deduction;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus trigger
        DB::unprepared('DROP TRIGGER IF EXISTS trig_tb_employee_payroll_net_salary_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS trig_tb_employee_payroll_net_salary_update;');

        // Drop tabel (urutan: anak → induk)
        Schema::dropIfExists('tb_payroll_details');
        Schema::dropIfExists('tb_employee_payroll');
        Schema::dropIfExists('tb_payroll_periods');
        Schema::dropIfExists('tb_position_salary_components');
        Schema::dropIfExists('tb_salary_components');
        Schema::dropIfExists('tb_employees');
        Schema::dropIfExists('tb_positions');
        Schema::dropIfExists('tb_monthly_fixed_expenses');
    }
};