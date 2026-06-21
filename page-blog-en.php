<?php
/**
 * English blog index — Polylang wouldn't allow reusing slug `blog`
 * across languages here, so this page is slug `blog-en` instead. Rather
 * than a filesystem symlink to page-blog.php (risky across a git
 * push/pull deploy — not all hosting/sync setups preserve symlinks),
 * this just requires the real template directly. Single source of
 * truth: any future edit to page-blog.php applies here automatically.
 *
 * File: page-blog-en.php
 */

require __DIR__ . '/page-blog.php';
