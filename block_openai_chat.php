<?php
class block_openai_chat extends block_base {
    public function init() {        
        $this->title = get_string('openai_chat', 'block_openai_chat');
    }

    function has_config() {return true;}

    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        if ($this->content !== null) {
          return $this->content;
        }
    
        $this->page->requires->js('/blocks/openai_chat/lib.js');
        $this->page->requires->js_init_call('init');

        // Determine if name labels should be shown
        $showlabels_css = '';
        if (!empty($this->config) && !$this->config->showlabels) {
            $showlabels_css = '
                .openai_message:before {
                    display: none;
                }
                .openai_message {
                    margin-bottom: 0.5rem;
                }
            ';
        }

        $agent_name = get_config('block_openai_chat', 'agentname') ? get_config('block_openai_chat', 'agentname') : 'Agent';
        $user_name = get_config('block_openai_chat', 'username') ? get_config('block_openai_chat', 'username') : 'User';

        $this->content         =  new stdClass;
        $this->content->text   = '
            <script>
                var agentName = "' . $agent_name . '";
                var userName = "' . $user_name . '"; 
            </script>

            <style>
                ' . $showlabels_css . '
                .openai_message.user:before {
                    content: "' . $user_name . '";
                }
                .openai_message.bot:before {
                    content: "' . $agent_name . '";
                }
            </style>

            <div id="openai_chat_log"></div>
        ';
        $this->content->footer = '<input id="openai_input" placeholder="Ask a question..." type="text" name="message" />';
     
        return $this->content;
    }
}