# moodle-block_openai_chat

<img align="right" src="https://user-images.githubusercontent.com/33644013/162025197-52c34e24-66a8-46e7-ab95-b0f65268031b.png" />

### GPT-3 powered AI chat block for Moodle

This block allows your Moodle users to get 24/7 chat support via OpenAI's GPT-3 text-completion AI. The block offers multiple options for customizing the persona of the AI and the prompt it is given, in order to influence the text it outputs.

To get started, create an OpenAI account [here](https://openai.com/api/). **This plugin requries a commercial subscription via a paid OpenAI account. For more information on purchasing a subscription, please see the aforementioned link. Once a paid account is created, all you need to do is add the API key to the plugin settings.**

This plugin uses the Completion API, where a prompt is given and the AI is asked to guess what might come next in the sequence. To read more about this API, and for tips on optimizing your prompt to get the output that you want, please look [here](https://beta.openai.com/docs/guides/completion/introduction).

## Source of truth

Although the AI is very capable out-of-the-box, if it doesn't know the answer to a question, it is more likely to confidently give incorrect information  than to refuse to answer. The plugin provides a text area where administrators can include a list of questions and answers that the AI will ingest before generating a completion; as a result, the AI is more likely to provide accurate information when a submitted query is similar to the questions it has been given direct answers to. For example, an AI that hasn't been provided any extra information may respond to the query "What color is the car?" with a random color, such as red. However, if the following is included in the source of truth box:
```
Q: What color is the car?
A: The car is blue.
```
the AI will then respond to the question "What color is the car?" with the exact answer provided, "The car is blue." The AI will also still answer accurately if the question is asked in a different way; for example, a user might ask, "What color is the apple?" "What color is the forest?" and finally, "and what about the car?" The AI will correctly identify the apple, the forest, and the car as red, green, and blue, respectively.

## Prompt format

Knowing exactly the format of the text sent to OpenAI can help users tailor their prompt for best results. In this plugin, the entire conversation transcript is sent every time a user sends a message, so that the AI is able to remember previous parts of the conversation. The "prompt" is placed at the top of the text, and then a back-and-forth transcript is added for each message. For example, if the prompt is set to the default ("Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:"), the User's name is set to "User" and the AI's name is set to "Agent," the text sent to the AI might look like this:

```
Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:
User: Hello?
Agent: Hello, how can I help you?
User: Yes, I need help updating my email in Moodle.
Agent: 
```
The above is assuming that a few messages have already been exchanged between the user and the AI as transcribed above. As you can see, each time the AI is asked to respond, the prompt is given, followed by a transcript of the entire conversation so far, with each party clearly labeled. Note how the prompt ends with "Agent: ". This indicates to the AI that the text it generates must be a logical response from the Agent based on the existing conversation.

The user is able to customize the prompt added to the top of the transcript, as well as the names of the parties of the conversation.

If a source of truth is provided, it will also be prepended to the transcript, similar to the below:
```
Below is a list of questions and their answers:

Q: What color is the car?
A: The car is blue

Q: How do I upload an assignment?
A: In your course, click on the assignment. Click the Add submission button, and then upload your file.

=======================================

Below is a conversation between a user and a support agent for a Moodle site, where users go for online learning:
User: What color is the apple?
Agent: The apple is red.
User: What color is the forest?
Agent: The forest is green.
User: and what about the car?
Agent: The car is blue.
```

## Global block settings

The global block settings can be found by going to Site Administration > Plugins > Blocks > OpenAI Chat Block. The options are:
-  **Restrict chat usage to logged-in users:** If this box is checked, only logged-in users will be able to use the chat box.
-  **OpenAI API Key:** This is where you add the API key given to you by OpenAI
-  **Completion prompt:** Here you can edit the text added to the top of the conversation in order to influence the AI's persona and responses
-  **Agent name:** This is the name that the AI will use for itself in the conversation
-  **User name:** This is the name that will be used for the User in the conversation. Both this and the above option can be used to influence the persona and responses of the AI.
-  **Source of truth:** Here you can add a list of questions and answers that the AI will use to accurately respond to queries.

## Individual block settings

There are a few settings that can be changed on a per-block basis. You can access these settings by entering editing mode on your site and clicking the gear on the block, and then going to "Configure OpenAI Chat Block"

- **Block title:** The title for this block
- **Show labels:** Whether or not the names chosen for "Agent name" and "User name" should appear in the chat UI

Maintained by [Bryce Yoder](https://bryceyoder.com)
