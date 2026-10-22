<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the dormant 2022-era Microsoft token-vault table.
     * Verified empty in production before removal (2026-10-22).
     */
    public function up()
    {
        Schema::dropIfExists('docu_confs');
    }

    public function down()
    {
        Schema::create('docu_confs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('access')->nullable();
            $table->longText('refresh')->nullable();
            $table->timestamps();
        });
    }
};
