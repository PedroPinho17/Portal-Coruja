PDF.js (viewer) - installation notes

This folder is intended to contain a local copy of Mozilla's PDF.js web viewer.

Recommended steps to install (manual):

1) Download the official pdfjs distribution (pdfjs-dist) from GitHub releases:
   https://github.com/mozilla/pdf.js/releases

   Look for an archive named like `pdfjs-<version>-dist.zip` or use `pdfjs-dist` package from npm.

2) Extract the `web/` folder from the distribution into this folder as `web/`.
   After extraction you should have: `public/plugins/pdfjs/web/viewer.html` and related assets.

3) Usage in the app
   The viewer URL will be:
     /plugins/pdfjs/web/viewer.html?file=<URL_OF_PDF>

   Example: if you store PDFs under `storage/app/public/docs/example.pdf` and `Storage::url()` resolves to
   `https://your-site.test/storage/docs/example.pdf` then the viewer URL will be:
     /plugins/pdfjs/web/viewer.html?file=https%3A%2F%2Fyour-site.test%2Fstorage%2Fdocs%2Fexample.pdf

4) Notes & CORS
   - The viewer will fetch the PDF via XHR; make sure the URL you provide allows CORS requests if it's on a different domain.
   - If you serve PDFs through a Laravel route that returns a response (for permissions), ensure that route allows requests from the viewer (no blocking headers like X-Frame-Options denied).

5) Alternative: npm
   If you prefer npm, install `pdfjs-dist` and copy the `web` contents to `public/plugins/pdfjs/web` during your build step.

If you want, I can add an example Blade partial that renders an embedded viewer area (iframe) or a modal viewer and wire the click handler to open PDFs in that viewer.