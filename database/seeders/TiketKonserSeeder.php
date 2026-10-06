<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Seeder;

class TiketKonserSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'organizer' => 'Hectic Creative',
                'title'     => 'Sound Of Downton',
                'slug'      => 'SOD-2026',
                'poster'    => 'SOD.jpg',
                'gradient'  => 'linear-gradient(135deg,#0b0b0b,#7a0f12)',
                'start'     => '28-11-2026',
                'end' => null,
                'tickets'   => [['Regular', 210510, true]],
            ],
            [
                'organizer' => 'SMAN 16 Surabaya',
                'title'     => 'Classical',
                'slug'      => 'classical-2026',
                'poster'    => 'classical.jpg',
                'gradient'  => 'linear-gradient(135deg,#f7a21b,#e63d8c)',
                'start'     => '2026-10-31',
                'end' => null,
                'tickets'   => [
                    ['One Day Pass', 135000, false],
                    ['Two Day Pass', 225000, false],
                    ['Standing VIP One Day Pass', 250000, false],
                ],
            ],
            [
                'organizer' => 'Saint Johns Catholic School',
                'title'     => "SYNC 2026 'Luminaria'",
                'slug'      => 'sync-2026-luminaria',
                'poster'    => 'images/events/sync-2026-luminaria.jpg',
                'gradient'  => 'linear-gradient(135deg,#2b1b52,#7b3fa0 60%,#c9a24a)',
                'start'     => '2026-10-17',
                'end' => null,
                'tickets'   => [['Presale 1', 80000, true]],
            ],
            [
                'organizer' => 'Sky Entertainment',
                'title'     => 'Festival Tangerang 2026 - 76 Festival Indonesia Kita',
                'slug'      => 'festival-tangerang-2026',
                'poster'    => 'images/events/festival-tangerang.jpg',
                'gradient'  => 'linear-gradient(135deg,#3aa335,#a3d63a 55%,#b3202a)',
                'start'     => '2026-10-03',
                'end' => null,
                'tickets'   => [['Tiket Masuk', 25000, false]],
            ],
        ];

        foreach ($rows as $r) {
            $organizer = Organizer::firstOrCreate(['name' => $r['organizer']]);

            $event = Event::updateOrCreate(['slug' => $r['slug']], [
                'organizer_id'    => $organizer->id,
                'title'           => $r['title'],
                'poster'          => $r['poster'],
                'poster_gradient' => $r['gradient'],
                'start_date'      => $r['start'],
                'end_date'        => $r['end'],
                'is_popular'      => true,
            ]);

            $event->ticketCategories()->delete();
            foreach ($r['tickets'] as [$name, $price, $limited]) {
                $event->ticketCategories()->create([
                    'name' => $name,
                    'price' => $price,
                    'status' => 'on_sale',
                    'is_limited' => $limited,
                ]);
            }
        }
    }
}
