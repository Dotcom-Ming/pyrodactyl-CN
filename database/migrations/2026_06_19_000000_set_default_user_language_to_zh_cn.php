<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    DB::table('users')->where('language', 'en')->update(['language' => 'zh_CN']);
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    DB::table('users')->where('language', 'zh_CN')->update(['language' => 'en']);
  }
};
