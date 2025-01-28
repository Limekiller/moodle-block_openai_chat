var questionString = 'Ask a question...'
var errorString = 'An error occurred! Please try again later.'

export const init = (data) => {
    const blockId = data['blockId']
    const api_type = data['api_type']
    const persistConvo = data['persistConvo']

    // Initialize local data storage if necessary
    // If a thread ID exists for this block, make an API request to get existing messages
    if (api_type === 'assistant') {
        chatData = localStorage.getItem("block_openai_chat_data")
        if (chatData) {
            chatData = JSON.parse(chatData)
            if (chatData[blockId] && chatData[blockId]['threadId'] && persistConvo === "1") {
                fetch(`${M.cfg.wwwroot}/blocks/openai_chat/api/thread.php?thread_id=${chatData[blockId]['threadId']}`)
                .then(response => response.json())
                .then(data => {
                    for (let message of data) {
                        addToChatLog(message.role === 'user' ? 'user' : 'bot', message.message, blockId)
                    }
                })
                // Some sort of error in the API call. Probably the thread no longer exists, so lets reset it
                .catch(error => {
                    chatData[blockId] = {}
                    localStorage.setItem("block_openai_chat_data", JSON.stringify(chatData));
                })
            // The block ID doesn't exist in the chat data object, so let's create it
            } else {
                chatData[blockId] = {}
            }
        // We don't even have a chat data object, so we'll create one
        } else {
            chatData = {[blockId]: {}}
        }
        localStorage.setItem("block_openai_chat_data", JSON.stringify(chatData));
    }

    // Prevent sidebar from closing when osk pops up (hack for MDL-77957)
    window.addEventListener('resize', event => {
        event.stopImmediatePropagation();
    }, true);

    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).addEventListener('keyup', e => {
        if (e.which === 13 && e.target.value !== "") {
            addToChatLog('user', e.target.value, blockId)
            createCompletion(e.target.value, blockId, api_type)
            e.target.value = ''
        }
    })
    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #go`).addEventListener('click', e => {
        const input = document.querySelector('#openai_input')
        if (input.value !== "") {
            addToChatLog('user', input.value, blockId)
            createCompletion(input.value, blockId, api_type)
            input.value = ''
        }
    })

    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #refresh`).addEventListener('click', e => {
        clearHistory(blockId)
    })

    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #popout`).addEventListener('click', e => {
        if (document.querySelector('.drawer.drawer-right')) {
            document.querySelector('.drawer.drawer-right').style.zIndex = '1041'
        }
        document.querySelector(`.block_openai_chat[data-instance-id='${blockId}']`).classList.toggle('expanded')
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
 * @param {int} blockId The ID of the block to manipulate
 */
const addToChatLog = (type, message, blockId) => {
    let messageContainer = document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_chat_log`)
    
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
    messageContainer.closest('.block_openai_chat > div').scrollTop = messageContainer.scrollHeight
}

/**
 * Clears the thread ID from local storage and removes the messages from the UI in order to refresh the chat
 */
const clearHistory = (blockId) => {
    chatData = localStorage.getItem("block_openai_chat_data")
    if (chatData) {
        chatData = JSON.parse(chatData)
        if (chatData[blockId]) {
            chatData[blockId] = {}
            localStorage.setItem("block_openai_chat_data", JSON.stringify(chatData));
        }
    }
    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_chat_log`).innerHTML = ""
}

/**
 * Makes an API request to get a completion from GPT-3, and adds it to the chat log
 * @param {string} message The text to get a completion for
 * @param {int} blockId The ID of the block this message is being sent from -- used to override settings if necessary
 * @param {string} api_type "assistant" | "chat" The type of API to use
 */
const createCompletion = (message, blockId, api_type) => {
    let threadId = null
    let chatData

    // If the type is assistant, attempt to fetch a thread ID
    if (api_type === 'assistant') {
        chatData = localStorage.getItem("block_openai_chat_data")
        if (chatData) {
            chatData = JSON.parse(chatData)
            if (chatData[blockId]) {
                threadId = chatData[blockId]['threadId'] || null
            }
        } else {
            // create the chat data item if necessary
            chatData = {[blockId]: {}}
        }
    }  

    const history = buildTranscript(blockId)

    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #control_bar`).classList.add('disabled')
    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).classList.remove('error')
    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).placeholder = questionString
    document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).blur()
    addToChatLog('bot loading', '...', blockId);

    fetch(`${M.cfg.wwwroot}/blocks/openai_chat/api/completion.php`, {
        method: 'POST',
        body: JSON.stringify({
            message: message,
            history: history,
            blockId: blockId,
            threadId: threadId
        })
    })
    .then(response => {
        let messageContainer = document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_chat_log`)
        messageContainer.removeChild(messageContainer.lastElementChild)
        document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #control_bar`).classList.remove('disabled')

        if (!response.ok) {
            throw Error(response.statusText)
        } else {
            return response.json()
        }
    })
    .then(data => {
        try {
            addToChatLog('bot', data.message, blockId)
            if (data.thread_id) {
                chatData[blockId]['threadId'] = data.thread_id
                localStorage.setItem("block_openai_chat_data", JSON.stringify(chatData));
            }
        } catch (error) {
            console.log(error)
            addToChatLog('bot', data.error.message, blockId)
        }
        document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).focus()
    })
    .catch(error => {
        console.log(error)
        document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).classList.add('error')
        document.querySelector(`.block_openai_chat[data-instance-id='${blockId}'] #openai_input`).placeholder = errorString
    })
}

/**
 * Using the existing messages in the chat history, create a string that can be used to aid completion
 * @param {int} blockId The block from which to build the history
 * @return {JSONObject} A transcript of the conversation up to this point
 */
const buildTranscript = (blockId) => {
    let transcript = []
    document.querySelectorAll(`.block_openai_chat[data-instance-id='${blockId}'] .openai_message`).forEach((message, index) => {
        if (index === document.querySelectorAll(`.block_openai_chat[data-instance-id='${blockId}'] .openai_message`).length - 1) {
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
