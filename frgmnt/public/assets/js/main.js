/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */

window.addEventListener("DOMContentLoaded", () => {
  
  document.querySelectorAll('.page-link').forEach(link => {
     
    link.addEventListener('click', async (e) => {
      e.preventDefault();
      const id = link.dataset.id;
      const url = '/frgmnt/pages/edit?id=' + id;
      window.history.pushState(
        { pageId: id },
        '',
        '/frgmnt/pages/edit?id=' + id
      );


      const response = await fetch('/frgmnt/pages/edit?id=' + id, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      const html = await response.text();
      document.getElementById('editor-panel').innerHTML = html;
    });
  });
});
