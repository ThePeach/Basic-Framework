<?php

Class View {
    const CONTENT = '{CONTENT}';
    const TITLE   = '{TITLE}';
    const TPLDIR  = 'templates/';
    const TPLEXT  = '.xhtml';

    private $_page = null;
    private $_tokensValues = array();

    /**
     * This function assign the requested template
     */
    public function assembleTemplate($template, $layout) {
        if (null === $this->_page) {
            $this->_page = $this->_fileRead($layout);
        }
        $this->_substituteTokens(self::CONTENT, $this->_fileRead($template));
        // once the page has been constructed, replace the remaining tokens
        $this->_substituteTokens(
            array_keys($this->_tokensValues),
            $this->_tokensValues
        );
    }

    /**
     * Replaces all tokens with their assigned content inside the loaded page
     *
     * @param mixed $token   a string or an array of values to be replaced
     * @param mixed $content a string or an array of values to be used as replacement
     * 
     * @throws Exception if the params types are not of the same kind
     *                   or if the page has not been init
     */
    private function _substituteTokens($token, $content) {
        if (is_array($token) && !is_array($content)
            || !is_array($token) && is_array($content)
        ) {
            // that not what we wanted the user to do
            throw new Exception('token and content types mismatch', 500);
        }
        // we are always replaing inside of the loaded page
        if (null === $this->_page) {
            throw new Exception('page not loaded', 500);
        }
        // we should probably ensure the tokens are there when we start substituting them

        $this->_page = str_replace($token, $content, $this->_page);
    }

    /**
     * assigns certain tokens to parts of the page
     *
     * @param string $token   the token to be used
     * @param string $content the content to be assigned to the token
     */
    public function assign($token, $content) {
        // FIXME we should do something here to ensure it does not try to assign
        // the content to non existing tokens... maybe
        $this->_tokensValues[$token] = $content;
    }

    /**
     * This is a handy function to read the content of a file and return it
     * as a string.
     *
     * @param  string $filename the name of the file
     * @return string the content read
     */
    private function _fileRead($filename)
    {
        // add default base dir and extension before doing anything
        $filename = self::TPLDIR . $filename . self::TPLEXT;
        $fileContent = "";
        $file = fopen($filename, 'r');
        if ($file) {
            $fileContent = fread($file, filesize($filename));
            fclose($file);
        }
        return $fileContent;
    }

    /**
     * Overrides the magic function to print out the page without worring too much
     */
    public function  __toString() {
        return $this->_page;
    }
}
?>
