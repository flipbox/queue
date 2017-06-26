<?php

namespace flipbox\queue\jobs;

use flipbox\queue\jobs\traits\EventCollectionTrait;

abstract class AbstractEventCollection extends AbstractCollection
{
    use EventCollectionTrait;
}
