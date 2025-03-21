<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\PlaceTag;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaceTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Alonso's Cafe
        $alonsoCafe = Place::where('name', "Alonso's Cafe")->first();
        $tag1 = Tag::where('name', 'Bar')->first();
        $alonsoCafe->tags()->attach([$tag1->id]);

        $ermitaBellvtg = Place::where('name', "Ermita Sta Mª de Bellvitge")->first();
        $tag2 = Tag::where('name', 'Capilla')->first();
        $ermitaBellvtg->tags()->attach([$tag2->id]);

        $parqueBellvtg = Place::where('name', "Parque de Bellvitge")->first();
        $tag3 = Tag::where('name', 'Parque')->first();
        $parqueBellvtg->tags()->attach([$tag3->id]);

        $centreCultural = Place::where('name', "Centre Cultural Bellvitge-Gornal")->first();
        $tag4 = Tag::where('name', 'Centro Cultural')->first();
        $centreCultural->tags()->attach([$tag4->id]);

        $pistaBaloncesto = Place::where('name', "Pista de futsal")->first();
        $tag4 = Tag::where('name', 'Cancha de baloncesto')->first();
        $pistaBaloncesto->tags()->attach([$tag4->id]);

        $centroOcio = Place::where('name', "Pirulo Bellvitge")->first();
        $tag5 = Tag::where('name', 'Centro de ocio')->first();
        $centroOcio->tags()->attach([$tag5->id]);

        $petanca = Place::where('name', "Petanca de Bellvitge")->first();
        $tag6 = Tag::where('name', 'Parque')->first();
        $petanca->tags()->attach([$tag6->id]);

        $xurreria = Place::where('name', "Xurreria La Confiança")->first();
        $tag7 = Tag::where('name', 'Churrería')->first();
        $xurreria->tags()->attach([$tag7->id]);

        $trenBellvtg = Place::where('name', "Estación de tren Bellvitge")->first();
        $tag8 = Tag::where('name', 'Estación de tren')->first();
        $trenBellvtg->tags()->attach([$tag8->id]);

        $polideportivoBellvtg = Place::where('name', "Polideportivo Municipal Bellvitge Sergio Manzano")->first();
        $tag9 = Tag::where('name', 'Polideportivo')->first();
        $polideportivoBellvtg->tags()->attach([$tag9->id]);

        $caprabo = Place::where('name', "Caprabo")->first();
        $tag10 = Tag::where('name', 'Supermercado')->first();
        $caprabo->tags()->attach([$tag10->id]);

        $tabacs = Place::where('name', "Tabacs Exp. nº 21")->first();
        $tag11 = Tag::where('name', 'Estanco')->first();
        $tabacs->tags()->attach([$tag11->id]);

        $hospital = Place::where('name', "Consultas Externas Hospital de Bellvitge")->first();
        $tag12 = Tag::where('name', 'Hospital')->first();
        $hospital->tags()->attach([$tag12->id]);

        $pitas = Place::where('name', "BELLVITGE PITA KEBAB")->first();
        $tag12 = Tag::where('name', 'Kebab')->first();
        $pitas->tags()->attach([$tag12->id]);
        
    }
}
