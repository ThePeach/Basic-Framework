<?php

/**
 * The view is actually using an inline replace method for templates.
 * Pros:
 *  - keep each template as clean as possible
 *  - separate markup from logic
 *  - avoid interpretation of any piece of code
 *  - provides content to AJAX easily
 *
 * Cons:
 *  - can have only one template per page (given by {CONTENT})
 *  - inhibit interpretation of code
 *  - prohibit iteration of templates of omogeneous content
 *  - creates the need of creating a const variable
 *  - creates the need of adapting the template accordingly
 *
 * This could obviously have been improved by removing the need to create a
 * const variable and by making the code an auto generated class.
 *
 * **EXAMPLE**
 * $view = new View();
 * // assign a title to the page (substituting {TITLE}
 * $view->assign(View::TITLE, 'My test page');
 * // inserts content.xhtml into page.xhtml where {CONTENT} is
 * $view->assembleTemplate('content', 'page');
 * // outputs the view
 * echo $view;
 *
 */
Class View
{
    const CONTENT = '{CONTENT}';
    const TITLE   = '{TITLE}';
    const TPLDIR  = 'templates/';
    const TPLEXT  = '.xhtml';

    private $_page = null;
    private $_tokensValues = array();

    /**
     * This function assign the requested template
     *
     * @param string $template the name of the template to be included
     * @param string $layout   the name of the layout to be used as wrapper
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
