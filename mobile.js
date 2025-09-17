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
        let lq = document.querySelector('#openai_chat_log > :last-child');
        let container = document.querySelector('#openai_chat_log');
        container.scrollTop = lq.offsetTop;
    }
    let newHtml = '<div class="openai_message bot"><span><p>' + msg + '</p></span></div>';

    document.querySelector('.chat-loading').remove();
    document.getElementById('openai_chat_log').insertAdjacentHTML('beforeend', newHtml);

    let container = document.querySelector('#openai_chat_log');
    let lastMessage = document.querySelector('#openai_chat_log div:last-child');
    container.scrollTop = lastMessage.offsetTop;
};
