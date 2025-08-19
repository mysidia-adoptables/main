<?php

/**
 * Validates a number as defined by the CSS spec.
 */
class HTMLPurifier_AttrDef_CSS_Number extends HTMLPurifier_AttrDef
{

    /**
     * @param bool $non_negative indicates whether negatives are forbidden
     */
    public function __construct(protected $non_negative = false)
    {
    }

    /**
     * @param string $number
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string|bool
     * @warning Some contexts do not pass $config, $context. These
     *          variables should not be used without checking HTMLPurifier_Length
     */
    public function validate($number, $config, $context)
    {
        $number = $this->parseCDATA($number);

        if ($number === '') {
            return false;
        }
        if ($number === '0') {
            return '0';
        }

        $sign = '';
        switch ($number[0]) {
            case '-':
                if ($this->non_negative) {
                    return false;
                }
                $sign = '-';
            case '+':
                $number = substr((string) $number, 1);
        }

        if (ctype_digit((string) $number)) {
            $number = ltrim($number, '0');
            return $number ? $sign . $number : '0';
        }

        // Period is the only non-numeric character allowed
        if (!str_contains((string) $number, '.')) {
            return false;
        }

        [$left, $right] = explode('.', (string) $number, 2);

        if ($left === '' && $right === '') {
            return false;
        }
        if ($left !== '' && !ctype_digit($left)) {
            return false;
        }

        // Remove leading zeros until positive number or a zero stays left
        if (ltrim($left, '0') != '') {
            $left = ltrim($left, '0');
        } else {
            $left = '0';
        }

        $right = rtrim($right, '0');

        if ($right === '') {
            return $left ? $sign . $left : '0';
        } elseif (!ctype_digit($right)) {
            return false;
        }
        return $sign . $left . '.' . $right;
    }
}

// vim: et sw=4 sts=4
