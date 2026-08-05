<?php
header('Content-Type: text/html; charset=UTF-8');

$id = basename($_GET['id'] ?? '');
$filePath = __DIR__ . "/quizzes/{$id}.json";

if (!$id || !file_exists($filePath) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$quiz = json_decode(file_get_contents($filePath), true);
$userAnswers = $_POST['answers'] ?? [];

$score = 0;
$total = count($quiz['questions']);
$results = [];

foreach ($quiz['questions'] as $idx => $q) {
    $type = $q['type'] ?? 'radio';
    $userAns = $userAnswers[$idx] ?? '';

    if ($type === 'multi_text') {
        $isCorrect = true;
        $userAnsList = [];
        $correctAnsList = [];

        foreach ($q['inputs'] as $input) {
            $key = $input['key'];
            $uVal = trim($userAns[$key] ?? '');
            $cVal = trim($input['answer']);

            $userAnsList[] = "{$input['label']}: " . ($uVal !== '' ? $uVal : '未回答');
            $correctAnsList[] = "{$input['label']}: {$cVal}";

            if ($uVal !== $cVal) {
                $isCorrect = false;
            }
        }

        if ($isCorrect) $score++;

        $results[] = [
            'question' => $q['question'],
            'user_answer' => implode(' / ', $userAnsList),
            'correct_answer' => implode(' / ', $correctAnsList),
            'is_correct' => $isCorrect
        ];

    } elseif ($type === 'text') {
        $uVal = trim(is_array($userAns) ? '' : $userAns);
        $cVal = trim($q['answer']);
        $isCorrect = ($uVal === $cVal);

        if ($isCorrect) $score++;

        $results[] = [
            'question' => $q['question'],
            'user_answer' => $uVal !== '' ? $uVal : '未回答',
            'correct_answer' => $cVal,
            'is_correct' => $isCorrect
        ];

    } else {
        $uVal = is_array($userAns) ? '' : $userAns;
        $cVal = $q['answer'];
        $isCorrect = ($uVal === $cVal);

        if ($isCorrect) $score++;

        $results[] = [
            'question' => $q['question'],
            'user_answer' => $uVal !== '' ? $uVal : '未回答',
            'correct_answer' => $cVal,
            'is_correct' => $isCorrect
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>結果発表 - <?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="app-header">
    <div class="header-container">
      <a href="index.php" class="brand-title">社会科 QUIZ HUB</a>
    </div>
  </header>

  <main class="main-container">
    <section class="metro-section">
      <h1 class="page-title">結果発表</h1>

      <div class="score-card">
        <div class="score-label">あなたのスコア</div>
        <div class="score-num"><?= $score ?> / <?= $total ?></div>
      </div>

      <div class="results-list">
        <?php foreach ($results as $idx => $res): ?>
          <div class="metro-card <?= $res['is_correct'] ? 'correct-border' : 'wrong-border' ?>">
            <div class="result-header">
              <span class="q-num">Q<?= $idx + 1 ?></span>
              <?php if ($res['is_correct']): ?>
                <span class="badge badge-correct">正解 ◯</span>
              <?php else: ?>
                <span class="badge badge-wrong">不正解 ×</span>
              <?php endif; ?>
            </div>

            <p class="question-text"><?= nl2br(htmlspecialchars($res['question'], ENT_QUOTES, 'UTF-8')) ?></p>

            <div class="answer-comparison">
              <p>あなたの回答: <strong><?= htmlspecialchars($res['user_answer'], ENT_QUOTES, 'UTF-8') ?></strong></p>
              <?php if (!$res['is_correct']): ?>
                <p class="correct-text">正しい解答: <strong><?= htmlspecialchars($res['correct_answer'], ENT_QUOTES, 'UTF-8') ?></strong></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="form-actions btn-group-mobile">
        <a href="quiz.php?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" class="metro-btn secondary-btn">もう一度挑戦する</a>
        <a href="index.php" class="metro-btn primary-btn">トップへ戻る</a>
      </div>
    </section>
  </main>
</body>
</html>