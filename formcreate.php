<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Survey</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <a href="home.php" class="text-blue-500">← Back to Home</a>
    <h1>Create a New Survey</h1>

    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-xl font-bold mb-4">Create a Survey</h2>

        <form id="surveyForm">
            <label class="block mb-2">Survey Title:</label>
            <input type="text" id="title" required class="w-full p-2 border rounded mb-4">

            <label class="block mb-2">Description:</label>
            <textarea id="description" required class="w-full p-2 border rounded mb-4"></textarea>

            <label class="block mb-2">Questions:</label>
            <div id="questionsContainer" class="mb-4">
                <input type="text" class="question-input w-full p-2 border rounded mb-2" placeholder="Enter a question">
            </div>
            <button type="button" id="addQuestion" class="bg-blue-500 text-white p-2 rounded">+ Add Question</button>

            <button type="button" id="previewBtn" class="bg-yellow-400 text-black p-2 rounded mt-4 w-full">Preview Survey</button>
            <button type="submit" class="bg-green-500 text-white p-2 rounded mt-4 w-full">Create Survey</button>
        </form>

        <!-- Survey Preview Section -->
        <div id="surveyPreview" class="mt-6 p-4 border border-gray-300 rounded bg-gray-50 hidden">
            <h3 class="text-xl font-bold">Preview Survey</h3>
            <div id="previewTitle"></div>
            <div id="previewDescription"></div>
            <div id="previewQuestions"></div>
            <button type="button" id="editBtn" class="bg-blue-500 text-white rounded p-2 mt-4">Edit Survey</button>
        </div>

        <p id="message" class="text-center mt-4 text-red-500"></p>
    </div>

    <script>
        document.getElementById("addQuestion").addEventListener("click", function() {
            // Add new question input
            const questionInput = document.createElement("input");
            questionInput.type = "text";
            questionInput.classList.add("question-input", "w-full", "p-2", "border", "rounded", "mb-2");
            questionInput.placeholder = "Enter a question";
            document.getElementById("questionsContainer").appendChild(questionInput);
        });

        document.getElementById("previewBtn").addEventListener("click", function() {
            // Get the form values
            const title = document.getElementById("title").value;
            const description = document.getElementById("description").value;
            const questions = Array.from(document.querySelectorAll(".question-input"))
                                    .map(input => input.value)
                                    .filter(value => value.trim() !== ""); // Filter out empty questions

            // Display the preview
            document.getElementById("surveyPreview").classList.remove("hidden");

            // Populate preview content
            document.getElementById("previewTitle").innerHTML = `<strong>Title:</strong> ${title}`;
            document.getElementById("previewDescription").innerHTML = `<strong>Description:</strong> ${description}`;
            document.getElementById("previewQuestions").innerHTML = `<strong>Questions:</strong><ul>${questions.map(q => `<li>${q}</li>`).join('')}</ul>`;

            // Hide the form
            document.getElementById("surveyForm").classList.add("hidden");
        });

        document.getElementById("editBtn").addEventListener("click", function() {
            // Show the form again and hide the preview
            document.getElementById("surveyPreview").classList.add("hidden");
            document.getElementById("surveyForm").classList.remove("hidden");
        });

        // Handle the form submission
        document.getElementById("surveyForm").addEventListener("submit", function(e) {
            e.preventDefault(); // Prevent actual submission to demonstrate the functionality
            // You can handle form submission here (send the data to a PHP script or AJAX)
            alert("Survey Created!");
        });
    </script>
</body>
</html>
