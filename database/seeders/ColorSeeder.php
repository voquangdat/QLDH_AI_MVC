<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['color_ten' => 'Den', 'color_anh' => null],
            ['color_ten' => 'Trang', 'color_anh' => null],
            ['color_ten' => 'Do', 'color_anh' => null],
            ['color_ten' => 'Xanh navy', 'color_anh' => null],
            ['color_ten' => 'Xam', 'color_anh' => null],
            ['color_ten' => 'Vang', 'color_anh' => null],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(['color_ten' => $color['color_ten']], $color);
        }
    }
}
