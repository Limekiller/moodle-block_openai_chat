const init = () => {
    document.querySelector('#openai_input').addEventListener('keyup', e => {
        if (e.which === 13) {
            addToChatLog('user', e.target.value)
            createCompletion(e.target.value)
            e.target.value = ''
        }
    })
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

const createCompletion = (message) => {
    /**
     * Makes an API request to get a completion from GPT-3, and adds it to the chat log
     * @param {string} message The text to get a completion for
     */
    const history = buildTranscript()
    document.querySelector('#openai_input').classList.add('disabled')
    document.querySelector('#openai_input').blur()
    addToChatLog('bot loading', '...');

    fetch('/blocks/openai_chat/api/completion.php', {
        method: 'POST',
        body: JSON.stringify({
            message: message,
            history: history
        })
    })
    .then(response => response.json())
    .then(data => {
        let messageContainer = document.querySelector('#openai_chat_log')
        messageContainer.removeChild(messageContainer.lastElementChild)
        document.querySelector('#openai_input').classList.remove('disabled')

        try {
            addToChatLog('bot', data.choices[0].text)
        } catch (error) {
            addToChatLog('bot', data.error.message)
        }
    })
}

const buildTranscript = () => {
    /**
     * Using the existing messages in the chat history, create a string that can be used to aid completion
     * @return {string} A transcript of the conversation up to this point
     */
    let transcript = ''
    document.querySelectorAll('.openai_message').forEach((message, index) => {
        if (index === document.querySelectorAll('.openai_message').length - 1) {
            return
        }

        let user = userName
        if (message.classList.contains('bot')) {
            user = agentName
        }
        transcript += `${user}: ${message.innerText}\n`
    })

    return transcript
}
