$(document).ready(function () {
    const messageList = $("#message-list");

    function addMessage(message) {
        const listItem = $("<li>").addClass("list-group-item").text(message);
        messageList.prepend(listItem);
    }

    function pollMessages() {
        // Fetch messages from your PHP script
        $.ajax({
            url: "fetch_messages.php", // Replace with the actual path to your PHP script
            type: "GET",
            success: function (data) {
                addMessage(data);
                setTimeout(pollMessages, 1000); // Poll every 1 second
            },
            error: function () {
                // Handle errors here
                console.error("Error fetching messages");
                setTimeout(pollMessages, 5000); // Retry after 5 seconds on error
            }
        });
    }

    // Start polling for messages
    pollMessages();
});
