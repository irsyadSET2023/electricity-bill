<?php

namespace App\Enums;

enum State: string
{
    case JOHOR = 'Johor';
    case KEDAH = 'Kedah';
    case KELANTAN = 'Kelantan';
    case MELAKA = 'Melaka';
    case NEGERI_SEMBILAN = 'Negeri Sembilan';
    case PAHANG = 'Pahang';
    case PENANG = 'Pulau Pinang';
    case PERAK = 'Perak';
    case PERLIS = 'Perlis';
    case SABAH = 'Sabah';
    case SARAWAK = 'Sarawak';
    case SELANGOR = 'Selangor';
    case TERENGGANU = 'Terengganu';

    // Federal Territories
    case KUALA_LUMPUR = 'Kuala Lumpur';
    case LABUAN = 'Labuan';
    case PUTRAJAYA = 'Putrajaya';
}
