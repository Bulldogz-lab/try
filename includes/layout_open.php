<?php
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title ?? 'PropSight') ?> — PropSight</title>
  <link rel="stylesheet" href="../../assets/css/admin-css/style.css"/>
  <link rel="icon" type="image/png" href="../../assets/images/final logo.png"/>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <div class="search-bar">
      <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      Search anything here
    </div>
  </div>
  <div class="topbar-divider"></div>
  <div class="content">
