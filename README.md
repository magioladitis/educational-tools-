# v3.20.10 header hotfix

Fixes a malformed, unclosed HTML comment introduced in v3.20.9 in five calculator pages. The malformed line caused browsers to treat the remainder of each document as an HTML comment, producing a blank white page even though PHP executed successfully.

Affected files:
- ypologismos-morion.php
- ypologismos-morion-1ea-2025.php
- ypologismos-morion-1gt-2024.php
- ypologismos-morion-2ea-2025.php
- ypologismos-morion-4ea-2025.php

No calculation/business logic or CSS content changed. Shared asset query strings are cache-busted to v3.20.10 in the complete production package.
