export const init = () => {
    document.querySelector('#id_s_block_openai_chat_type').addEventListener('change', e => {
        window.location.reload()
    })
}