<?php

namespace App\Models;

/**
 * Class Company
 * 
 * Alias for Brand providing domain-specific naming:
 * Company -> CarModel -> CarVariant -> Product
 */
class Company extends Brand
{
    protected $table = 'brands';
}
