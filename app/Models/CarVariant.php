<?php

namespace App\Models;

/**
 * Class CarVariant
 * 
 * Alias for ModelVariant providing domain-specific naming:
 * Company -> CarModel -> CarVariant -> Product
 */
class CarVariant extends ModelVariant
{
    protected $table = 'model_variants';
}
