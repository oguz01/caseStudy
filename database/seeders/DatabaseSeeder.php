<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Her seed işleminde klasör içini boşaltıp tekrardan dolduracak şekilde ayarladım.
     * Bunu sadece hızlı başlangıç için düşündüm.
     */
    public function run()
    {
        $path = 'public/images';
        is_dir($path) ? array_map('unlink', glob("$path/*.*")) : mkdir($path);
        \App\Models\Image::factory(10)->create();

    }

}
