<?php
session_start();
require_once 'dbConnect.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Survey</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4">Create Survey</h1>
        <form id="surveyForm">
            <div class="mb-4">
                <label class="block mb-2 font-semibold">Survey Title:</label>
                <input type="text" id="title" required class="w-full p-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-semibold">Description:</label>
                <textarea id="description" required class="w-full p-2 border rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-semibold">Questions:</label>
                <div id="questionsContainer" class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" class="question-input flex-1 p-2 border rounded" placeholder="Enter question">
                        <button type="button" class="remove-question bg-red-500 text-white p-2 rounded">×</button>
                    </div>
                </div>
                <button type="button" id="addQuestion" class="bg-blue-500 text-white p-2 rounded mt-2">
                    + Add Question
                </button>
            </div>

            <div class="flex space-x-4">
                <button type="button" id="previewBtn" class="bg-yellow-400 text-black p-2 rounded flex-1">
                    Preview Survey
                </button>
                <button type="submit" class="bg-green-500 text-white p-2 rounded flex-1">
                    Create Survey
                </button>
            </div>
        </form>

        <div id="surveyPreview" class="mt-6 p-4 border border-gray-300 rounded bg-gray-50 hidden">
            <h3 class="text-xl font-bold mb-2">Preview Survey</h3>
            <div id="previewTitle" class="mb-2"></div>
            <div id="previewDescription" class="mb-4"></div>
            <div id="previewQuestions" class="mb-4"></div>
            <button type="button" id="editBtn" class="bg-blue-500 text-white p-2 rounded w-full">
                Edit Survey
            </button>
        </div>

        <div id="message" class="mt-4 text-center"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add question
            document.getElementById('addQuestion').addEventListener('click', function() {
                const container = document.getElementById('questionsContainer');
                const div = document.createElement('div');
                div.className = 'flex gap-2';
                div.innerHTML = `
                    <input type="text" class="question-input flex-1 p-2 border rounded" placeholder="Enter question">
                    <button type="button" class="remove-question bg-red-500 text-white p-2 rounded">×</button>
                `;
                container.appendChild(div);
            });

            // Remove question
            document.getElementById('questionsContainer').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-question')) {
                    if (document.querySelectorAll('.question-input').length > 1) {
                        e.target.parentElement.remove();
                    } else {
                        alert('A survey must have at least one question');
                    }
                }
            });

            // Preview survey
            document.getElementById('previewBtn').addEventListener('click', function() {
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                const questions = Array.from(document.querySelectorAll('.question-input'))
                                     .map(input => input.value.trim())
                                     .filter(q => q !== '');

                if (!title) {
                    alert('Survey title is required');
                    return;
                }

                if (questions.length === 0) {
                    alert('Please add at least one question');
                    return;
                }

                document.getElementById('previewTitle').textContent = title;
                document.getElementById('previewDescription').textContent = description || 'No description';
                
                const previewQuestions = document.getElementById('previewQuestions');
                previewQuestions.innerHTML = '<h4 class="font-semibold mb-2">Questions:</h4>';
                questions.forEach((q, i) => {
                    previewQuestions.innerHTML += `<div class="mb-2">${i+1}. ${q}</div>`;
                });

                document.getElementById('surveyForm').classList.add('hidden');
                document.getElementById('surveyPreview').classList.remove('hidden');
            });

            // Edit survey
            document.getElementById('editBtn').addEventListener('click', function() {
                document.getElementById('surveyPreview').classList.add('hidden');
                document.getElementById('surveyForm').classList.remove('hidden');
            });

            // Form submission
            document.getElementById('surveyForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                const questions = Array.from(document.querySelectorAll('.question-input'))
                                     .map(input => input.value.trim())
                                     .filter(q => q !== '');

                if (!title) {
                    alert('Survey title is required');
                    document.getElementById('title').focus();
                    return;
                }

                if (questions.length === 0) {
                    alert('Please add at least one question');
                    document.querySelector('.question-input').focus();
                    return;
                }

                const formData = {
                    title: title,
                    questions: questions,
                    user_id: <?= $_SESSION['user']['id'] ?? 0 ?>
                };
                
                if (description) formData.description = description;

                try {
                    const response = await fetch('store_survey.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });

                    const result = await response.json();
                    
                    if (!response.ok) throw new Error(result.error || "Failed to create survey");
                    
                    alert(`Survey created with ${questions.length} questions!`);
                    window.location.href = `take_survey.php?survey_id=${result.survey_id}`;
                    
                } catch (error) {
                    console.error("Submission error:", error);
                    alert("Error: " + error.message);
                }
            });
        });
    </script>
</body>
</html>