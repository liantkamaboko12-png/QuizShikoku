<?php
header('Content-Type: text/html; charset=UTF-8');

// quizzes/*.xml を検索
$quizFiles = glob(__DIR__ . '/quizzes/*.xml');
if ($quizFiles === false || empty($quizFiles)) {
    $quizFiles = glob('quizzes/*.xml');
}
$quizzes = [];

if ($quizFiles) {
    foreach ($quizFiles as $file) {
        $key = pathinfo($file, PATHINFO_FILENAME);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file);

        if ($xml !== false) {
            $quizzes[$key] = [
                'title' => (string)($xml->title ?? '無題'),
                'description' => (string)($xml->description ?? ''),
                'color' => (string)($xml->color ?? '#0078D7'),
                'questions_count' => count($xml->questions->question ?? [])
            ];
        } else {
            $quizzes[$key] = [
                'title' => 'XML構文エラー',
                'description' => 'XMLの解析に失敗しました。構文を確認してください。',
                'color' => '#E81123',
                'questions_count' => 0
            ];
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
            <a href="quiz.php?id=<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" class="tile" style="background-color: <?= htmlspecialchars($quiz['color'], ENT_QUOTES, 'UTF-8') ?>;">
              <div class="tile-content">
                <h2><?= htmlspecialchars($quiz['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p><?= htmlspecialchars($quiz['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <span class="badge"><?= $quiz['questions_count'] ?> 問</span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: #ff6b6b;">クイズが見つかりませんでした。`quizzes` フォルダ内に XML ファイルがあるか確認してください。</p>
        <?php endif; ?>
      </div>
    </section>
  </main>
</body>
</html>