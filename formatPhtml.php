<?php
class formatPhtml
{
    public function phtml_strtolower($mixedVar)
    {
        return (strtolower($mixedVar));
    }

    public function phtml_lower($mixedVar)
    {
        return (strtolower($mixedVar));
    }

    public function phtml_lowercase($mixedVar)
    {
        return (strtolower($mixedVar));
    }

    public function phtml_strtoupper($mixedVar)
    {
        return (strtoupper($mixedVar));
    }

    public function phtml_upper($mixedVar)
    {
        return (strtoupper($mixedVar));
    }

    public function phtml_uppercase($mixedVar)
    {
        return (strtoupper($mixedVar));
    }

    public function phtml_camelcase($mixedVar)
    {
        return (ucwords($mixedVar));
    }

    public function phtml_ucfirst($mixedVar)
    {
        return (ucfirst($mixedVar));
    }

    public function phtml_urlencode($mixedVar)
    {
        return (urlencode($mixedVar));
    }

    public function phtml_urldecode($mixedVar)
    {
        return (urldecode($mixedVar));
    }

    public function phtml_trim($mixedVar)
    {
        return (trim($mixedVar));
    }

    public function phtml_ltrim($mixedVar)
    {
        return (ltrim($mixedVar));
    }

    public function phtml_rtrim($mixedVar)
    {
        return (rtrim($mixedVar));
    }

    public function phtml_htmlentities($mixedVar)
    {
        return (htmlentities($mixedVar));
    }

    public function phtml_html_entity_decode($mixedVar)
    {
        return (html_entity_decode($mixedVar));
    }

    public function phtml_addslashes($mixedVar)
    {
        return (addslashes($mixedVar));
    }

    public function phtml_stripslashes($mixedVar)
    {
        return (stripslashes($mixedVar));
    }

    public function phtml_htmlespecialschars($mixedVar)
    {
        return (htmlspecialchars($mixedVar));
    }

    public function phtml_strlen($mixedVar)
    {
        return (strlen($mixedVar));
    }
}
