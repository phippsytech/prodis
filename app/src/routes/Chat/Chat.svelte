<script>
  import Container from "@shared/Container.svelte";

  //     const chatbox = document.getElementById("chatbox");
  // const chatContainer = document.getElementById("chat-container");
  // const userInput = document.getElementById("user-input");
  // const sendButton = document.getElementById("send-button");
  // const openChatButton = document.getElementById("open-chat");
  // const closeChatButton = document.getElementById("close-chat");

  // let isChatboxOpen = true; // Set the initial state to open

  // // Function to toggle the chatbox visibility
  // function toggleChatbox() {
  // chatContainer.classList.toggle("hidden");
  // isChatboxOpen = !isChatboxOpen; // Toggle the state
  // }

  // // // Add an event listener to the open chat button
  // // openChatButton.addEventListener("click", toggleChatbox);

  // // // Add an event listener to the close chat button
  // // closeChatButton.addEventListener("click", toggleChatbox);

  // // // Add an event listener to the send button
  // // sendButton.addEventListener("click", function () {
  // // const userMessage = userInput.value;
  // // if (userMessage.trim() !== "") {
  // //     addUserMessage(userMessage);
  // //     respondToUser(userMessage);
  // //     userInput.value = "";
  // // }
  // // });

  // // userInput.addEventListener("keyup", function (event) {
  // // if (event.key === "Enter") {
  // //     const userMessage = userInput.value;
  // //     addUserMessage(userMessage);
  // //     respondToUser(userMessage);
  // //     userInput.value = "";
  // // }
  // // });

  // function addUserMessage(message) {
  // const messageElement = document.createElement("div");
  // messageElement.classList.add("mb-2", "text-right");
  // messageElement.innerHTML = `<p class="bg-indigo-600 text-white rounded-lg py-2 px-4 inline-block">${message}</p>`;
  // chatbox.appendChild(messageElement);
  // chatbox.scrollTop = chatbox.scrollHeight;
  // }

  // function addBotMessage(message) {
  // const messageElement = document.createElement("div");
  // messageElement.classList.add("mb-2");
  // messageElement.innerHTML = `<p class="bg-indigo-50 text-slate-700 rounded-lg py-2 px-4 inline-block">${message}</p>`;
  // chatbox.appendChild(messageElement);
  // chatbox.scrollTop = chatbox.scrollHeight;
  // }

  // function respondToUser(userMessage) {
  // // Replace this with your chatbot logic
  // setTimeout(() => {
  //     addBotMessage("This is a response from the chatbot.");
  // }, 500);
  // }

  // Automatically open the chatbox on page load
  // toggleChatbox();
</script>

<Container>
  <h2 class="text-2xl text-indigo-900 font-fredoka-one-regular">Team Chat</h2>

  <div id="chatbox" class="p-4 h-80 overflow-y-auto">
    <!-- Chat messages will be displayed here -->
    <div class="mb-2 text-right">
      <p class="bg-indigo-600 text-white rounded-lg py-2 px-4 inline-block">
        hello
      </p>
    </div>
    <div class="mb-2">
      <p class="bg-indigo-50 text-slate-700 rounded-lg py-2 px-4 inline-block">
        This is a response from the chatbot.
      </p>
    </div>
    <div class="mb-2 text-right">
      <p class="bg-indigo-600 text-white rounded-lg py-2 px-4 inline-block">
        this example of chat
      </p>
    </div>
    <div class="mb-2">
      <p class="bg-indigo-50 text-slate-700 rounded-lg py-2 px-4 inline-block">
        This is a response from the chatbot.
      </p>
    </div>
    <div class="mb-2 text-right">
      <p class="bg-indigo-600 text-white rounded-lg py-2 px-4 inline-block">
        design with tailwind
      </p>
    </div>
    <div class="mb-2">
      <p class="bg-indigo-50 text-slate-700 rounded-lg py-2 px-4 inline-block">
        This is a response from the chatbot.
      </p>
    </div>
  </div>

  <div class="p-4 border-t border-indigo-50 flex">
    <input
      id="user-input"
      type="text"
      placeholder="Type a message"
      class="w-full px-3 py-2 border border-indigo-100 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-600"
    />
    <button
      id="send-button"
      class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-600 transition duration-300"
      >Send</button
    >
  </div>
</Container>
