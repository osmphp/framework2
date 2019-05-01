<?php

namespace Manadev\Data\Tables\Migrations\Schema;

use Illuminate\Database\Schema\Blueprint;
use Manadev\Framework\Migrations\Migration;

class Tables extends Migration
{
    public function up() {
        $this->schema->create('tables', function(Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->unique('name');
        });
    }

    public function down() {
        $this->schema->drop('tables');
    }
}