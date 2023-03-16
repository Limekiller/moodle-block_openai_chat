# moodle-block_openai_chat

<img align="right" src="https://user-images.githubusercontent.com/33644013/162025197-52c34e24-66a8-46e7-ab95-b0f65268031b.png" />

### GPT powered AI chat block for Moodle

This block allows your Moodle users to get 24/7 chat support via OpenAI's GPT AI. The block offers multiple options for customizing the persona of the AI and the prompt it is given, in order to influence the text it outputs.

To get started, create an OpenAI account [here](https://openai.com/api/). **This plugin requries a commercial subscription via a paid OpenAI account. For more information on purchasing a subscription, please see the aforementioned link. Once a paid account is created, all you need to do is add the API key to the plugin settings.**

This plugin supports both the [Completion API](https://beta.openai.com/docs/guides/completion/introduction) and the [Chat API](https://platform.openai.com/docs/guides/chat).

## Source of truth

Although the AI is very capable out-of-the-box, if it doesn't know the answer to a question, it is more likely to confidently give incorrect information  than to refuse to answer. The plugin provides a text area at both the *block instance* level as well as the *plugin* level where teachers or administrators can include a list of questions and answers that the AI will ingest before generating a completion; as a result, the AI is more likely to provide accurate information when a submitted query is similar to the questions it has been given direct answers to. For example, an AI that hasn't been provided any extra information may respond to the query "What color is the car?" with a random color, such as red. However, if the following is included in the source of truth box:
```
Q: What color is the car?
A: The car is blue.
```
the AI will then respond to the question "What color is the car?" with the exact answer provided, "The car is blue." The AI will also still answer accurately if the question is asked in a different way; for example, a user might ask, "What color is the apple?" "What color is the forest?" and finally, "and what about the car?" The AI will correctly identify the apple, the forest, and the car as red, green, and blue, respectively.

## Prompt format

Although the Completion API and the Chat API differ slightly in how requests are made, the prompt format is generally the same between both of them: 
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

## Global block settings

The global block settings can be found by going to Site Administration > Plugins > Blocks > OpenAI Chat Block. The options are:
-  **Restrict chat usage to logged-in users:** If this box is checked, only logged-in users will be able to use the chat box.
-  **OpenAI API Key:** This is where you add the API key given to you by OpenAI
-  **Completion prompt:** Here you can edit the text added to the top of the conversation in order to influence the AI's persona and responses
-  **Assistant name:** This is the name that the AI will use for itself in the conversation
-  **User name:** This is the name that will be used for the User in the conversation. Both this and the above option can be used to influence the persona and responses of the AI.
-  **Source of truth:** Here you can add a list of questions and answers that the AI will use to accurately respond to queries. Anything added here in the SoT at the plugin level will be applied to every block instance on the site.
There is also an "Advanced" section that allows a user to fine-tune the AI's parameters. Please see OpenAI's documentation for more information on these options.

## Individual block settings

There are a few settings that can be changed on a per-block basis. You can access these settings by entering editing mode on your site and clicking the gear on the block, and then going to "Configure OpenAI Chat Block"

- **Block title:** The title for this block
- **Show labels:** Whether or not the names chosen for "Assistant name" and "User name" should appear in the chat UI
- **Source of Truth:** Here you can add a list of questions and answers that the AI will use to accurately respond to queries at the block instance level. Information provided here will only apply to this specific block.

Maintained by [Bryce Yoder](https://bryceyoder.com)
