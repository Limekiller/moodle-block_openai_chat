export const init = (type) => {
    document.querySelector('#id_s_block_openai_chat_type').value = type
    document.querySelector('#id_s_block_openai_chat_type').addEventListener('change', e => {
        window.onbeforeunload = null
        const parser = new URL(window.location);
        parser.searchParams.set('type', e.target.value);
        window.location = parser.href;
    })
}