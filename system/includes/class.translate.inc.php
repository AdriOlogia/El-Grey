<?php

class Translator {

    private $language   = 'eng';
    private $lang       = array();

    public function __construct($language)
    {
        $this->language = $language;
    }

    private function findString($str)
    {
        if (!array_key_exists($str, $this->lang[$this->language]))
            throw new Exception("The language key ".$str."doesn't exist.");
        
        return $this->lang[$this->language][$str];
    }

    private function splitStrings($str)
    {
        return explode('=',trim($str));
    }

    public function __tr($str)
    {  
        if (!array_key_exists($this->language, $this->lang))
        {
            if (!file_exists(DIR_includes.$this->language.'.txt'))
            	throw new Exception("The language doesn't exist.");
                
            $strings = array_map(array($this,'splitStrings'),file(DIR_includes.$this->language.'.txt'));
            foreach ($strings as $k => $v)
            {
                $this->lang[$this->language][$v[0]] = $v[1];
            }
            
            return $this->findString($str);
            
        } else {
            return $this->findString($str);
        }
    }
}