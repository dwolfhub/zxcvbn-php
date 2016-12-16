<?php

namespace ZxcvbnPhp;

class Scoring
{
    const START_UPPER = '/^[A-Z][^A-Z]+$/';
    const END_UPPER = '/^[^A-Z]+[A-Z]$/';
    const ALL_UPPER = '/^[^a-z]+$/';
    const ALL_LOWER = '/^[^A-Z]+$/';

}