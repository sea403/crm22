<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'account_id')) {
                $table->unsignedBigInteger('account_id')->nullable()->after('created_by');
                $table->index('account_id');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'account_id')) {
                $table->unsignedBigInteger('account_id')->nullable()->after('created_by');
                $table->index('account_id');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'account_id')) {
                $table->unsignedBigInteger('account_id')->nullable()->after('created_by');
                $table->index('account_id');
            }
            if (!Schema::hasColumn('expenses', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('account_id');
                $table->index('project_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (Schema::hasColumn('contacts', 'account_id')) {
                $table->dropColumn('account_id');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'account_id')) {
                $table->dropColumn('account_id');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'project_id')) {
                $table->dropColumn('project_id');
            }
            if (Schema::hasColumn('expenses', 'account_id')) {
                $table->dropColumn('account_id');
            }
        });
    }
};

