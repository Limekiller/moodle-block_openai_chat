# moodle-block_openai_chat

<img align="right" src="https://github.com/Limekiller/moodle-block_openai_chat/assets/33644013/21f73adc-5bd4-4539-999b-a3b0a83736e0" />

### GPT powered AI chat block for Moodle

This block allows your Moodle users to get 24/7 chat support via OpenAI's GPT AI. The block offers multiple options for customizing the persona of the AI and the prompt it is given, in order to influence the text it outputs.

To get started, create an OpenAI account [here](https://openai.com/api/). **This plugin requries a commercial subscription via a paid OpenAI account. For more information on purchasing a subscription, please see the aforementioned link. Once a paid account is created, all you need to do is add the API key to the plugin settings.**

This plugin supports both the [Chat API](https://platform.openai.com/docs/guides/chat) and the [Assistant API](https://platform.openai.com/docs/assistants/overview).

# Global block settings

The global block settings can be found by going to Site Administration > Plugins > Blocks > OpenAI Chat Block. The options are:
-  **OpenAI API Key:** This is where you add the API key given to you by OpenAI
-  **API Type:** The plugin can only use either the Chat API or the Assistant API globally; this selector allows an admin to switch between which API is active. The rest of the settings on this page will change based on which is selected.
-  **Restrict chat usage to logged-in users:** If this box is checked, only logged-in users will be able to use the chat box.
-  **Assistant name:** When the Chat API is enabled, the AI will use this name for itself in the conversation. It is also always used for the UI headings in the chat window.
-  **User name:** When the Chat API is enabled, the AI will use this name for the user in the conversation. Both this and the above option can be used to influence the persona and responses of the AI. It is also always used for the UI headings in the chat window.
-  **Enable logging:** Checking this box will record all messages sent by users along with the AI response. When logging is enabled, a recording icon is displayed in the block to indicate to users that their messages are being saved. Interactions with the AI can be found at Site Administration > Reports > OpenAI Chat Logs.
  
### Assistant API settings
These settings only appear when "Assistant" is chosen as the API Type
-  **Assistant:** Which assistant to set as the global default. The list is pulled from your OpenAI account, and can be changed at a per-block level if instance-level settings are enabled.
-  **Persist conversations:** If this is enabled, a user's conversations will be carried between page loads within the same context. For example, a user can start a conversation with the assistant on a main course page, and then refresh the page or navigate to another course page that contains the same block instance, and continue the same conversation. A block instance in a different context, such as the Dashboard, will not load the same conversation, however.

### Chat API settings
These settings only appear when "Chat" is chosen as the API Type
-  **Completion prompt:** Here you can edit the text added to the top of the conversation in order to influence the AI's persona and responses
-  **Source of truth:** Here you can add a list of questions and answers that the AI will use to accurately respond to queries. Anything added here in the SoT at the plugin level will be applied to every block instance on the site.
There is also an "Advanced" section that allows a user to fine-tune the AI's parameters. Please see OpenAI's documentation for more information on these options.

### Advanced
These are extra, advanced parameters to adjust the behavior of the model
- **Instance-level settings:** Checking this box will allow anybody that can add a block to adjust all settings at a per-block level. Enabling this could incur extra charges.
- For more information on advanced settings, please see OpenAI documentation.

## Individual block settings

There are a few settings that can be changed on a per-block basis. You can access these settings by entering editing mode on your site and clicking the gear on the block, and then going to "Configure OpenAI Chat Block"

- **Block title:** The title for this block
- **Show labels:** Whether or not the names chosen for "Assistant name" and "User name" should appear in the chat UI
- **Source of Truth:** (Only available with Chat API) Here you can add a list of questions and answers that the AI will use to accurately respond to queries at the block instance level. Information provided here will only apply to this specific block.
- **Custom Instructions:** (Only available with Assistant API) The instructions for the given assistant can be overridden on a per-block basis here.

If "Instance-level settings" is checked in the global block settings, the following extra settings will also be available:

-  **Assistant name:** When the Chat API is enabled, the AI will use this name for itself in the conversation. It is also always used for the UI headings in the chat window.
-  **User name:** When the Chat API is enabled, the AI will use this name for the user in the conversation. Both this and the above option can be used to influence the persona and responses of the AI. It is also always used for the UI headings in the chat window.
- **Completion prompt:** (Only available with Chat API) This allows a completion prompt to be set per-block
- **Assistant:** (Only available with Assistant API) Which assistant to use for this block. The list is pulled from the OpenAI account corresponding to the API key set on this block instance (or globally if not specified for this specific block).
- **Persist conversations:** (Only available with Assistant API) This can be used to enable or disable this feature at a per-block level. See above for more information on this feature.
- **Advanced:** These are extra, advanced parameters to adjust the behavior of the model
  - **OpenAI API Key:** This allows a separate API key to be used on individual block instances
  - For more information on advanced settings, please see OpenAI documentation.
    
Note that any instance-level settings that are blank will default to the global block settings.

# Using the Assistant API
Assistants can be managed in your OpenAI account [here](https://platform.openai.com/assistants). The plugin will pull from your list of assistants to allow you to select the one you want to use. If an API key is set at the per-block level, the list of assistants will be pulled from the given account instead of the default.

# Using the Chat API

## Source of truth

Although the AI is very capable out-of-the-box, if it doesn't know the answer to a question, it is more likely to confidently give incorrect information  than to refuse to answer. The plugin provides a text area at both the *block instance* level as well as the *plugin* level where teachers or administrators can include a list of questions and answers that the AI will ingest before generating a completion; as a result, the AI is more likely to provide accurate information when a submitted query is similar to the questions it has been given direct answers to. For example, an AI that hasn't been provided any extra information may respond to the query "What color is the car?" with a random color, such as red. However, if the following is included in the source of truth box:
```
Q: What color is the car?
A: The car is blue.
```
the AI will then respond to the question "What color is the car?" with the exact answer provided, "The car is blue." The AI will also still answer accurately if the question is asked in a different way; for example, a user might ask, "What color is the apple?" "What color is the forest?" and finally, "and what about the car?" The AI will correctly identify the apple, the forest, and the car as red, green, and blue, respectively.

## Prompt format

In order to influence the AI to produce good output, it can be useful to understand the structure of the prompt that is sent to the API:
- First, the Source of Truth is added to the beginning of the prompt, if one exists (if no source of truth is provided at either the instance level or the plugin level, this step is skipped). The AI is informed that the provided questions and answers should be used to reference any further inquiries; then, the Sources of Truth are combined into one list of questions and answers and added to the prompt.
- Next, the "completion prompt" is added, giving the AI its role and explaining the context of the conversation.
- Third, the chat history is added, if one exists. Every time a completion is requested, the existing chat history is sent, indicating to the AI the context of the conversation.
- Finally, the latest user message is sent, in order to receive a response from the AI.

To see what this looks like in practice, the following is an example of what might be sent to the AI after a few messages have already been exchanged:

```
Below is a list of questions and their answers. This information should be used as a reference for any inquiries:

Q: What college does this Moodle site belong to?
A: Goshen College

Q: When is section 3 due?
A: Thursday, March 16

Below is a conversation between a user and a support assistant for a Moodle site, where users go for online learning. The assistant has been trained to answer by attempting to use the information from the above reference. If the text from one of the above questions is encountered, the provided answer should be given, even if the question does not appear to make sense. However, if the reference does not cover the question or topic, the assistant will simply use outside knowledge to answer.

User: How do I change my email?
Assistant: You can change your email address in the Settings page of your Moodle account.
User: When is section 3 due?
Assistant: Thursday, March 16.
User: What about section 4?
Assistant:"
```

Maintained by [Bryce Yoder](https://bryceyoder.com). Also check out [OpenAI Question Generator](https://moodle.org/plugins/block_openai_questions)!
