<?php
// 文字化け防止ヘッダーの出力
header('Content-Type: text/html; charset=UTF-8');

// __DIR__ を使って絶対パスで quizzes フォルダ内の JSON を検索
$quizFiles = glob(__DIR__ . '/quizzes/*.json');
$quizzes = [];

if ($quizFiles !== false) {
    foreach ($quizFiles as $file) {
        $key = pathinfo($file, PATHINFO_FILENAME);
        $json = file_get_contents($file);
        $quizzes[$key] = json_decode($json, true);
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
                <h2><?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                <p><?= htmlspecialchars($quiz['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <span class="badge"><?= count($quiz['questions'] ?? []) ?> 問</span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: #ff6b6b;">クイズが見つかりませんでした。`quizzes` フォルダの中に JSON ファイルがあるか確認してください。</p>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>