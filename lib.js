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
    let messageContainer = document.querySelector('#openai_chat_log')
    messageContainer.insertAdjacentHTML('beforeend', `
        <div class='openai_message ${type}'>
            ${message}
        </div>
    `)
    messageContainer.scrollTop = messageContainer.scrollHeight
}

const createCompletion = (message) => {
    const history = buildTranscript()
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
        addToChatLog('bot', data.choices[0].text)
    })
}

const buildTranscript = () => {
    let transcript = ''
    document.querySelectorAll('.openai_message').forEach((message, index) => {
        if (index === document.querySelectorAll('.openai_message').length - 1) {
            return
        }

        let user = 'User'
        if (message.classList.contains('bot')) {
            user = 'Agent'
        }
        transcript += `${user}: ${message.innerText}\n`
    })

    return transcript
}