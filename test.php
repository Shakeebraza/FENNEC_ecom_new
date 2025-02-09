<?php
// include_once('global.php');
// echo smtp_mailer('shakeebrazamuhammad@gmail.com', 'Subject', 'Body of the email');
$seoTitle             = $fun->getData('site_settings', 'value', 11);
$seoTitleEnabled      = $fun->getData('approval_parameters', 'seo_param_title', 1);

$seoDescription       = $fun->getData('site_settings', 'value', 12);
$seoDescriptionEnabled= $fun->getData('approval_parameters', 'seo_param_description', 1);

$seoKeywords          = $fun->getData('site_settings', 'value', 13);
$seoKeywordsEnabled   = $fun->getData('approval_parameters', 'seo_param_keyword', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- SEO Meta Tags -->
    <?php if (strtolower($seoTitleEnabled) === 'enabled'): ?>
    <title><?= htmlspecialchars($seoTitle) ?></title>
    <?php else: ?>
        <title>Fennec</title>
    <?php endif; ?>

    <?php if (strtolower($seoDescriptionEnabled) === 'enabled'): ?>
        <meta name="description" content="<?= htmlspecialchars($seoDescription) ?>">
    <?php endif; ?>

    <?php if (strtolower($seoKeywordsEnabled) === 'enabled'): ?>
        <meta name="keywords" content="<?= htmlspecialchars($seoKeywords) ?>">
    <?php endif; ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3145087323601863"
crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>

   This is the body of your page.

</body>
</html>
