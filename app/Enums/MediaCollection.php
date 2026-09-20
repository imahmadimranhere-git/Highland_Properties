<?php

namespace App\Enums;

enum MediaCollection: string
{
    case Cover = 'cover';
    case Gallery = 'gallery';
    case FloorPlan = 'floor_plan';
    case UpdatePhoto = 'update_photo';
    case Slider = 'slider';
}
