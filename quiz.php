<?php
header('Content-Type: text/html; charset=UTF-8');

$id = basename($_GET['id'] ?? '');
$filePath = __DIR__ . "/quizzes/{$id}.xml";

if (!$id || !file_exists($filePath)) {
    header("Location: index.php");
    exit;
}

libxml_use_internal_errors(true);
$xml = simplexml_load_file($filePath);

if (!$xml) {
    echo "XMLファイルの読み込みに失敗しました。構文を確認してください。";
    exit;
}

$title = (string)($xml->title ?? '無題');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - 社会科問題集</title>
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
      <h1 class="page-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

      <form action="result.php?id=<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" method="POST" class="quiz-form">
        <?php foreach ($xml->questions->question as $idx => $q): ?>
          <?php
            $qType = (string)($q['type'] ?? 'radio');
            $qImage = (string)($q->image ?? '');
            $qText = (string)($q->text ?? '');
          ?>
          <div class="metro-card">
            <div class="question-number">Q<?= $idx + 1 ?></div>

            <?php if (!empty($qImage)): ?>
              <div class="question-image" style="margin-bottom: 16px;">
                <img src="images/<?= htmlspecialchars($qImage, ENT_QUOTES, 'UTF-8') ?>" alt="問題画像" style="max-width: 100%; height: auto; border: 1px solid #333;">
              </div>
            <?php endif; ?>

            <p class="question-text"><?= nl2br(htmlspecialchars($qText, ENT_QUOTES, 'UTF-8')) ?></p>

            <?php if ($qType === 'multi_text'): ?>
              <div class="multi-input-group">
                <?php foreach ($q->inputs->input as $input): ?>
                  <?php 
                    $iKey = (string)$input['key'];
                    $iLabel = (string)($input['label'] ?? $iKey);
                  ?>
                  <div class="input-row">
                    <label class="input-label"><?= htmlspecialchars($iLabel, ENT_QUOTES, 'UTF-8') ?>:</label>
                    <input type="text" name="answers[<?= $idx ?>][<?= htmlspecialchars($iKey, ENT_QUOTES, 'UTF-8') ?>]" class="metro-input" placeholder="回答を入力" required>
                  </div>
                <?php endforeach; ?>
              </div>

            <?php elseif ($qType === 'text'): ?>
              <div class="single-input-group">
                <input type="text" name="answers[<?= $idx ?>]" class="metro-input" placeholder="回答を入力してください" required>
              </div>

            <?php else: ?>
              <div class="options-grid">
                <?php foreach ($q->options->option as $opt): ?>
                  <?php $optVal = (string)$opt; ?>
                  <label class="option-tile">
                    <input type="radio" name="answers[<?= $idx ?>]" value="<?= htmlspecialchars($optVal, ENT_QUOTES, 'UTF-8') ?>" required>
                    <span class="tile-label"><?= htmlspecialchars($optVal, ENT_QUOTES, 'UTF-8') ?></span>
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