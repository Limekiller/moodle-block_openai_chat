var questionString = 'Ask a question...'
var errorString = 'An error occurred! Please try again later.'

export const init = (data) => {

    const blockId = data['blockId']
    const api_type = data['api_type']

    addEventListener("beforeunload", e => {
        localStorage.removeItem("block_openai_chat_threadId");
    })

    document.querySelector('#openai_input').addEventListener('keyup', e => {
        if (e.which === 13 && e.target.value !== "") {
            addToChatLog('user', e.target.value)
            createCompletion(e.target.value, blockId, api_type)
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

/**
 * Add a message to the chat UI
 * @param {string} type Which side of the UI the message should be on. Can be "user" or "bot"
 * @param {string} message The text of the message to add
 */
const addToChatLog = (type, message) => {
    let messageContainer = document.querySelector('#openai_chat_log')
    
    const messageElem = document.createElement('div')
    messageElem.classList.add('openai_message')
    for (let className of type.split(' ')) {
        messageElem.classList.add(className)
    }

    const messageText = document.createElement('span')
    messageText.innerHTML = message
    messageElem.append(messageText)

    messageContainer.append(messageElem)
    if (messageText.offsetWidth) {
        messageElem.style.width = (messageText.offsetWidth + 40) + "px"
    }
    messageContainer.scrollTop = messageContainer.scrollHeight
}

/**
 * Makes an API request to get a completion from GPT-3, and adds it to the chat log
 * @param {string} message The text to get a completion for
 * @param {int} blockId The ID of the block this message is being sent from -- used to override settings if necessary
 * @param {string} api_type "assistant" | "chat" The type of API to use
 */
const createCompletion = (message, blockId, api_type) => {
    let threadId = null
    if (api_type === 'assistant') {
        threadId = localStorage.getItem("block_openai_chat_threadId")
    }  

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
            blockId: blockId,
            api_type: api_type,
            threadId: threadId
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
            addToChatLog('bot', data.message)
            if (data.thread_id) {
                localStorage.setItem("block_openai_chat_threadId", data.thread_id);
            }
        } catch (error) {
            addToChatLog('bot', data.error.message)
        }
        document.querySelector('#openai_input').focus()
    })
    .catch(error => {
        document.querySelector('#openai_input').classList.add('error')
        document.querySelector('#openai_input').placeholder = errorString
    })
}

/**
 * Using the existing messages in the chat history, create a string that can be used to aid completion
 * @return {JSONObject} A transcript of the conversation up to this point
 */
const buildTranscript = () => {
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
