<?php

namespace Modules\Ticket\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Ticket\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $dummyText = "لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.";

        Faq::create([
            'question' => 'متن آزمایشی برای سوال کاربر متن آزمایشی برای سوال کاربر',
            'answer' => $dummyText,
            'sort_order' => 1,
            'last_change_by' => 1,
        ]);

        Faq::create([
            'question' => 'متن آزمایشی برای سوال کاربر متن آزمایشی برای سوال کاربر',
            'answer' => $dummyText,
            'sort_order' => 2,
            'last_change_by' => 1,
        ]);

        Faq::create([
            'question' => 'متن آزمایشی برای سوال کاربر متن آزمایشی برای سوال کاربر',
            'answer' => $dummyText,
            'sort_order' => 3,
            'last_change_by' => 1,
        ]);
    }
}
