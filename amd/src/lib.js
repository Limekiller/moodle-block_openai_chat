// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

import {getString} from 'core/str';

/**
 * Initialize block_openai_chat JS
 * @param {object} data The initial block data from PHP
 */
export const init = async data => {
    if (data['persistConvo']) {
        data['chat'] = initChatData(data)
    }

    // Submit on enter
    document.querySelector(`.block_openai_chat #openai_input`)?.addEventListener('keyup', e => {
        if (e.which === 13 && e.target.value !== "") {
            addToChatLog('user', e.target.value)
            createCompletion(e.target.value, data)
            e.target.value = ''
        }
    })
    // Submit on button click
    document.querySelector(`.block_openai_chat #go`)?.addEventListener('click', () => {
        const input = document.querySelector('#openai_input')
        if (input.value !== "") {
            addToChatLog('user', input.value)
            createCompletion(input.value, data)
            input.value = ''
        }
    })

    // Clear history on button click
    document.querySelector(`.block_openai_chat #refresh`)?.addEventListener('click', () => {
        data['chat'][data['blockId']] = {}
        document.querySelector(`.block_openai_chat #openai_chat_log`).innerHTML = ""
    })

    // Pop out window on button click
    document.querySelector(`.block_openai_chat #popout`)?.addEventListener('click', () => togglePopout())
    document.querySelector(`.drawer-right .drawertoggle`)?.addEventListener('click', () => togglePopout(true, false))
}

/**
 * Toggle the expanded chat window
 * @param {boolean} force (optional) Whether to force a certain state
 * @param {boolean} open (optional) Whether the window should be open or closed
 */
const togglePopout = (force = false, open = false) => {
    if (document.querySelector('.drawer.drawer-right')) {
        document.querySelector('.drawer.drawer-right').style.zIndex = '1041'
    }
    if (!force) {
        document.querySelector(`.block_openai_chat`).classList.toggle('expanded')
    } else {
        document.querySelector(`.block_openai_chat`).classList.remove('expanded')
        if (open) {
            document.querySelector(`.block_openai_chat`).classList.add('expanded')
        }
    }
}

/**
 * Add a message to the chat UI
 * @param {string} type Which side of the UI the message should be on. Can be "user" or "bot"
 * @param {string} message The text of the message to add
 */
const addToChatLog = (type, message) => {
    let messageContainer = document.querySelector(`.block_openai_chat #openai_chat_log`)

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
 * Makes an API request to get a completion from GPT-3, and adds it to the chat log
 * @param {string} message The text to get a completion for
 * @param {object} blockData The block data object
 */
const createCompletion = async (message, blockData) => {
    const questionString = await getString('askaquestion', 'block_openai_chat')
    const errorString = await getString('erroroccurred', 'block_openai_chat')

    const history = buildTranscript(blockData)

    document.querySelector(`.block_openai_chat #control_bar`).classList.add('disabled')
    document.querySelector(`.block_openai_chat #openai_input`).classList.remove('error')
    document.querySelector(`.block_openai_chat #openai_input`).placeholder = questionString
    document.querySelector(`.block_openai_chat #openai_input`).blur()
    addToChatLog('bot loading', '...');

    fetch(`${M.cfg.wwwroot}/blocks/openai_chat/api/completion.php`, {
        method: 'POST',
        body: JSON.stringify({
            message: message,
            history: history,
            blockId: blockData['blockId'],
        })
    })
    .then(response => {
        let messageContainer = document.querySelector(`.block_openai_chat #openai_chat_log`)
        messageContainer.removeChild(messageContainer.lastElementChild)
        document.querySelector(`.block_openai_chat #control_bar`).classList.remove('disabled')
        if (!response.ok) {
            throw Error(response.statusText)
        } else {
            return response.json()
        }
    })
    .then(data => {
        try {
            addToChatLog('bot', data.message)
            if (blockData['persistConvo']) {
                const newTranscript = buildTranscript(blockData, true)
                blockData['chat'][blockData['blockId']] = newTranscript
                localStorage.setItem("block_openai_chat_data", JSON.stringify(blockData['chat']))
            }
        } catch (error) {
            addToChatLog('bot', data.error.message)
        }
        document.querySelector(`.block_openai_chat[data-instance-id='${blockData['blockId']}'] #openai_input`).focus()
    })
    .catch(error => {
        console.log(error)
        document.querySelector(`.block_openai_chat #openai_input`).classList.add('error')
        document.querySelector(`.block_openai_chat #openai_input`).placeholder = errorString
    })
}

/**
 * Using the existing messages in the chat history, create a string that can be used to aid completion
 * @param {object} data The block data object
 * @param {boolean} includeLast Whether or not to include the last message in the log
 * @return {object} A transcript of the conversation up to this point
 */
const buildTranscript = (data, includeLast = false) => {
    let transcript = []
    document.querySelectorAll(`.block_openai_chat .openai_message`).forEach((message, index) => {
        if (index === document.querySelectorAll(`.block_openai_chat .openai_message`).length - 1 && !includeLast) {
            return
        }
        let user = data['userName']
        if (message.classList.contains('bot')) {
            user = data['assistantName']
        }
        transcript.push({"user": user, "message": message.innerText})
    })

    return transcript
}

/**
 * Initialize local storage chat data
 * @param {object} data The block data object
 * @returns {object} An object representing the current conversation state
 */
const initChatData = (data) => {
    let chatData = localStorage.getItem("block_openai_chat_data")

    if (chatData) {
        // If no data for this block yet, initialize the object
        chatData = JSON.parse(chatData)
        if (!chatData[data['blockId']]) {
            chatData[data['blockId']] = {}
        } else {
            for (const message of chatData[data['blockId']]) {
                addToChatLog(message.user === data['userName'] ? 'user' : 'bot', message.message)
            }
        }
    } else {
        // We don't even have a chat data object, so we'll create one
        chatData = {[data['blockId']]: {}}
    }

    return chatData
}
