<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'UFO Sightings & Alien Encounters',
                'related_keywords' => 'ufo, alien, abduction, flying saucer, extraterrestrial, government disclosure, strange objects',
                'image' => 'uploads/categories/UFO_Sightings_&_Alien Encounters.png',
            ],
            [
                'name' => 'Cryptids & Mythical Creatures',
                'related_keywords' => 'bigfoot, mothman, chupacabra, loch ness, cryptid, monster',
                'image' => 'uploads/categories/Cryptids_&_Mythical_Creatures.png',
            ],
            [
                'name' => 'Paranormal Activity',
                'related_keywords' => 'ghost, haunted, possession, exorcism, spirit',
                'image' => 'uploads/categories/Paranormal_Activity.png',
            ],
            [
                'name' => 'Time Travel Claims',
                'related_keywords' => 'time travel, time traveler, from the future',
                'image' => 'uploads/categories/Time_Travel_Claims.png',
            ],
            [
                'name' => 'Bizarre Animal Behavior',
                'related_keywords' => 'zombie deer, bird fall, animal attack, unusual animal',
                'image' => 'uploads/categories/Bizarre_Animal_Behavior.png',
            ],
            [
                'name' => 'Magic and Haunted Objects',
                'related_keywords' => 'haunted doll, cursed painting, haunted mirror, bad luck object',
                'image' => 'uploads/categories/Magic_and_Haunted_Objects.png',
            ],
            [
                'name' => 'Strange Weather Phenomena',
                'related_keywords' => 'blood rain, fish rain, strange lightning, weather anomaly',
                'image' => 'uploads/categories/Strange_Weather_Phenomena.png',
            ],
            [
                'name' => 'Weird Science Discoveries',
                'related_keywords' => 'genetic anomaly, bizarre physics, unexplained experiment',
                'image' => 'uploads/categories/Weird_Science_Discoveries.png',
            ],
            [
                'name' => 'Weird Archeological Discoveries',
                'related_keywords' => 'unexplained artifact, ancient civilization, hieroglyph, mass extinction',
                'image' => 'uploads/categories/Weird_Archeological_Discoveries.png',
            ],
            [
                'name' => 'Government Conspiracies',
                'related_keywords' => 'secret base, mind control, government coverup, suppressed tech',
                'image' => 'uploads/categories/Government_Conspiracies.png',
            ],
            [
                'name' => 'Unusual World Records',
                'related_keywords' => 'longest nails, fastest backward running, weird record, eating contest',
                'image' => 'uploads/categories/Unusual_World_Records.png',
            ],
            [
                'name' => 'Apocalyptic Predictions',
                'related_keywords' => 'end of the world, doomsday, apocalypse, prophecy',
                'image' => 'uploads/categories/Apocalyptic_Predictions.png',
            ],
            [
                'name' => 'Mass Hysteria Events',
                'related_keywords' => 'dancing plague, laughing epidemic, tik tok hysteria',
                'image' => 'uploads/categories/Mass_Hysteria_Events.png',
            ],
            [
                'name' => 'Bizarre Medical Cases',
                'related_keywords' => 'rare condition, woke in coffin, extra limb',
                'image' => 'uploads/categories/Bizarre_Medical_Cases.png',
            ],
            [
                'name' => 'Weird Criminal Stories',
                'related_keywords' => 'funny crime, thief sleep, burglar call police, strange theft',
                'image' => 'uploads/categories/Weird_Criminal_Stories.png',
            ],
            [
                'name' => 'Displays of Superhuman Abilities',
                'related_keywords' => 'super strength, ice resistance, extreme memory',
                'image' => 'uploads/categories/Displays_of_Superhuman_Abilities.png',
            ],
            [
                'name' => 'Supernatural Locations',
                'related_keywords' => 'bermuda triangle, devil\'s kettle, mysterious place',
                'image' => 'uploads/categories/Supernatural_Locations.png',
            ],
        ];

        foreach ($categories as $data) {
            Category::create($data);
        }
    }
}
