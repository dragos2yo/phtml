<?php
class formatPhtml
{
    public function phtml_strtolower($var)
    {
        return (strtolower($var));
    }

    public function phtml_lower($var)
    {
        return (strtolower($var));
    }

    public function phtml_lowercase($var)
    {
        return (strtolower($var));
    }

    public function phtml_strtoupper($var)
    {
        return (strtoupper($var));
    }

    public function phtml_upper($var)
    {
        return (strtoupper($var));
    }

    public function phtml_uppercase($var)
    {
        return (strtoupper($var));
    }

    public function phtml_camelcase($var)
    {
        return (ucwords($var));
    }

    public function phtml_ucfirst($var)
    {
        return (ucfirst($var));
    }

    public function phtml_urlencode($var)
    {
        return (urlencode($var));
    }

    public function phtml_urldecode($var)
    {
        return (urldecode($var));
    }

    public function phtml_trim($var)
    {
        return (trim($var));
    }

    public function phtml_ltrim($var)
    {
        return (ltrim($var));
    }

    public function phtml_rtrim($var)
    {
        return (rtrim($var));
    }

    public function phtml_htmlentities($var)
    {
        return (htmlentities($var));
    }

    public function phtml_html_entity_decode($var)
    {
        return (html_entity_decode($var));
    }

    public function phtml_addslashes($var)
    {
        return (addslashes($var));
    }

    public function phtml_stripslashes($var)
    {
        return (stripslashes($var));
    }

    public function phtml_htmlespecialschars($var)
    {
        return (htmlspecialchars($var));
    }

    public function phtml_strlen($var)
    {
        return (strlen($var));
    }
}
