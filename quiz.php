<?php
header('Content-Type: text/html; charset=UTF-8');

$id = basename($_GET['id'] ?? '');
$filePath = "{$id}.php";

$systemFiles = ['index.php', 'quiz.php', 'result.php'];

function loadQuizFile($filePath) {
    if (!file_exists($filePath)) return null;
    $quiz = null;
    $data = include $filePath;
    if (is_array($data) && isset($data['questions'])) {
        return $data;
    }
    if (isset($quiz) && is_array($quiz) && isset($quiz['questions'])) {
        return $quiz;
    }
    return null;
}

// $id のバリデーション強化（英数字・ハイフン・アンダースコアのみ許可）
if (!$id || !preg_match('/^[a-zA-Z0-9_-]+$/', $id) || in_array("{$id}.php", $systemFiles, true)) {
    header("Location: index.php");
    exit;
}

$quiz = loadQuizFile($filePath);
if ($quiz === null) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($quiz['title'], ENT_QUOTES, 'UTF-8') ?> - 社会科問題集</title>
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
      <div class="breadcrumb"><a href="index.php">← コース一覧に戻る</a></div>
      <h1 class="page-title"><?= htmlspecialchars($quiz['title'], ENT_QUOTES, 'UTF-8') ?></h1>

      <form action="result.php?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" method="POST" class="quiz-form">
        <?php foreach ($quiz['questions'] as $idx => $q): ?>
          <div class="metro-card">
            <div class="question-number">Q<?= $idx + 1 ?></div>

            <?php if (!empty($q['image'])): ?>
              <div class="question-image" style="margin-bottom: 16px;">
                <img src="images/<?= htmlspecialchars($q['image'], ENT_QUOTES, 'UTF-8') ?>" alt="問題画像" style="max-width: 100%; height: auto; border: 1px solid #333;">
              </div>
            <?php endif; ?>

            <p class="question-text"><?= nl2br(htmlspecialchars($q['question'], ENT_QUOTES, 'UTF-8')) ?></p>

            <?php $type = $q['type'] ?? 'radio'; ?>

            <?php if ($type === 'multi_text'): ?>
              <div class="multi-input-group">
                <?php foreach ($q['inputs'] as $input): ?>
                  <div class="input-row">
                    <label class="input-label"><?= htmlspecialchars($input['label'], ENT_QUOTES, 'UTF-8') ?>:</label>
                    <input type="text" name="answers[<?= $idx ?>][<?= htmlspecialchars($input['key'], ENT_QUOTES, 'UTF-8') ?>]" class="metro-input" placeholder="回答を入力" required>
                  </div>
                <?php endforeach; ?>
              </div>

            <?php elseif ($type === 'text'): ?>
              <div class="single-input-group">
                <input type="text" name="answers[<?= $idx ?>]" class="metro-input" placeholder="回答を入力してください" required>
              </div>

            <?php elseif ($type === 'checkbox'): ?>
              <div class="options-grid">
                <?php foreach ($q['options'] as $opt): ?>
                  <label class="option-tile">
                    <input type="checkbox" name="answers[<?= $idx ?>][]" value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>">
                    <span class="tile-label"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></span>
                  </label>
                <?php endforeach; ?>
              </div>

            <?php else: ?>
              <div class="options-grid">
                <?php foreach ($q['options'] as $opt): ?>
                  <label class="option-tile">
                    <input type="radio" name="answers[<?= $idx ?>]" value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>" required>
                    <span class="tile-label"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>

        <div class="form-actions">
          <button type="submit" class="metro-btn primary-btn">全問の答え合わせをする</button>
        </div>
      </form>
    </section>
  </main>
</body>
</html>