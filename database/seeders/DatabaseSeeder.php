<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Transaction;
use App\Models\Transaction_item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        for ($i=1; $i <= 10; $i++) { 

            $user = User::factory()->create();

            $uuid = Str::uuid()->toString();

            $trans = Transaction::create([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'device_timestamp' => Carbon::now()->toDateTimeString(),
                'total_amount' => 0,
                'paid_amount' => 0,
                'change_amount' => 0,
                'payment_method' => 'cash'
            ]);

            $price = [];
            for ($j=0; $j < 3 ; $j++) { 
                $uuid = Str::uuid()->toString();
                $price[$j] = random_int(10, 200);
                Transaction_item::create([
                    'uuid' => $uuid,
                    'transaction_id' => $trans->id,
                    'title' => 'product_'.$i.'_'.$j,
                    'qty' => 1,
                    'price' => $price[$j]
                ]);
            }

            $total_amount = array_sum($price);
            $paid_amount = random_int($total_amount, $total_amount+100);
            $trans->total_amount = $total_amount;
            $trans->paid_amount = $paid_amount;
            $trans->change_amount = $paid_amount - $total_amount;
            $trans->save();

        }
    }
}
