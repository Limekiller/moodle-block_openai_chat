var questionString = 'Ask a question...'
var errorString = 'An error occurred! Please try again later.'

const init = (Y, sourceOfTruth) => {
    document.querySelector('#openai_input').addEventListener('keyup', e => {
        if (e.which === 13 && e.target.value !== "") {
            addToChatLog('user', e.target.value)
            createCompletion(e.target.value, sourceOfTruth)
            e.target.value = ''
        }
    })

    require(['core/str'], function(str) {
        var strings = [
            {
                key: 'askaquestion',
                component: 'block_openai_chat'
            },
            {
                key: 'erroroccurred',
                component: 'block_openai_chat'
            },
        ];
        str.get_strings(strings).then((results) => {
            questionString = results[0];
            errorString = results[1];
        });
    });
}

const addToChatLog = (type, message) => {
    /**
     * Add a message to the chat UI
     * @param {string} type Which side of the UI the message should be on. Can be "user" or "bot"
     * @param {string} message The text of the message to add
     */
    let messageContainer = document.querySelector('#openai_chat_log')
    messageContainer.insertAdjacentHTML('beforeend', `
        <div class='openai_message ${type}'>
            ${message}
        </div>
    `)
    messageContainer.scrollTop = messageContainer.scrollHeight
}

const createCompletion = (message, sourceOfTruth) => {
    /**
     * Makes an API request to get a completion from GPT-3, and adds it to the chat log
     * @param {string} message The text to get a completion for
     */
    const history = buildTranscript()
    document.querySelector('#openai_input').classList.add('disabled')
    document.querySelector('#openai_input').classList.remove('error')
    document.querySelector('#openai_input').placeholder = questionString
    document.querySelector('#openai_input').blur()
    addToChatLog('bot loading', '...');

    fetch(`${M.cfg.wwwroot}/blocks/openai_chat/api/completion.php`, {
        method: 'POST',
        body: JSON.stringify({
            message: message,
            history: history,
            sourceOfTruth: sourceOfTruth
        })
    })
    .then(response => {
        let messageContainer = document.querySelector('#openai_chat_log')
        messageContainer.removeChild(messageContainer.lastElementChild)
        document.querySelector('#openai_input').classList.remove('disabled')

        if (!response.ok) {
            throw Error(response.statusText)
        } else {
            return response.json()
        }
    })
    .then(data => {
        try {
            data.choices[0].text ? addToChatLog('bot', data.choices[0].text) : addToChatLog('bot', data.choices[0].message.content);
        } catch (error) {
            addToChatLog('bot', data.error.message)
        }
    })
    .catch(error => {
        document.querySelector('#openai_input').classList.add('error')
        document.querySelector('#openai_input').placeholder = errorString
    })

}

const buildTranscript = () => {
    /**
     * Using the existing messages in the chat history, create a string that can be used to aid completion
     * @return {JSONObject} A transcript of the conversation up to this point
     */
    let transcript = []
    document.querySelectorAll('.openai_message').forEach((message, index) => {
        if (index === document.querySelectorAll('.openai_message').length - 1) {
            return
        }

        let user = userName
        if (message.classList.contains('bot')) {
            user = assistantName
        }
        transcript.push({"user": user, "message": message.innerText})
    })

    return transcript
}
