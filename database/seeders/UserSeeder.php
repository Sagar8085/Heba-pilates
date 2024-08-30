<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionTier;
use App\Models\Member;
use App\Models\ReformerBooking;
use App\Models\Reformer;
use App\Models\Gym;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->times(1000)->create();

        $tiers = ['standard', 'premium', 'one-month-unlimited', 'vip-unlimited', 'premium-membership', 'unlimited-membership', 'premium-membership-subscription', 'unlimited-membership-subscription'];

        User::chunk(100, function($users) use($tiers) {
            foreach ($users as $user) {

                $member = Member::updateOrCreate(['user_id' => $user->id], ['home_studio_id' => mt_rand(1, 3)]);

                $rand = mt_rand(1, 4);
                $randomTier = array_rand($tiers);

                $tier = SubscriptionTier::where('slug', $tiers[$randomTier])->first();

                /** If 1, create an active subscription that renews. */
                if ($rand === 1) {
                    $sub = Subscription::create([
                        'user_id' => $user->id,
                        'tier' => $tier->slug,
                        'expires' => Carbon::now()->addDays(mt_rand(1, 30)),
                        'renew' => 1,
                        'studio_credits' => $tier->studio_credits,
                        'billing_type' => 'manual'
                    ]);

                    $numberOfBookingsToCreate = mt_rand(0, 10);
                    $this->createBookings($numberOfBookingsToCreate, $user, $member, $sub);
                }

                /** If 2, create an active subscription that does not renew. */
                else if ($rand === 2) {
                    Subscription::create([
                        'user_id' => $user->id,
                        'tier' => $tier->slug,
                        'expires' => Carbon::now()->addDays(mt_rand(1, 30)),
                        'renew' => 0,
                        'studio_credits' => $tier->studio_credits,
                        'billing_type' => 'manual'
                    ]);
                }

                /** If 3, create an expired subscription. */
                else if ($rand === 3) {
                    Subscription::create([
                        'user_id' => $user->id,
                        'tier' => $tier->slug,
                        'expires' => Carbon::now()->subDays(mt_rand(1, 100)),
                        'renew' => 0,
                        'studio_credits' => $tier->studio_credits,
                        'billing_type' => 'manual'
                    ]);
                }

                /**
                 * If 4, don't create anything.
                 * We still need the 4th number even though it doesn't do otherwise
                 * a subscription would always be created.
                 */
                else if ($rand === 4) {

                }
            }
        });
    }

    private function createBookings($numberOfBookingsToCreate, $user, $member, $sub)
    {
        for ($x = 0; $x < $numberOfBookingsToCreate; $x++) {
            ReformerBooking::create([
                'user_id' => $user->id,
                'reformer_id' => Reformer::where('gym_id', $member->home_studio_id)->inRandomOrder()->first(),
                'datetime' => Carbon::parse('2021-12-01 '.mt_rand(10,21).':00:00')->addDays(mt_rand(1,70)),
                'bookable_id' => $sub->id,
                'bookable_type' => 'App\Models\Subscription'
            ]);
        }
    }
}
