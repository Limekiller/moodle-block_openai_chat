<?php
class block_openai_chat extends block_base {
    public function init() {
        $this->title = get_string('openai_chat', 'block_openai_chat');
    }

    function has_config() {return true;}

    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
    
        $this->page->requires->js('/blocks/openai_chat/lib.js');
        $this->page->requires->js_init_call('init');

        $this->content         =  new stdClass;
        $this->content->text   = '<div id="openai_chat_log"></div>';
        $this->content->footer = '<input id="openai_input" placeholder="Ask a question..." type="text" name="message" />';
     
        return $this->content;
    }
}