<?php

namespace phpDB;

use MyCLabs\Enum\Enum;

/**
 * Class QueryType
 * @package phpDB
 *
 * @method static self SELECT()
 * @method static self INSERT()
 * @method static self UPDATE()
 * @method static self DELETE()
 */

class QueryType extends Enum
{

    private const SELECT = "SELECT";
    private const INSERT = "INSERT";
    private const UPDATE = "UPDATE";
    private const DELETE = "DELETE";

}