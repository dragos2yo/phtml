<?php

class condPhtml
{
    public function phtml_true($mixedVar)
    {
        return ($mixedVar == true);
    }

    public function phtml_false($mixedVar)
    {
        return ($mixedVar == false);
    }

    public function phtml_is_null($mixedVar)
    {
        return (is_null($mixedVar));
    }

    public function phtml_not_null($mixedVar)
    {
        return (!is_null($mixedVar));
    }

    public function phtml_is_array($mixedVar)
    {
        return (is_array($mixedVar));
    }

    public function phtml_not_array($mixedVar)
    {
        return (!is_array($mixedVar));
    }

    public function phtml_is_object($mixedVar)
    {
        return (is_object($mixedVar));
    }

    public function phtml_not_object($mixedVar)
    {
        return (!is_object($mixedVar));
    }

    public function phtml_is_numeric($mixedVar)
    {
        return (is_numeric($mixedVar));
    }

    public function phtml_is_int($mixedVar)
    {
        return (is_int($mixedVar));
    }

    public function phtml_not_numeric($mixedVar)
    {
        return (!is_numeric($mixedVar));
    }

    public function phtml_is_string($mixedVar)
    {
        return (is_string($mixedVar));
    }

    public function phtml_not_string($mixedVar)
    {
        return (!is_string($mixedVar));
    }

    public function phtml_empty($mixedVar)
    {
        return (empty($mixedVar));
    }

    public function phtml_not_empty($mixedVar)
    {
        return (!empty($mixedVar));
    }

    public function phtml_isset($mixedVar)
    {
        return (isset($mixedVar));
    }

    public function phtml_notset($mixedVar)
    {
        return (!isset($mixedVar));
    }

    public function phtml_preg_match($mixedVar, $strOperand)
    {
        return (preg_match($strOperand, $mixedVar));
    }

    public function phtml_not_preg_match($mixedVar, $strOperand)
    {
        return (!preg_match($strOperand, $mixedVar));
    }

    public function phtml_is_email($mixedVar)
    {
        return (!filter_var($mixedVar, FILTER_VALIDATE_EMAIL) ? false : true);
    }

    public function phtml_equalto($mixedVar, $strOperand)
    {
        return ($mixedVar == $strOperand);
    }

    public function phtml_not_equalto($mixedVar, $strOperand)
    {
        return ($mixedVar != $strOperand);
    }

    public function phtml_morethan($mixedVar, $strOperand)
    {
        return ($mixedVar > $strOperand);
    }

    public function phtml_morethan_equalto($mixedVar, $strOperand)
    {
        return ($mixedVar >= $strOperand);
    }

    public function phtml_lessthan($mixedVar, $strOperand)
    {
        return ($mixedVar < $strOperand);
    }

    public function phtml_lessthan_equalto($mixedVar, $strOperand)
    {
        return ($mixedVar <= $strOperand);
    }

    public function phtml_length($mixedVar, $strOperand)
    {
        if (is_array($mixedVar)) {
            return (sizeof($mixedVar) == (int)$strOperand);
        } else {
            return (strlen($mixedVar) == (int)($strOperand));
        }
    }

    public function phtml_maxlength($mixedVar, $strOperand)
    {
        if (is_array($mixedVar)) {
            return (sizeof($mixedVar) <= (int)$strOperand);
        } else {
            return (strlen($mixedVar) <= (int)($strOperand));
        }
    }

    public function phtml_minlength($mixedVar, $strOperand)
    {
        if (is_array($mixedVar)) {
            return (sizeof($mixedVar) >= (int)$strOperand);
        } else {
            return (strlen($mixedVar) >= (int)($strOperand));
        }
    }

    public function phtml_premitted_characters($mixedVar, $strOperand)
    {
        $size = strlen($mixedVar);
        for ($i = 0; $i < $size; $i++) {
            $thisCharacter = substr($mixedVar, $i, 1);
            if (strpos($strOperand, $thisCharacter) === false) {
                return (false);
            }
        }
        return (true);
    }

    public function phtml_not_premitted_characters($mixedVar, $strOperand)
    {
        $size = strlen($mixedVar);
        for ($i = 0; $i < $size; $i++) {
            $thisCharacter = substr($mixedVar, $i, 1);
            if (strpos($strOperand, $thisCharacter) !== false) {
                return (false);
            }
        }
        return (true);
    }
}
