<?php
header('Content-Type: text/html; charset=UTF-8');

// システム用ファイル（除外対象）
$systemFiles = ['index.php', 'quiz.php', 'result.php'];

// クイズデータを安全に読み込む関数
function loadQuizFile($filePath) {
    if (!file_exists($filePath)) return null;
    $quiz = null;
    $data = include $filePath;
    if (is_array($data) && isset($data['title'])) {
        return $data;
    }
    if (isset($quiz) && is_array($quiz) && isset($quiz['title'])) {
        return $quiz;
    }
    return null;
}

$quizzes = [];
$files = glob('*.php');

if ($files) {
    foreach ($files as $file) {
        if (in_array($file, $systemFiles, true)) {
            continue;
        }

        $key = pathinfo($file, PATHINFO_FILENAME);
        $quiz = loadQuizFile($file);
        if ($quiz !== null) {
            $quizzes[$key] = $quiz;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>社会科問題集</title>
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
      <h1 class="page-title">コースを選択</h1>
      <div class="tile-grid">
        <?php if (!empty($quizzes)): ?>
          <?php foreach ($quizzes as $key => $quiz): ?>
            <a href="quiz.php?id=<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" class="tile" style="background-color: <?= htmlspecialchars($quiz['color'] ?? '#0078D7', ENT_QUOTES, 'UTF-8') ?>;">
              <div class="tile-content">
                <h2><?= htmlspecialchars($quiz['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p><?= htmlspecialchars($quiz['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <span class="badge"><?= count($quiz['questions'] ?? []) ?> 問</span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: #ff6b6b;">クイズが見つかりませんでした。同ディレクトリ内に `chugoku_shikoku.php` 等のファイルがあるか確認してください。</p>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>