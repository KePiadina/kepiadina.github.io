<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File: quiz.php
$csvFile = 'sign_assets/quiz.csv';
$logFile = 'quiz_log.txt';

// Function to log messages
function logMessage($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

// Read the CSV file and parse it into an array
$quizData = [];
if (file_exists($csvFile) && ($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Ensure data is well-formed before adding to the quiz data array
        if (count($data) >= 3) {
            $quizData[] = [
                'risposta_corretta' => $data[0],
                'id' => $data[1],
                'categoria' => $data[2]
            ];
        }
    }
    fclose($handle);
} else {
    logMessage("Quiz file not found.");
    die("<div class='error'>Quiz file not found. Please check the file and try again.</div>");
}

// Check if quiz data is empty
if (empty($quizData)) {
    logMessage("No quiz data available.");
    die("<div class='error'>No quiz data available. Please check the CSV file and try again.</div>");
}

// Shuffle questions if not already shuffled (when first loading the page)
if (!isset($_SESSION['shuffled_quiz'])) {
    shuffle($quizData);
    $_SESSION['shuffled_quiz'] = $quizData;
    $_SESSION['current_question'] = 0; // Start with the first question
    $_SESSION['score'] = 0;            // Initialize score
} else {
    $quizData = $_SESSION['shuffled_quiz'];
}

// Function to generate wrong choices from the same category
function generateWrongChoices($correctAnswer, $quizData, $category, $numChoices = 3) {
    $wrongChoices = [];
    // Filter answers by category
    $categoryAnswers = array_filter($quizData, function($q) use ($category) {
        return $q['categoria'] === $category; // Use correct key
    });

    // Extract correct answers from the filtered category answers
    $categoryCorrectAnswers = array_column($categoryAnswers, 'risposta_corretta');

    // Randomly select wrong choices from the category
    while (count($wrongChoices) < $numChoices && !empty($categoryCorrectAnswers)) {
        $randomAnswer = $categoryCorrectAnswers[array_rand($categoryCorrectAnswers)];
        if ($randomAnswer !== $correctAnswer && !in_array($randomAnswer, $wrongChoices)) {
            $wrongChoices[] = $randomAnswer;
        }
    }
    return $wrongChoices;
}

// Check if the user has submitted the form
$submitted = isset($_POST['submit']);
$feedbackMessage = '';

if ($submitted) {
    // Retrieve the current question index
    $currentQuestionIndex = $_SESSION['current_question'];
    $questionData = $quizData[$currentQuestionIndex] ?? null;

    if ($questionData) {
        $correctAnswer = $questionData['risposta_corretta'];
        $userAnswer = $_POST["answer"] ?? '';

        // Check if the user's answer is correct
        if ($userAnswer === $correctAnswer) {
            $_SESSION['score']++;
            $feedbackMessage = "Risposta corretta";
        } else {
            $feedbackMessage = "Errato. La risposta corretta era: $correctAnswer";
        }

        logMessage("User answer for Question $currentQuestionIndex: $userAnswer");

        // Move to the next question
        $_SESSION['current_question']++;

        // Check if there are more questions
        $nextQuestionAvailable = $_SESSION['current_question'] < count($quizData);
    } else {
        $nextQuestionAvailable = false;
        $feedbackMessage = "Error loading question data.";
    }
} else {
    $nextQuestionAvailable = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz cartelli</title>
    <style>
        body {
            background: #333; 
            color: #eee; 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .quiz-container {
            width: 80%; 
            max-width: 600px;
            margin: 20px; 
            padding: 20px; 
            background: #444; 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        .question {
            font-size: 24px; 
            margin-top: 10px;
            margin-bottom: 20px; 
            font-weight: bold;
        }
        .question-block {
            margin-bottom: 20px;
        }
        .choices {
            margin: 20px 0; 
            text-align: left;
        }
        .choices label {
            display: block; 
            margin: 10px 0; 
            padding: 10px; 
            background: #555;
            border-radius: 5px; 
            cursor: pointer;
            transition: background 0.3s;
        }
        .choices label:hover {
            background: #666;
        }
        .choices input[type="radio"] {
            margin-right: 10px;
        }
        .image {
            margin-bottom: 20px;
        }
        .image img {
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            max-width: 100px;
            height: auto;
        }
        button[type="submit"] {
            background: #28a745;
            color: #fff; 
            border: none; 
            padding: 10px 20px; 
            font-size: 16px;
            cursor: pointer; 
            border-radius: 5px; 
            transition: background 0.3s;
        }
        button[type="submit"]:hover {
            background: #218838;
        }
        .feedback {
            margin: 20px 0;
            padding: 10px;
            background: #555;
            border-radius: 5px;
            color: #eee;
            font-weight: bold;
        }
        .error {
            color: red; 
            font-weight: bold; 
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <?php if (empty($quizData)): ?>
            <!-- Fallback error display if no questions are available -->
            <div class="error">Error: No quiz data available. Please check the CSV file and try again.</div>
        <?php elseif (!$nextQuestionAvailable): ?>
            <!-- Quiz results -->
            <h2>Quiz Completato!</h2>
            <p>Punteggio: <?= $_SESSION['score'] ?> / <?= count($quizData) ?></p>
            <a href="quiz.php">Riprova</a>
            <?php session_destroy(); ?>
        <?php else: ?>
            <!-- Display feedback message if available -->
            <?php if ($submitted): ?>
                <div class="feedback"><?= $feedbackMessage ?></div>
            <?php endif; ?>

            <!-- Single question form -->
            <?php
                $currentQuestionIndex = $_SESSION['current_question'];
                $questionData = $quizData[$currentQuestionIndex];
                $wrongChoices = generateWrongChoices($questionData['risposta_corretta'], $quizData, $questionData['categoria']);
                $choices = array_merge([$questionData['risposta_corretta']], $wrongChoices);
                shuffle($choices);
            ?>
            <h2>Cartello <?= $currentQuestionIndex + 1 ?></h2>
            <form method="POST">
                <div class="question-block">
                    <div class="image">
                        <img src="sign_assets/<?= $questionData['categoria'] . $questionData['id'] ?>.png" alt="Image" width="100">
                    </div>
                    <div class="choices">
                        <?php foreach ($choices as $choice): ?>
                            <label>
                                <input type="radio" name="answer" value="<?= $choice ?>" required>
                                <?= $choice ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" name="submit">Conferma</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
