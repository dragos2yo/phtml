<?php

class condPhtml
{
    public function phtml_true($var)
    {
        return ($var == true);
    }

    public function phtml_false($var)
    {
        return ($var == false);
    }

    public function phtml_is_null($var)
    {
        return (is_null($var));
    }

    public function phtml_not_null($var)
    {
        return (!is_null($var));
    }

    public function phtml_is_array($var)
    {
        return (is_array($var));
    }

    public function phtml_not_array($var)
    {
        return (!is_array($var));
    }

    public function phtml_is_object($var)
    {
        return (is_object($var));
    }

    public function phtml_not_object($var)
    {
        return (!is_object($var));
    }

    public function phtml_is_numeric($var)
    {
        return (is_numeric($var));
    }

    public function phtml_is_int($var)
    {
        return (is_int($var));
    }

    public function phtml_not_numeric($var)
    {
        return (!is_numeric($var));
    }

    public function phtml_is_string($var)
    {
        return (is_string($var));
    }

    public function phtml_not_string($var)
    {
        return (!is_string($var));
    }

    public function phtml_empty($var)
    {
        return (empty($var));
    }

    public function phtml_not_empty($var)
    {
        return (!empty($var));
    }

    public function phtml_isset($var)
    {
        return (isset($var));
    }

    public function phtml_notset($var)
    {
        return (!isset($var));
    }

    public function phtml_preg_match($var, $strOperand)
    {
        return (preg_match($strOperand, $var));
    }

    public function phtml_not_preg_match($var, $strOperand)
    {
        return (!preg_match($strOperand, $var));
    }

    public function phtml_is_email($var)
    {
        return (!filter_var($var, FILTER_VALIDATE_EMAIL) ? false : true);
    }

    public function phtml_equalto($var, $strOperand)
    {
        return ($var == $strOperand);
    }

    public function phtml_not_equalto($var, $strOperand)
    {
        return ($var != $strOperand);
    }

    public function phtml_morethan($var, $strOperand)
    {
        return ($var > $strOperand);
    }

    public function phtml_morethan_equalto($var, $strOperand)
    {
        return ($var >= $strOperand);
    }

    public function phtml_lessthan($var, $strOperand)
    {
        return ($var < $strOperand);
    }

    public function phtml_lessthan_equalto($var, $strOperand)
    {
        return ($var <= $strOperand);
    }

    public function phtml_length($var, $strOperand)
    {
        if (is_array($var)) {
            return (sizeof($var) == (int)$strOperand);
        } else {
            return (strlen($var) == (int)($strOperand));
        }
    }

    public function phtml_maxlength($var, $strOperand)
    {
        if (is_array($var)) {
            return (sizeof($var) <= (int)$strOperand);
        } else {
            return (strlen($var) <= (int)($strOperand));
        }
    }

    public function phtml_minlength($var, $strOperand)
    {
        if (is_array($var)) {
            return (sizeof($var) >= (int)$strOperand);
        } else {
            return (strlen($var) >= (int)($strOperand));
        }
    }

    public function phtml_premitted_characters($var, $strOperand)
    {
        $size = strlen($var);
        for ($i = 0; $i < $size; $i++) {
            $thisCharacter = substr($var, $i, 1);
            if (strpos($strOperand, $thisCharacter) === false) {
                return (false);
            }
        }
        return (true);
    }

    public function phtml_not_premitted_characters($var, $strOperand)
    {
        $size = strlen($var);
        for ($i = 0; $i < $size; $i++) {
            $thisCharacter = substr($var, $i, 1);
            if (strpos($strOperand, $thisCharacter) !== false) {
                return (false);
            }
        }
        return (true);
    }
}
