#!/bin/sh

FILES=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$')

if [ -z "$FILES" ]; then
  echo "📁 No PHP files staged. Skipping PHPStan."
  exit 0
fi

echo "🔍 Running PHPStan on staged files..."

vendor/bin/phpstan analyse --no-progress $FILES

if [ $? -ne 0 ]; then
  echo "❌ PHPStan found issues. Commit aborted."
  exit 1
fi

echo "✅ PHPStan passed. Continuing commit."
exit 0
