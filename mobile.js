this.answer = function(result) {
    var msg = '';
    if (result.error == "") {
        msg = result.generatedcontent;
    } else {
        msg = 'error: ' + result.error;
    }
    this.update_history(msg);
};

this.update_history = function(msg) {
    let question = document.getElementById('openai_input').value;
    let history = document.getElementById('openai_history');
    let hst = [];
    if ((history.value != undefined) && (history.value !== '')) {
        hst = JSON.parse(history.value);
    }
    if (question == '') {
    } else {
        document.getElementById('openai_input').value = '';
        document.getElementById('openai_chat_log').insertAdjacentHTML(
             'beforeend',
             '<div class="openai_message user"><span>' + question + '</span></div>' +
             '<ion-item class="chat-loading">' +
                 '<ion-spinner name="crescent"></ion-spinner>' +
             '</ion-item>'
        );
        hst.push({'user': 'User', 'message': question});
        let lq = document.querySelector('#openai_chat_log > :last-child');
        let container = document.querySelector('#openai_chat_log');
        container.scrollTop = lq.offsetTop;
    }
    let assistantname = document.getElementById('openai_assistantname');
    let newHtml = '<span style="color: gray; margin-bottom: .2em">' + assistantname.innerHTML + '</span><div class="openai_message bot"><span><p>' + msg + '</p></span></div>';

    document.querySelector('.chat-loading').remove();
    document.getElementById('openai_chat_log').insertAdjacentHTML('beforeend', newHtml);

    let container = document.querySelector('#openai_chat_log');
    let lastMessage = document.querySelector('#openai_chat_log div:last-child');
    hst.push({'user': assistantname.innerHTML, 'message': msg})
    history.value = JSON.stringify(hst);

    container.scrollTop = lastMessage.offsetTop;
};
document.querySelector(`.block_openai_chat #refresh`).addEventListener('click', e => {
    document.getElementById('openai_history').value = "";
    document.getElementById('openai_chat_log').innerHTML = "";
})
