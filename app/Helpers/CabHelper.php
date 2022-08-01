<?php

/**
 * @param string|null $amount
 * @return bool
 */
function is_zero(?string $amount) : bool
{
    try {
        return (empty($amount) or custom_number($amount) == zero());
    }catch (Exception $e){
        return true;
    }
}


//Custom Money Number Format
function custom_number($number,$decimals = 2)
{
    return number_format($number,$decimals,'.',false);
}


/**
 * @return string
 */
function zero()
{
    return custom_number(0);
}